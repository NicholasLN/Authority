<?php include 'php/functions.php';
if (isset($_GET['state'])) {
    $stateAbbrv = $_GET['state'];
    $state = new State($_GET['state']);

    if ($state -> doesItExist) {
        if(!isset($_GET['gender'])){ $_GET['gender'] = "all"; }
        if(!isset($_GET['race'])){ $_GET['race'] = "all"; }

        $gender = $_GET['gender'];
        $race = $_GET['race'];


        if(Demographic::validGender($gender) && Demographic::validRace($race)){
            $stateDemographics = $state -> getDemographics($gender,$race);
        }
        else{
            if(Demographic::validGender($gender) && !Demographic::validRace($race)){
                $stateDemographics = $state->getDemographics($gender,"all");
            }
            else if(!Demographic::validGender($gender) && Demographic::validRace($race)){
                $stateDemographics = $state->getDemographics("all",$race);
            }
            else{
                $stateDemographics = $state->getDemographics();
            }
        }
    } else {
        invalidPage("Invalid State!", "State does not exist. Fuck off");
    }
} else {
    invalidPage("Invalid State!","Put in a state, dummy!");
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
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.23/r-2.2.7/datatables.min.js"></script>
    <script src='https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js'></script>

</head>
<? echoNavBar() ?>
<body>
<div class="main">
    <div class="gameContainer">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10">
                <h1 style="padding-top: 16px"><?=$state->stateInfoArray['name'] ?> Demographics</h1>
                <a href="state.php?state=<?= $state -> stateAbbr?>" class="btn btn-primary ">State Politics</a>
                <hr>
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
                                                <option value="All" <?=Demographic::rig("All",$race)?>>All Races</option>
                                                <option value="White" <?=Demographic::rig("White",$race)?>>White</option>
                                                <option value="Black" <?=Demographic::rig("Black",$race)?>>Black</option>
                                                <option value="Hispanic" <?=Demographic::rig("Hispanic",$race)?>>Hispanic</option>
                                                <option value="Asian" <?=Demographic::rig("Asian",$race)?>>Asian</option>
                                                <option value="Native American" <?=Demographic::rig("Native American",$race)?>>Native American</option>
                                                <option value="Pacific Islander" <?=Demographic::rig("Pacific Islander",$race)?>>Pacific Islander</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name='genderSelect' class="form-control">
                                                <option value="All" <?=Demographic::gig("All",$gender)?>>All Genders</option>
                                                <option value="Male" <?=Demographic::gig("Male",$gender)?>>Male</option>
                                                <option value="Female" <?=Demographic::gig("Female",$gender)?>>Female</option>
                                                <option value="Transgender/Nonbinary" <?=Demographic::gig("Transgender/Nonbinary",$gender)?>>Transgender/Nonbinary</option>
                                            </select>
                                        </td>    
                                        <td>
                                            <input type='submit' class='btn btn-primary' value='Search Demographics' name='searchDemos'/>
                                        </td>                                             
                                    </tr>
                                </table>
                            </form>
                        </div>
                        <hr/>
                        <div class='row justify-content-center'>
                            <h5>Demographic Table</h5>
                            <table class='table table-striped' id="demographicTable" style="text-align: left">
                                <thead class='dark'>
                                    <tr>
                                        <th style="width:20%">Race</th>
                                        <th style="width:20%">Gender</th>
                                        <th>Population</th>
                                        <th style="width:20%">Turnout</th>
                                    </tr>
                                </thead>
                                <?
                                    foreach($stateDemographics as $demographic){
                                    ?>
                                    <tr>
                                        <td><?=$demographic['Race']?></td>
                                        <td><?=$demographic['Gender']?></td>
                                        <td><?=number_format($demographic['Population']);?>
                                        <td>100%</td>
                                    </tr>
                                    <?
                                    }
                                ?>
                            </table>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <? charts($stateDemographics) ?>
                    </div>
                </div>
                <div class='row justify-content-center'>
                    <div class='col-sm-6'>
                    <? genderChart($stateDemographics); ?>
                    </div>
                    <div class='col-sm-6'>
                    <? raceChart($stateDemographics); ?>
                    </div>
                </div>
            </div>

            <div class="col-sm"></div>
        </div>
        <script>
        $('#demographicTable').DataTable({
            "responsive":true,
            "order": [[2, "desc"]],
        });
        </script>
    </div>
    <? echoFooter() ?>
</div>
</html>

<? 
if(isset($_POST['searchDemos'])){
    $gender = $_POST['genderSelect'];
    $race = $_POST['raceSelect'];
    redirect("demographics.php?state=$stateAbbrv&gender=$gender&race=$race");
}