<?php


class Demographic
{
    /**
     * @param array $demoDetails
     * @param String $type
     * @return float
     */

    public static function isMinority(array $demoDetails){
        $demoRace = $demoDetails['Race'];
        return $demoRace == "Hispanic" || $demoRace == "Black" || $demoRace == "Pacific Islander" || $demoRace == "Native American";
    }
    public static function getDemographicMean(Array $demoDetails, String $type): float
    {
        $ecoMean = 0;
        $socMean = 0;

        $demoGender = $demoDetails['Gender'];
        $demoRace = $demoDetails['Race'];
        $demoState = $demoDetails['State'];

        if(!self::isMinority($demoDetails)){
            if($demoState === "CA"){
                $ecoMean += nrandAverage(-0.4, 4);
                $socMean += nrandAverage(0.43, 2);
            }
            else if($demoState === "VT"){
                $ecoMean += nrandAverage(-0.7,3);
            }
            else if($demoState === "TX"){
                $ecoMean += nrandAverage(0.9, 2);
                $socMean += nrandAverage(-0.32,2);
            }
            else{
                $ecoMean += nrandAverage(0.45, 2.2);
                $socMean += nrandAverage(0, 3.4);
            }
        }
        else{
            $ecoMean += nrandAverage(-0.8, 2);
            $socMean += nrandAverage(0.7, 2);
        }


        if ($type == "economic") {
            return round($ecoMean,2);
        } else {
            return round($socMean,2);
        }
    }

    /**
     * @param String $demoRace
     * @return bool
     */
    public static function validRace(String $demoRace): bool
    {
        return $demoRace == "all" || $demoRace=="White" || $demoRace == "Black" || $demoRace == "Hispanic" || $demoRace == "Native American" || $demoRace == "Pacific Islander" || $demoRace == "Asian";
    }

    /**
     * @param String $demoGender
     * @return bool
     */
    public static function validGender(String $demoGender): bool
    {
        return $demoGender == "all" ||  $demoGender == "Male" || $demoGender == "Female" || $demoGender == "Transgender/Nonbinary";
    }

    /**
     * @param array $demographicsArray
     * @return int
     */
    public static function demoSetPopulation(Array $demographicsArray): int
    {
        $sumPopulation = 0;
        foreach($demographicsArray as $demo){
            $sumPopulation += $demo['Population'];
        }
        return $sumPopulation;
    }

    /**
     * @param array $demographicsArray
     * @param String $EconomicOrSocial
     * @param bool $parseForChart
     * @return false|int[]|string
     */
    public static function generatePoliticalLeanings(Array $demographicsArray, String $EconomicOrSocial, bool $parseForChart=True){
        global $db;
        $politicalLeaningsArray = array(
            -5=>0,
            -4=>0,
            -3=>0,
            -2=>0,
            -1=>0,
            0=>0,
            1=>0,
            2=>0,
            3=>0,
            4=>0,
            5=>0
        );
        $sumPopulation = Demographic::demoSetPopulation($demographicsArray);
        foreach($demographicsArray as $demo){

            // Pull Positions array for each demographic in list
            $stmt = $db->prepare("SELECT * FROM demoPositions WHERE demoID = ? AND type = ?");
            $stmt->bind_param("is",$demo['demoID'], $EconomicOrSocial);
            $stmt->execute();
            $assoc = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);

            // For each position from -5 to 5, create an average of population holding those leanings based on total demographic population.
            for($i=-5;$i<=5;$i++){
                $politicalLeaningsArray[$i] += ($assoc[$i]*$demo['Population'])/$sumPopulation;
            }
        }
        
