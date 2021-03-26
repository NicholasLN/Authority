<?
class DemographicSupport
{
    public static function getPositionDifferenceApproval($positionValue, $demographicValue){
        $difference = abs($positionValue-$demographicValue); 
        //   limit (100)
        $y = round(75+log($difference+1, 0.975),2);
        if($y>=100){ $y = 75;}
        if($y<0){ $y = 0;}

        return max($y,0);
    }

    // This should be extended later on to incorporate several other static functions involving party and other variables.
    public static function getTotalApproval($demographicPositions, $loggedInUser): float{
        global $memcached;
        $userID = $loggedInUser->userID;


        $demographicSocial = $demographicPositions['social'];
        $demographicEco = $demographicPositions['economic'];

        $userSocial = $loggedInUser->socPos;
        $userEconomic = $loggedInUser->ecoPos;

        $positionApproval = self::getPositionDifferenceApproval($userSocial, $demographicSocial)
                            +
                            self::getPositionDifferenceApproval($userEconomic, $demographicEco)
                            / 2;
        if($positionApproval>=100){
            $positionApproval = 75;
        }

        return round($positionApproval,2);

    }

}
?>