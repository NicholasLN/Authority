<?php


class Poll
{

    public array $demographicArray;
    private User $user;
    public int $sampleSize;
    public int $confidenceLevel;

    // Only for QUESTION POLL. //
    public array $questionArray;
    /////////////////////////////

    public float $marginOfError;
    public float $mean;
    public float $standardDeviation;
    public float $variance;


    /**
     * @param $z
     * @return float
     *
     * Function used for calculating prob. given confidence interval.
     */
    public function poz($z): float
    {
        if ($z === 0) {
            $x = 0.0;
        } else {
            $y = 0.5 * abs($z);
            if ($y > (6 * 0.5)) {
                $x = 1.0;
            } else if ($y < 1.0) {
                $w = $y * $y;
                $x = $x = ((((((((0.000124818987 * $w - 0.001075204047)
                                                * $w + 0.005198775019)
                                            * $w - 0.019198292004)
                                        * $w + 0.059054035642)
                                    * $w - 0.151968751364)
                                * $w + 0.319152932694)
                            * $w - 0.531923007300)
                        * $w + 0.797884560593)
                    * $y * 2.0;
            } else {
                $y -= 2.0;
                $x = (((((((((((((-0.000045255659 * $y
                                                                        + 0.000152529290)
                                                                    * $y - 0.000019538132)
                                                                * $y - 0.000676904986)
                                                            * $y + 0.001390604284)
                                                        * $y - 0.000794620820)
                                                    * $y - 0.002034254874)
                                                * $y + 0.006549791214)
                                            * $y - 0.010557625006)
                                        * $y + 0.011630447319)
                                    * $y - 0.009279453341)
                                * $y + 0.005353579108)
                            * $y - 0.002141268741)
                        * $y + 0.000535310849)
                    * $y + 0.999936657524;
            }
        }
        return $x;
    }

    /**
     * @param $p
     * @return float|int
     *
     * Function used for calculating z-score given confidence interval / 100.
     */
    public function z_score($p)
    {
        $p /= 100;
        $Z_EPSILON = 0.000001;
        $minZ = -6;
        $maxZ = 6;
        $zVal = 0.0;

        if ($p < 0.0 || $p > 1.0) {
            return -1;
        }

        while (($maxZ - $minZ) > $Z_EPSILON) {
            $pVal = $this->poz($zVal);
            if ($pVal > $p) {
                $maxZ = $zVal;
            } else {
                $minZ = $zVal;
            }
            $zVal = ($maxZ + $minZ) * 0.5;
        }
        return ($zVal);
    }

    /**
     * @param array $weightedPopArray
     * @return array
     * For grabbing random demographic based on pop share. (more likely to pick a pop that makes up 60% of pop compared to 10%)
     */
    public function grabWeightedRandomDemo(array $weightedPopArray): ?array
    {
        $rand = mt_rand(0, 100);
        $sum = 0;
        $selectedDemo = null;
        while($selectedDemo === null) {
            foreach ($weightedPopArray as $demo => $details) {
                $sum += $details['popShare'];
                if ($sum >= $rand) {
                    $selectedDemo = $details['demoInformation'];
                    break;
                }
            }
        }
        return $selectedDemo;
    }

    /**
     * @param array $positionShare
     * @return int
     * Grab weighted position from position share (as seen in demoPositions)
     */
    public function grabWeightedRandomPosition(array $positionShare): ?int
    {
        $rand = mt_rand(0, 100);
        $sum = 0;
        $selectedPosition = null;
        foreach ($positionShare as $position => $share) {
            $sum += $share;
            if ($sum >= $rand) {
                $selectedPosition = $position;
                break;
            }
        }
        return $selectedPosition;
    }


    /**
     * @param array $demographic
     * Poll someone random for their economic and social positions.
     * @return array
     */
    public function pollRandomDemographic(array $demographic): array
    {

        $socialShare = Demographic::grabPoliticalShare($demographic['demoID'], "social");
        $economicShare = Demographic::grabPoliticalShare($demographic['demoID'], "economic");

        return
            array(
                "social" => $this->grabWeightedRandomPosition($socialShare),
                "economic" => $this->grabWeightedRandomPosition($economicShare)
            );

    }