        // Condition for returning data in the required format for a chart.
        if($parseForChart){
            $chartArray = array();
            foreach($politicalLeaningsArray as $positionInterval=>$share){
                // y = leaning share * total population. label is required for chart, and share is the share of the population which is that ideology.
                $subArray = array("y"=>round($share/100*$sumPopulation), "label"=>getPositionName($EconomicOrSocial, $positionInterval), "share"=>$share, "color"=>getPositionFontColor($positionInterval,True));
                array_push($chartArray,$subArray,);
            }
            $json = json_encode($chartArray);
            return $json;            

        }
        // Return leanings (as % of population);
        return $politicalLeaningsArray;


    }

    public static function grabPoliticalShare(int $demoID, string $ecoOrSoc){
        global $db;
        $query = "SELECT `-5`,`-4`,`-3`,`-2`,`-1`,`0`,`1`,`2`,`3`,`4`,`5` FROM demoPositions WHERE demoID = $demoID AND type = '$ecoOrSoc'";
        $result = $db->query($query);
        return $result->fetch_array(MYSQLI_ASSOC);


    }

    /**
     * @param array $demographicsArray
     * @param bool $parseForChart
     * @return false|int[]|string
     */
    public static function generateGenderShare(Array $demographicsArray, bool $parseForChart=True){
        global $db;
        $genderArray = array(
            "Male"=>0,
            "Female"=>0,
            "Transgender/Nonbinary"=>0
        );
        $associatedColors = array(
            "Male"=>"#99ace0",
            "Female"=>"#ff748c",
            "Transgender/Nonbinary"=>"#CA93CA"
        );
        $sumPopulation = Demographic::demoSetPopulation($demographicsArray);
        foreach($demographicsArray as $demo){
            foreach($genderArray as $key=>&$value){
                if($key == $demo['Gender']){
                    $value += $demo['Population'];
                }
            }
        }
        
        // Condition for returning data in the required format for a chart.
        if($parseForChart){
            $chartArray = array();
            foreach($genderArray as $gender=>$population){
                if($population>0){
                    $subArray = array("y"=>round($population), "label"=>$gender, "share"=>$population/$sumPopulation, "color"=>$associatedColors[$gender]);
                    array_push($chartArray,$subArray);
                }
            }
            $json = json_encode($chartArray);
            return $json;            

        }
        return $genderArray;


    }

    /**
     * @param array $demographicsArray
     * @param bool $parseForChart
     * @return false|int[]|string
     */
    public static function generateRaceShare(Array $demographicsArray, bool $parseForChart=True){
        global $db;
        $raceArray = array(
            "White"=>0,
            "Black"=>0,
            "Hispanic"=>0,
            "Native American"=>0,
            "Pacific Islander"=>0,
            "Asian"=>0
        );
        $associatedColors = array(
            "White"=>"darkgrey",
            "Black"=>"grey",
            "Hispanic"=>"blue",
            "Native American"=>"#ff6500",
            "Pacific Islander"=>"orange",
            "Asian"=>"green"
        );
        $sumPopulation = Demographic::demoSetPopulation($demographicsArray);
        foreach($demographicsArray as $demo){
            foreach($raceArray as $key=>&$value){
                if($key == $demo['Race']){
                    $value += $demo['Population'];
                }
            }
        }
        
        // Condition for returning data in the required format for a chart.
        if($parseForChart){
            $chartArray = array();
            foreach($raceArray as $race=>$population){
                if($population>0){
                    $subArray = array("y"=>round($population), "label"=>$race, "share"=>$population/$sumPopulation, "color"=>$associatedColors[$race]);
                    array_push($chartArray,$subArray);
                }
            }
            $json = json_encode($chartArray);
            return $json;            

        }
        return $raceArray;


    }

    /**
     * @param array $demographicsArray
     * @param $confidenceLevel
     * @param $populationSize
     * @return Exception|float|int
     */
    public static function pollCost(Array $demographicsArray, $confidenceLevel, $populationSize){
        $baseCost = 50;
        try{
            $confidenceLevel = numfilter((float)str_replace("%", "", $confidenceLevel));
            $populationSize = numfilter((float)str_replace(",", "", $populationSize));
            $costPerPerson = round($confidenceLevel/100 * $baseCost,2);


            return $populationSize * $costPerPerson;
        }
        catch(Exception $e){
            return $e;
        }
    }


    /**
     * @param array $demographicsArray
     * @return array
     * For turning the demographicsArray into a distribution (Black Females = 60% of array, White Men = 40%... etc.)
     */
    public static function demographicArrayPopShare(Array $demographicsArray){
        global $memcached;
        global $loggedInID;

        $memCacheString = md5("$loggedInID DemoPopShare");
        $getCacheDetail = $memcached->get($memCacheString);
        if($getCacheDetail) {
            $popArray = $getCacheDetail;
            return $popArray;
        }

        $sum = 0;
        $popArray = array();
        foreach ($demographicsArray as $demographic) {
            $subArray = array($demographic['demoID'] => array("popShare" => 0, "demoInformation" => $demographic));
            $popArray += $subArray;
            $sum += $demographic['Population'];
        }
        foreach ($demographicsArray as $demographic) {
            $share = $demographic['Population'] / $sum * 100;
            $popArray[$demographic['demoID']]['popShare'] = $share;
        }
        $memcached->set($memCacheString, $popArray);
        return $popArray;
    }


    // rig = raceIsGET, gig = genderIsGET. Simplified condition for echoing "selected" in dropdown.
    /**
     * @param $demoRace
     * @param $get
     * @return string
     */
    public static function rig($demoRace, $get)
    {
        $get = ucwords($get);
        if($get==$demoRace){
            return "selected";
        }
    }

    /**
     * @param $demoRace
     * @param $get
     * @return string
     */
    public static function gig($demoRace, $get)
    {
        $get = ucwords($get);
        if($get==$demoRace){
            return "selected";
        }
    }
}