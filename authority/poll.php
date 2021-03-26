<?php include 'php/functions.php'; ?>
<?php
    if(isset($_GET['id'])){
        if(isset($loggedIn)){
            $pollID = numFilter($_GET['id']);
            $stmt = $db->prepare("SELECT * FROM demographicPolls WHERE poll_id = ?");
            $stmt->bind_param("i",$pollID);
            $stmt->execute();

            $result = $stmt->get_result();
            $rows = $result->num_rows;
            $poll = $result->fetch_array(MYSQLI_ASSOC);

            if($rows != 0){
                if($poll['user_id'] == $loggedInID || $loggedInUser->getVariable("admin") == 1) {
                    /** @var Poll $poll */
                    $poll = decompressObject($poll['poll_compressed'], "Poll");
                }
                else {
                    invalidPage("Error!", "Not your poll, shithead.");
                }
            }
            else{
                invalidPage("Error!","Not a poll.");
            }
        }
        else{
            invalidPage("No! You are not logged in. Leave!","LEAVE.");
        }
    }
    else{
        invalidPage("No poll ID provided!","Read the title, asshat!");
    }

?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Authority</title>
        <? echoHeader(); ?>
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
                    <br/>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col">
                                    <h2>Who You Polled</h2>
                                    <? demographicTable($poll->demographicArray) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col">
                                    <h2>Poll Data</h2>
                                    <table class="table table-bordered">
                                        <thead class="dark">
                                            <tr>
                                                <th>Variable</th>
                                                <th>Data</th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>Margin Of Error (+ or - in either direction)</td>
                                            <td>±<?=$poll->marginOfError?>%</td>
                                        </tr>
                                        <tr>
                                            <td>Mean Approval</td>
                                            <td><?=round($poll->mean,2)?>%</td>
                                        </tr>
                                        <tr>
                                            <td>Sample Size</td>
                                            <td><?=number_format($poll->sampleSize)?></td>
                                        </tr>
                                        <tr>
                                            <td>Confidence Level (Z-Score)</td>
                                            <td><?=$poll->confidenceLevel?> (<?=round($poll->z_score($poll->confidenceLevel),3)?>)</td>
                                        </tr>
                                        <tr>
                                            <td>Standard Deviation (measure of variability)</td>
                                            <td><?=round($poll->standardDeviation,2)?></td>
                                        </tr>


                                    </table>

                                    <h2>Poll Question/Respondents</h2>
                                    <table class="table table-bordered">
                                        <thead class="dark">
                                            <tr>
                                                <th>Answer</th>
                                                <th>Respondents</th>
                                                <th>Percent</th>
                                                <th>Margin of Error</th>
                                            </tr>
                                        </thead>
                                        <?
                                            foreach($poll->questionArray as $answer=>$respondents){
                                                $percent = $respondents/$poll->sampleSize;
                                                ?>
                                                    <tr>
                                                        <td><?=$answer?></td>
                                                        <td><?=$respondents?></td>
                                                        <td><?=$percent*100?>%</td>
                                                        <td>±<?=round($poll->marginOfError($percent)*100,2)?>%</td>
                                                    </tr>
                                                <?
                                            }
                                        ?>


                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                </div>
                <div class="col-sm"></div>
            </div>
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
        <? echoFooter() ?>
    </div>
    </html>

<?php
