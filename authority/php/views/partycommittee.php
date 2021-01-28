<?php

function partyVotesTableView($partyID)
{
    $party = new Party($partyID);
    ?>
    <div class="table-responsive">
        <table class="table table-striped" id="partyVotesTable" style="text-align: left">
            <thead class="dark">
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Name
                </th>
                <th style="max-width:30%">
                    Action
                </th>
                <th>
                    Author
                </th>
                <th>
                    Ayes
                </th>
                <th>
                    Nays
                </th>
                <th>
                    Status
                </th>
            </tr>
            </thead>
            <?
            $voteArray = (object)$party->getActiveVotes();
            foreach ($voteArray as $vote) {
                $voteID = $vote->getBillID();
                ?>
                <tr>
                    <td><? echo $voteID ?></td>
                    <td>
                        <a href="partyvote.php?id=<? echo $voteID ?>">
                            <? echo $vote->getBillTitle() ?>
                        </a>
                    </td>
                    <td>
                        <div class="comment more"><? echo $vote->getBillRundown(true); ?></div>
                    </td>
                    <td>
                        <a href="politician.php?id=<? echo $vote->getAuthor()->pictureArray()['id'] ?>">
                            <img style="max-width:30px;max-height:30px"
                                 src="<? echo $vote->getAuthor()->pictureArray()["picture"] ?>"/>
                            <? echo $vote->getAuthor()->pictureArray()['name']; ?>
                        </a>
                    </td>
                    <td>
                        <? echo $vote->getAyes(); ?>
                    </td>
                    <td>
                        <? echo $vote->getNays(); ?>
                    </td>
                    <td>
                        <? echo round($vote->getTimeLeft(), 1) . " hours left";
                        if ($vote->isDelayed) {
                            echo "<br><span class='redFont'>Delayed! (+12 hours)</span>";
                        }
                        ?>
                    </td>
                </tr>


                <?
            }
            ?>
        </table>
    </div>
    <hr/>
    <h6>Party Vote Distribution</h6>
    <div class="chart" id="chartContainer" style=""></div>
    <script>
        window.onload = function () {

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: false,
                data: [{
                    type: "pie",
                    startAngle: 240,
                    dataPoints: <? echo $party->getCommitteeData() ?>
                }],

                backgroundColor: "transparent"
            });
            chart.render();
        }
    </script>
    <script>
        $(document).ready(function () {
            var showChar = 30;
            var ellipsestext = "...";
            var moretext = "more";
            var lesstext = "...less";
            $('.more').each(function () {
                var content = $(this).html();

                if (content.includes("<hr>")) {

                    var firstAction = content.split(/<hr>(.+)/)[0];
                    var restAction = content.split(/<hr>(.+)/)[1];

                    var html = firstAction + "<span class='moreellipses'>" +
                        ellipsestext + "" +
                        "</span>" +
                        "<span class='morecontent'><span class='content'><hr>" + restAction + "</span>" +
                        "<a href='#' class='morelink'>" + moretext + "</a></span>"


                    $(this).html(html);
                }

            });

            $(".morelink").click(function () {
                if ($(this).hasClass("less")) {
                    $(this).removeClass("less");
                    $(this).html(moretext);
                } else {
                    $(this).addClass("less");
                    $(this).html(lesstext);
                }
                $(this).parent().prev().toggle();
                $(this).prev().toggle();
                return false;
            });
        });

    </script>
    <script>
        $('#partyVotesTable').DataTable({
            "responsive": true,
            "order": [[0, "desc"]],
            "columnDefs": [
                {
                    "targets": 0,
                    "visible": false
                }
            ]
        });
    </script>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <?
}
