<?php include 'php/functions.php';
if (isset($_GET['state'])) {
    $stateAbbrv = $_GET['state'];
    $state = new State($_GET['state']);

    if ($state->doesItExist) {
        if (!isset($_GET['gender'])) {
            $_GET['gender'] = "all";
        }
        if (!isset($_GET['race'])) {
            $_GET['race'] = "all";
        }

        $gender = $_GET['gender'];
        $race = $_GET['race'];


        if (Demographic::validGender($gender) && Demographic::validRace($race)) {
            $stateDemographics = $state->getDemographics($gender, $race);
        } else {
            if (Demographic::validGender($gender) && !Demographic::validRace($race)) {
                $stateDemographics = $state->getDemographics($gender, "all");
            } else if (!Demographic::validGender($gender) && Demographic::validRace($race)) {
                $stateDemographics = $state->getDemographics("all", $race);
            } else {
                $stateDemographics = $state->getDemographics();
            }
        }
    } else {
        invalidPage("Invalid State!", "State does not exist. Fuck off");
    }
} else {
    invalidPage("Invalid State!", "Put in a state, dummy!");
}
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8"/>
        <title>Authority</title>
        <? echoHeader(); ?>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
        <link rel="stylesheet" type="text/css"
              href="https://cdn.datatables.net/v/bs4/dt-1.10.23/b-1.6.5/datatables.min.css"/>
        <script type="text/javascript"
                src="https://cdn.datatables.net/v/dt/dt-1.10.23/r-2.2.7/datatables.min.js"></script>
        <script src='https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js'></script>

    </head>
    <? echoNavBar() ?>

    <body>
    <div class="main">
        <div class="gameContainer">
            <div class="row">
                <div class="col-sm"></div>
                <div class="col-sm-10">
                    <h1 style="padding-top: 16px"><?= $state->stateInfoArray['name'] ?> Demographics</h1>
                    <a href="state.php?state=<?= $state->stateAbbr ?>" class="btn btn-primary ">State Politics</a>
                    <hr>
                    <div class='row justify-content-center'>
                        <div class='col-md-6'>
                            <? genderChart($stateDemographics); ?>
                        </div>
                        <div class='col-md-6'>
                            <? raceChart($stateDemographics); ?>
                        </div>
                    </div>
                    <div class='row justify-content-center'>
                        <div class='col-md-8'>
                            <div class='row justify-content-center'>
                                <h5>Parameter Search</h5>
                                <form method='POST'>
                                    <table class='table table-striped'>
                                        <thead class='dark'>
                                        <tr>
                                            <th>Race</th>
                                            <th>Gender</th>
                                            <th>Submit</th>
                                        </tr>
                                        </thead>
                                        <tr>
                                            <td>
                                                <select name='raceSelect' class="form-control">
                                                    <option value="All" <?= Demographic::rig("All", $race) ?>>All
                                                        Races
                                                    </option>
                                                    <option value="White" <?= Demographic::rig("White", $race) ?>>
                                                        White
                                                    </option>
                                                    <option value="Black" <?= Demographic::rig("Black", $race) ?>>
                                                        Black
                                                    </option>
                                                    <option value="Hispanic" <?= Demographic::rig("Hispanic", $race) ?>>
                                                        Hispanic
                                                    </option>
                                                    <option value="Asian" <?= Demographic::rig("Asian", $race) ?>>
                                                        Asian
                                                    </option>
                                                    <option value="Native American" <?= Demographic::rig("Native American", $race) ?>>
                                                        Native American
                                                    </option>
                                                    <option value="Pacific Islander" <?= Demographic::rig("Pacific Islander", $race) ?>>
                                                        Pacific Islander
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name='genderSelect' class="form-control">
                                                    <option value="All" <?= Demographic::gig("All", $gender) ?>>All
                                                        Genders
                                                    </option>
                                                    <option value="Male" <?= Demographic::gig("Male", $gender) ?>>Male
                                                    </option>
                                                    <option value="Female" <?= Demographic::gig("Female", $gender) ?>>
                                                        Female
                                                    </option>
                                                    <option value="Transgender/Nonbinary" <?= Demographic::gig("Transgender/Nonbinary", $gender) ?>>
                                                        Transgender/Nonbinary
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type='submit' class='btn btn-primary' value='Search Demographics'
                                                       name='searchDemos'/>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                            <?
                            if ($loggedIn) {
                                ?>
                                <div class='row justify-content-center'>
                                    <form method='POST'>
                                        <table class='table table-striped'>
                                            <thead class='dark'>
                                            <tr>
                                                <th style='width:30%'>Confidence Level (%)</th>
                                                <th style='width:30%'>Sample Size</th>
                                                <th style='width:16%'>Submit</th>
                                            </tr>
                                            </thead>
                                            <tr>
                                                <td>
                                                    <input type='input'
                                                           class="form-control" aria-label="Confidence"
                                                           placeholder="95%" id='pollConfidence' name='pollConfidence'>
                                                </td>
                                                <td>
                                                    <input type='input' class='form-control'
                                                           placeholder="1 to 10,000" id='sampleSize' name='sampleSize'>
                                                </td>
                                                <td>
                                                    <input type="submit" class="btn btn-primary" value='Conduct Poll'
                                                           name='pollSubmit'/>
                                                    <br/>
                                                    <label id='pollCost'>
                                                        $<?= number_format(Demographic::pollCost($stateDemographics, 95, 1000)); ?>
                                                    </label>
                                                </td>
                                        </table>
                                    </form>
                                </div>
                                <?
                            }
                            ?>
                            <hr/>
                            <div class='row justify-content-center'>
                                <h5>Demographic Table</h5>
                                <? demographicTable($stateDemographics) ?>
                            </div>
                        </div>
                        <div class='col-md-4'>
                            <? charts($stateDemographics) ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm"></div>
            </div>

            <script>
                $('#demographicTable').DataTable({
                    "autoWidth": false,
                    "responsive": true,
                    "order": [
                        [2, "desc"]
                    ],
                });
            </script>
            <script>
                var countDecimals = function (value) {
                    if ((value % 1) != 0)
                        return value.toString().split(".")[1].length;
                    return 0;
                };
                $(document).ready(function() {
                    $('#pollConfidence').on('input', function postinput() {
                        var pollConfidence = $(this).val();
                        var decimalLength = countDecimals(pollConfidence);
                        if(decimalLength>6){
                            $('#pollConfidence').val(pollConfidence.toString().substring(0,pollConfidence.toString().length-1));
                        }
                        if(pollConfidence>=99.999999){
                            $('#pollConfidence').val(99.999999);
                        }
                        var sampleSize = $('#sampleSize').val();
                        if(pollConfidence == ""){
                            pollConfidence = 95;
                        }
                        if(sampleSize == ""){
                            sampleSize = 1000;
                        }
                        if(pollConfidence.includes("-")){
                            $('#pollConfidence').val(pollConfidence.replaceAll("-",""));
                            return null;
                        }
                        if(pollConfidence <= 0){
                            $('#pollConfidence').val(0.000001);
                        }
                        $.ajax({
                            type:'POST',
                            url: 'php/ajax/pollCostAjax.php',
                            data: {
                                state:'<?= $stateAbbrv ?>',
                                gender:'<?= $gender ?>',
                                race:'<?= $race ?>',
                                confidence:pollConfidence,
                                sample:sampleSize
                            }
                        }).done(function(responseData){
                            $('#pollCost').html("$"+responseData);


                        })

                    })
                })
            </script>

            <script>
                $(document).ready(function () {
                    $('#sampleSize').on('input', function postinput() {
                        var sampleSize = $(this).val();
                        if (sampleSize > 10000) {
                            $('#sampleSize').val("10000");
                        }
                        var pollConfidence = $('#pollConfidence').val();
                        if (pollConfidence == "") {
                            pollConfidence = 95;
                        }
                        if (sampleSize == "") {
                            sampleSize = 1000;
                        }
                        if (sampleSize.includes("-")) {
                            $('#sampleSize').val(sampleSize.replaceAll("-", ""));
                            return null;
                        }
                        if (sampleSize <= 0) {
                            $('#sampleSize').val(Math.abs(sampleSize));
                        }
                        $.ajax({
                            type: 'POST',
                            url: 'php/ajax/pollCostAjax.php',
                            data: {
                                state: '<?= $stateAbbrv ?>',
                                gender: '<?= $gender ?>',
                                race: '<?= $race ?>',
                                confidence: pollConfidence,
                                sample: sampleSize
                            }
                        }).done(function (responseData) {
                            $('#pollCost').html("$" + responseData);
                        })
                    })
                })
            </script>

        </div>
        <? echoFooter() ?>
    </div>

    </html>

<?
if (isset($_POST['searchDemos'])) {
    $gender = $_POST['genderSelect'];
    $race = $_POST['raceSelect'];
    redirect("demographics.php?state=$stateAbbrv&gender=$gender&race=$race");
}
if(isset($_POST['pollSubmit']) && $loggedIn){
    if(strlen($_POST['pollConfidence']) > 0){
        $confidenceLevel = numFilter($_POST['pollConfidence']);
    }
    else{
        $confidenceLevel = 95;
    }
    if(strlen($_POST['sampleSize']) > 0) {
        $sampleSize = numFilter($_POST['sampleSize']);
    }
    else{
        $sampleSize = 1000;
    }
    echo $confidenceLevel;
    echo $sampleSize;

    $poll = new Poll($stateDemographics, $confidenceLevel, $sampleSize, $loggedInUser);
    $poll->approvalPoll();

    $compressedPoll = compressObject($poll,9);
    $pollID = $poll->addPollToDatabase($compressedPoll);

    redirect("poll.php?id=$pollID");

}

//foreach($stateDemographics as $demographic){
//$poll = new Poll($stateDemographics, 95, 10000, $loggedInUser);
//$poll->approvalPoll();
