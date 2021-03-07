<?php

function selectColor($colors, $x){
    $r = 0.0; $g = 0.0; $b = 0.0;
    $total = 0.0;
    $step = -1/(count($colors));
    $mu = 0.0;
    $sigma_2 = 0.4;

    foreach($colors as $color){
        $total += exp(-($x - $mu) * ($x - $mu) / (2.0 * $sigma_2)) / sqrt(2.0 * pi() * $sigma_2);
        $mu += $step;
    }
    $mu = 0.0;
    foreach($colors as $color){
        $percent = exp((-($x - $mu) * ($x - $mu)) / (2.0 * $sigma_2)) / sqrt(2.0 * pi() * $sigma_2);
        $mu+=$step;

        $r += ($color["r"] * $percent) /$total;
        $g += ($color['g'] * $percent) /$total;
        $b += ($color['b'] * $percent) /$total;
    }
    return "rgb($r,$g,$b)";

}
function getPositionFontColor($pos)
{
    $red = array("r" => 139, "g" => 0, "b" => 0);
    $lightred = array("r"=>255,"g"=>0,"b"=>0);
    $grey = array("r" => 0, "g" => 0, "b" => 0);
    $white = array("r" => 255, "g" => 255, "b" => 255);
    $lightblue = array("r"=>0,"g"=>0,"b"=>255);
    $blue = array("r" => 0, "g" => 0, "b" => 139);

    $gradientColors = array($red,$lightred,$grey,$grey,$lightblue,$blue);
    $rgb = selectColor($gradientColors, $pos);

    if($pos < 0.1 && $pos > -0.1){
        return "rgb(0,0,0)";
    }
    else {
        return $rgb;
    }

}
function getEcoPositionName($position){
    $str = "";

    switch(true){
        case $position <= -10:
            $str = "<u><i>P̴͔̤̻̟͖̳͇̮̱̑̿͑̂̉͛̅̓̆́͘͘ö̷̧̯̱͚͕̪̗͎͓̠͙͕̗̙̜͈͆̊́̽͊́̐̊̕͝͠s̵̹̹̬̖͒̽͛̔͌͊͐̃͂̒̌͘̕̕â̵̧̨̡͉̹̹̯͓̱̤̦̭͎̠͙̲͑͑̊̿̕͘͜͝͝d̵̡̛̯̻̑͗͋̈́̕͘͠͠i̸͕̭̗͖̺͓͙͍͈̘͊̃̋̆͌̏̕͜s̵̨̝̝͖͖͌͐̐̃̇m̵̘̩̳͉͙͎̲̯͖͓̳̫̹̬̥̯̭̝̾̓́̒͑̐̋̋̉̀͌͛̄̚͝</i></u>";
            break;
        case $position <= -5 && $position< -4.5:
            $str = "Collectivism";
            break;
        case ($position >= -4.5) && ($position <= -4):
            $str = "Socialism";
            break;
        case ($position > -4) && ($position <= -3):
            $str = "Left Wing";
            break;
        case ($position > -3) && ($position <= -2):
            $str = "Somewhat Left Wing";
            break;
        case ($position > -2) && ($position <= -1):
            $str = "Slightly Left Wing";
            break;
        case ($position > -1) && ($position < -0.1):
            $str = "Center Left";
            break;
        case $position >= -0.1 && $position <= 0.1:
            $str = "Mixed Capitalism";
            break;
        case ($position > 0.1) && ($position < 1):
            $str = "Center Right";
            break;
        case ($position >= 1) && ($position < 2):
            $str = "Slightly Right Wing";
            break;
        case ($position >= 2) && ($position < 3):
            $str = "Somewhat Right Wing";
            break;
        case ($position >= 3) && ($position < 4):
            $str = "Right Wing";
            break;
        case ($position >= 4) && ($position < 4.5):
            $str = "Capitalism";
            break;
        case $position >= 4.5 && $position < 10:
            $str = "Libertarianism";
            break;
        case $position >= 10:
            $str = "<i><u>S̷̢̡͓̩͉̦͎̝̟̬͚̉̎͌̿́͆̓͊͂̕͜L̶̹̠̹͚̀͐͊̂̆́̒̍͂̒̎̃͜͝Ä̶͖͚̝͕̻́͒̒̿̏͋̈͐̆̅̎̕͝V̴͕͌̓̓̿̃̄̏͒̈́̿͘͝Ę̷̹͍͚̞̾̓͆̋̓̔̑̈́̀͆̆̈͑̈́̅ ̶̯͈̓̄̂̓̆̄̋͂̂Ľ̵̡͈͔̭̲̹̗͈̺̘̳̪̭̭͆́͆̀Ã̸̖͚̳̖͉͇̯͖̬̟̼̊͑͋̐̿̾̍̇͑͜͝͝B̸̫̝̞͔̰̯̰̅͒̋̊̉̃̊͘O̸͓̹̭̼͈͚̠̿͐̀͒̒̀̈̇͐̌̍͆̉͘R̵̨̧̺̮̜̭̠̤̖̽̽̒̈́̀ͅ</u></i>";
            break;
    }
    return $str;
}
function getSocPositionName($position){
    $str = "";
    switch(true){

        case $position <= -5 && $position< -4.5:
            $str = "Anarchism";
            break;
        case ($position >= -4.5) && ($position <= -4):
            $str = "Communalism";
            break;
        case ($position > -4) && ($position <= -3):
            $str = "Left Wing";
            break;
        case ($position > -3) && ($position <= -2):
            $str = "Somewhat Left Wing";
            break;
        case ($position > -2) && ($position <= -1):
            $str = "Slightly Left Wing";
            break;
        case ($position > -1) && ($position < -0.1):
            $str = "Center Left";
            break;
        case $position >= -0.1 && $position <= 0.1:
            $str = "Centrist";
            break;
        case ($position > 0.1) && ($position < 1):
            $str = "Center Right";
            break;
        case ($position >= 1) && ($position < 2):
            $str = "Slightly Right Wing";
            break;
        case ($position >= 2) && ($position < 3):
            $str = "Somewhat Right Wing";
            break;
        case ($position >= 3) && ($position < 4):
            $str = "Right Wing";
            break;
        case ($position >= 4) && ($position < 4.5):
            $str = "Authoritarian Right";
            break;
        case $position >= 4.5 && $position < 10:
            $str = "Totalitarian Right";
            break;
        case $position >= 10:
            $str = "<i><u>P̷̧̛̘̅̄̆̓͋̐̊̏͝͝Ú̷̝̼͚͙͍̣͇͚͎̯̠̲̩̖̙̪̩̉͠R̷̨̘̳̽̍̈́G̸̡͇͉͈̲̮̼͍̬̩̪̰̖͇̳̺͚̼͙̻̤͐̓̑͋͑̋̊́͌̾́͝Ę̶̛̛͈̟̪̭̱͖͉̻̤̖͓̟̣͔͔̣͋͌̍̇̔̎̒͗͂̌̉̃̐̆̐̕ ̵̨̢̫̫͉̼̠̰̝͇͎͍̞̯̉͒͂̐̐͌̎T̵͆̎̄̈́͛̆̋̏̊̈́̋͊͂͜͠͠͝H̴̥̦̋̚È̴͈͕̭̼̪̬̻̗̯̟̦̜̥͕͎͙̻̔͐̀͑͂̐͒̀͆̆̓̅̉̂̌̈́̐͘̕͜ ̴̡̢̝͚̰̱͔͍̝̘͎̫̟̲́̐͐̊͒͑͒̄͊͐̆̊́̒̚͜W̴̡̡̹̼̰̥̻͚̭͚̟̠̞̺̝̎͊̒̒̒͒̂̌̏̑̃̄͒̕̕͝O̵̰͓̳̲̤͚̤͈͓̱͇͔̰̐͋̽͊͝ͅŖ̵̦̺͉̗͈̞͕̟͙͔̩͎͙͙̺͙͍̈́͑͂̉͐͌͑̂͐̉ͅL̵̡̺̳̭̩̳̒̉̏͌̐͛͊̈́̍͐̽͛͐̀͊͗͠Ḓ̶̨̨̙͎͚̮̘̲̺͙̹͇̜̄̈́͜</u></i>";
            break;
    }
    return $str;
}

function ecoPositionString($position){
    echo "<p style='color:".getPositionFontColor($position)."'>".getEcoPositionName($position)."</p>";
}
function socPositionString($position){
    echo "<p style='color:".getPositionFontColor($position)."'>".getSocPositionName($position)."</p>";
}