    private function updateStandardDeviation(): void
    {
        $this->standardDeviation = sqrt($this->variance / $this->sampleSize);
    }

    public function marginOfError(float $p): float{
        $z = $this->z_score($this->confidenceLevel);
        $pop = Demographic::demoSetPopulation($this->demographicArray);
        $sampleSize = $this->sampleSize;

        return $z * sqrt($p*(1-$p))/sqrt(($pop-1)*$sampleSize/($pop-$sampleSize));
    }

    private function updateMarginOfError(): void
    {
        $p = $this->mean/100;
        $moe = $this->marginOfError($p);

        $this->marginOfError = round($moe,4)*100;
    }

    public function approvalQuestionsAsPercent($questionArray){
        foreach($questionArray as $question=>&$questionValue){
            $questionValue /= $this->sampleSize;
        }
        return $questionArray;
    }
    public function populateQuestionArray($sampleArray): array
    {
        // Question: What do you think of candidate X
        $questionArray = array(
            "I strongly dislike them." => 0,
            "I dislike them" => 0,
            "They're okay." => 0,
            "I like them" => 0,
            "I like them very much" => 0
        );

        foreach ($sampleArray as $value) {
            $approval = $value[0];
            $this->variance += ($approval - $this->mean) ** 2;

            switch ($approval) {
                case $approval <= 15:
                    ++$questionArray['I strongly dislike them.'];
                    break;
                case $approval > 15 && $approval <= 40:
                    ++$questionArray['I dislike them'];
                    break;
                case $approval > 40 && $approval <= 60:
                    ++$questionArray['They\'re okay.'];
                    break;
                case($approval > 60 && $approval <= 85):
                    ++$questionArray['I like them'];
                    break;
                case($approval > 85):
                    ++$questionArray['I like them very much'];
                    break;
            }
        }

        return $questionArray;


    }


    /* One of two polls I plan to implement. One for approval, one for votes. */
    public function approvalPoll(): void
    {
        $sumApproval = 0;
        for ($i = 0; $i < $this->sampleSize; $i++) {
            /* grabs a random demographic by generating distribution of demographics and selecting a more common one.
                For example: The group you are polling is made up of 82% white people, and 18% black. You are probably more likely to poll a white person than a black person.
                That is what this function replicates.

                User can narrow this down by selecting a specific demographic to poll (instead of polling the whole state)
            */
            $pollingDemographics[] = $this->grabWeightedRandomDemo(Demographic::demographicArrayPopShare($this->demographicArray));
        }
        foreach ($pollingDemographics as $randomDemographic) {

            // then.. polls that demographic for it's positions!
            $pollDemographics = $this->pollRandomDemographic($randomDemographic);


            $approval = DemographicApproval::getTotalApproval($pollDemographics, $this->user);
            $sumApproval += $approval;

            $sampleArray[] = [$approval];
            ++$i;
        }
        $this->mean = $sumApproval / $this->sampleSize;
        $this->questionArray = $this->populateQuestionArray($sampleArray);
        $this->updateStandardDeviation();
        $this->updateMarginOfError();
    }


    public function __construct(array $demographicsArray, $confidenceLevel, $sampleSize, $loggedInUser)
    {
        $this->sampleSize = $sampleSize > 10000 ? 10000 : $sampleSize;

        if($confidenceLevel >= 100 ){ $confidenceLevel = 99.9; }
        if($confidenceLevel <= 0){ $confidenceLevel = 0.5; }
        $this->confidenceLevel = abs(round($confidenceLevel,6));

        $this->demographicArray = $demographicsArray;
        $this->confidenceLevel = $confidenceLevel;
        $this->user = $loggedInUser;

        $this->mean = 0;
        $this->variance = 0;
        $this->standardDeviation = 0;
        $this->marginOfError = 0;
    }

    public function addPollToDatabase(string $compressed){
        global $db;
        global $loggedInUser;

        $time = time();

        $stmt = $db->prepare("INSERT INTO demographicPolls (user_id, poll_compressed, date_held) VALUES ( ?, ?, ? )");
        $stmt->bind_param("isi", $loggedInUser->userID, $compressed, $time);
        $stmt->execute();

        return $stmt->insert_id;

    }
}