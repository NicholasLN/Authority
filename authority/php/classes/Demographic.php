<?php


class Demographic
{
    public static function getDemographicMean(Array $demoDetails, String $type)
    {
        $ecoMean = 0;
        $socMean = 0;

        $demoGender = $demoDetails['Gender'];
        $demoRace = $demoDetails['Race'];

        if ($demoRace != "White") {
            $ecoMean += rand(0, -4) / 10;
            $socMean += rand(1, 4) / 10;
        }
        if ($demoRace == "White") {
            $ecoMean += rand(-2, 5) / 10;
        }
        if ($demoGender == "Female") {
            $socMean += rand(1, 3) / 10;
        }
        if ($demoGender == "Male") {
            $ecoMean += rand(-2, 5) / 10;
            $socMean += rand(-5, 5) / 10;
        }
        if ($demoGender == "Transgender/Nonbinary") {
            $ecoMean += rand(0, -4) / 10;
            $socMean += rand(0, 2) / 10;
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
}