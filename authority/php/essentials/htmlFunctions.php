<?php
    function redirect($url){
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    function alert($header,$msg){
        echo <<<ALERT
        <script>
            alertify.alert("$header", "$msg");
        </script>
ALERT;
    }
    function echoNoScript(){
        echo '<noscript>
                <style>html{display:none;}</style>
                <meta http-equiv="refresh" content="0; url=https://pastebin.com/LHAyJ7Zf"/>
            </noscript>';
    }
    function echoAlertify(){
        echo '
            <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
            <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
            <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
        ';
    }
    function echoJQuery(){
        echo '<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>';
    }
    function echoFA(){
        echo '<script src="https://kit.fontawesome.com/e317ab0c61.js" crossorigin="anonymous"></script>';
    }
    function echoStyles(){
        echo '<link href="css/main.css?id=84" rel="stylesheet">';
    }
    function echoBootstrap(){
        // STYLE
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" 
              integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">';
        // JavaScript
        echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" 
              integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>';
    }
    function echoHeader(){
    // Use this function to include bootstrap, etc.
        // bootstrap cdns
        echoNoScript();
        echoAlertify();
        echo '<meta name="viewport" content="width=device-width, initial-scale=0.74">';
        echoJQuery();
        echoBootstrap();
        echoFA();
        echoStyles();

    }
    function echoFooter(){
        global $loggedInRow;
        echo "
        <div class='footerBar'>
          <p>Developed by Phil Scott<br/>This is a WIP game.</p>
        </div>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js' integrity='sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1' crossorigin='anonymous'></script>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js' integrity='sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM' crossorigin='anonymous'></script>
        ";
        if($_SESSION['loggedIn']){
        echo "
        <div class='footerInformation'>     
            <p>
                <b>AUTHORITY:</b> ".$loggedInRow['authority']." <b>||</b>
                <b>CF: $</b><span class='greenFont'>".number_format($loggedInRow['campaignFinance'])."</span>
            </p> 
        </div>
        ";


        }
    }

    function lastOnlineString($time): string
    {
        $currentTime = time();
        $mins = getMinDifference($currentTime, $time);
        $hours = round(getHourDifference($currentTime, $time));
        $days = getDayDifference($currentTime,$time);
        $lastOnline="";
        switch($mins){
            // user was on within the last hour
            case $mins<60:
                if($mins >= 10) {
                    $lastOnline =  "Last Seen " . round($mins) . " minutes ago.";}
                if($mins < 10){
                    $lastOnline =  "Online Now";
                }
                break;
            // user was on within the last day
            case ($mins >= 60) && ($mins <= 1440):
                if($hours == 1){
                    $lastOnline =  "Last Seen 1 Hour Ago";
                }
                if($hours > 1){
                    $lastOnline =  "Last Seen ".round($hours)." Hours Ago";
                }
                break;
            // user has been offline for more than a day (Regina)
            case $mins>=1440:
                if($days == 1){
                    $lastOnline =  "Last Seen 1 Day Ago";
                }
                if($days > 1){
                    $lastOnline =  "Last Seen " . round($days,1)." Days Ago";
                }
                break;
            default:
                $lastOnline =  "";
        }
        return $lastOnline;
    }

    if(isset($_POST['logout'])){
        logout();
    }


