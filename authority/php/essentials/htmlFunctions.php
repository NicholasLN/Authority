<?php
function alert($header,$msg,$icon="error",$changeGet=false){
    ?>
    <script>
        Swal.fire({
            icon:"<? echo $icon ?>",
            title:"<? echo $header ?>",
            text:"<? echo $msg ?>",
            footer: 'Any questions? Feel free to join the <b><a style="margin-left:4px;margin-right: 4px" href="https://discord.gg/FdPu2gx"> discord </a></b> and ask me. '
        });
        <?
        if($changeGet){
        ?>
        var queryParams = new URLSearchParams(window.location.search);
        queryParams.set("noAlert", "true");
        history.replaceState(null, null, "?"+queryParams.toString());
        <?
        }
        ?>
    </script>


    <?
}
function redirect($url, $header="",$msg="", $icon="success",$s="&"){
    if($header!="") {
        $url .= $s . "alertHeader=$header&alertMsg=$msg&alertIcon=$icon&noAlert=false";
    }
    ?>
    <script type="text/javascript">
        window.location.href='<? echo $url; ?>'
    </script>
    <noscript>
        <meta http-equiv="refresh" content="0;url=<? echo $url?>"/>
    </noscript>
    <?

}
function echoNoScript(){
    echo '<noscript>
                <style>html{display:none;}</style>
                <meta http-equiv="refresh" content="0; url=https://pastebin.com/LHAyJ7Zf"/>
            </noscript>';
}
function echoSwal(){
    ?>
    <link rel="stylesheet" href="@sweetalert2/theme-borderless/borderless.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <?
}
function echoJQuery(){
    echo '<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>';
}
function echoFA(){
    echo '<script src="https://kit.fontawesome.com/e317ab0c61.js" crossorigin="anonymous"></script>';
}
function echoStyles(){
    echo '<link href="css/main.css?id=104" rel="stylesheet">';
}
function echoFavIcon(){
    echo '<link rel="icon" type="image/png" href="images/AuthorityLogoSMALL.png">';
}
function echoBootstrap(){
    ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <?
}
function echoHeader(){
    // Use this function to include bootstrap, etc.
    // bootstrap cdns
    echoNoScript();
    echoSwal();
    echo '<meta name="viewport" content="width=device-width, initial-scale=0.68">';
    echoJQuery();
    echoBootstrap();
    echoFA();
    echoFavIcon();
    echoStyles();
    ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <?

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
    if($_SESSION['loggedIn']) {
        echo "
            <div class='footerInformation'>     
                <p>
                    <b>AUTHORITY:</b> " . $loggedInRow['authority'] . " <b>||</b>
                    <b>CF: $</b><span class='greenFont'>" . number_format($loggedInRow['campaignFinance']) . "</span>
                </p> 
            </div>
            ";
    }
    // Alert Redirect Handling
    if (isset($_GET['alertHeader']) && isset($_GET['alertMsg'])) {
        if(isset($_GET['noAlert']) && $_GET['noAlert'] != "true") {
            if (isset($_GET['alertIcon'])) {
                alert($_GET['alertHeader'], $_GET['alertMsg'], $_GET['alertIcon'], true);
            } else {
                alert($_GET['alertHeader'], $_GET['alertMsg'], "error", true);
            }
        }

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

function partyRoleSearchAjax($partyID, $loggedInID){
    ?>
    <select id="selUser" name='partySearch' style="width: 100%">

    </select>
    <script>
        $(document).ready(function(){
            $("#selUser").select2({
                placeholder:"Occupant",
                dropdownAutoWidth : true,
                ajax: {
                    url: "php/ajax/partyRoleUserSearch.php",
                    type: "post",
                    dataType: 'json',
                    delay: 150,
                    data: function (params) {
                        return {
                            searchTerm: params.term, // search term,
                            partyID: '<? echo $partyID ?>',
                            loggedInID: '<? echo $loggedInID?>'
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
    <?
}

function partySearchAjax($partyID, $loggedInID){
    ?>
    <select id="selUser" name='partySearch' style="width: 100%;margin-top:3px;">

    </select>
    <script>
        $(document).ready(function(){
            $("#selUser").select2({
                placeholder:"Member",
                dropdownAutoWidth : true,
                ajax: {
                    url: "php/ajax/partyUserSearch.php",
                    type: "post",
                    dataType: 'json',
                    delay: 150,
                    data: function (params) {
                        return {
                            searchTerm: params.term, // search term,
                            partyID: '<? echo $partyID ?>',
                            loggedInID: '<? echo $loggedInID?>'
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
    <?
}



if(isset($_POST['logout'])){
    logout();
}