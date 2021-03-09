<?php


class Demographic
{
    public static function getDemographicMean(Array $demoDetails, String $type)
    {
        $ecoMean = 0;
        $socMean = 0;

        $demoGender = $demoDetails['Gender'];
        $demoRace = $demoDetails['Race'];

        if($demoDetails['State'] == "CA"){
            $ecoMean += nrandAverage(50, -0.3, 1, 5, -5);
            $socMean += nrandAverage(50,0.2,1,5,-5);
        }
        if($demoDetails['State'] == "TX"){
            $ecoMean += nrandAverage(50, 0, 1, 5, -5);
            $socMean += nrandAverage(50,-0.3,1,5,-5);
        }
        if ($type == "economic") {
            return $ecoMean;
        } else {
            return $socMean;
        }
    }
    public static function validRace(String $demoRace): bool
    {
        return $demoRace == "all" || $demoRace=="White" || $demoRace == "Black" || $demoRace == "Hispanic" || $demoRace == "Native American" || $demoRace == "Pacific Islander";
    }
    public static function validGender(String $demoGender): bool
    {
        return $demoGender == "all" ||  $demoGender == "Male" || $demoGender == "Female" || $demoGender == "Transgender/Nonbinary";
    }
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
        $sumPopulation = 0;
        foreach($demographicsArray as $demo){
            $sumPopulation += $demo['Population'];
        }
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
}