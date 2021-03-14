<?
class DemographicApproval
{
    public static function getPositionDifferenceApproval($positionValue, $demographicValue){
        $difference = abs($positionValue-$demographicValue); 
        //   limit (100)
        $y = 100-($difference**2)/0.3;

        return $y;
    }

}
?>