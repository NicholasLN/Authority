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
                        <? echo $vote->ayes; ?>
                    </td>
                    <td>
                        <? echo $vote->nays; ?>
                    </td>
                    <td>
                        <?
                        if (!$vote->votingEnded) {
                            echo round($vote->getTimeLeft(), 1) . " hours left";
                            if ($vote->isDelayed) {
                                echo "<br><span class='redFont'>Delayed! (+12 hours)</span>";
                            }
                        } else {
                            if ($vote->hasPassed) {
                                echo "<span class='greenFont'>Passed!</span>";
                            } else {
                                echo "<span class='redFont'>Failed!</span>";
                            }
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

function proposePartyVoteView($partyID)
{
    global $loggedInUser;
    $party = new Party($partyID);
    ?>
    <form method="post">
        <table class="table table-striped">
            <thead class="dark">
            <tr>
                <th width="15%">Field</th>
                <th>Input</th>
            </tr>
            </thead>
            <tr>
                <td><b class="bold">Party Vote Name</b></td>
                <td>
                    <input class="form-control" type="input" placeholder="Vote Name" name="partyVoteName">
                </td>
            </tr>
            <tr>
                <td><b class="bold">Party Vote Action(s)</b></td>
                <td>
                    <select id='selector' class="form-control" name="proposePartyVoteType">
                        <option value="New Chair" default>New Chair</option>
                        <option value="Rename Role">Rename Role</option>
                        <option value="Grant Permission">Grant Permission to Role</option>
                        <option value="Remove Permission">Remove Permission from Role</option>
                        <option value="Delete Role">Delete Role</option>
                        <option value="Change Role Occupant">Change Role Occupant</option>
                        <option value="Change Fees">Change Party Fees</option>
                        <option value="Rename Party">Rename Party</option>
                        <option value="Change Number of Party Votes">Change # of Party Votes</option>
                    </select>
                    <div class='New Chair box' style="margin-top:8px;">
                        <? partySearchAjax($partyID, "selUser1", "newChairSearch"); ?>
                    </div>
                    <div class='Rename Role box' style="display:none">
                        <select class="form-control" name="renameRoleSelect">
                            <?
                            $party->echoRoleOptions("true");
                            ?>
                        </select>
                        <input class="form-control" type="input" placeholder="Rename Role To" name="renameRoleTo">
                    </div>
                    <div class="Grant Permission box" style="display:none">
                        <select class="form-control" name="grantPermissionRoleSelect">
                            <?
                            $party->echoRoleOptions();
                            ?>
                        </select>
                        <select class="form-control" name="grantPermissionSelect">
                            <?
                            roleOptions();
                            ?>
                        </select>
                    </div>
                    <div class="Remove Permission box" style="display:none">
                        <select class="form-control" name="removePermissionRoleSelect">
                            <?
                            $party->echoRoleOptions();
                            ?>
                        </select>
                        <select class="form-control" name="removePermissionSelect">
                            <?
                            roleOptions();
                            ?>
                        </select>
                    </div>
                    <div class="Delete Role box" style="display: none">
                        <select class="form-control" name="deleteRoleSelect">
                            <?
                            $party->echoRoleOptions();
                            ?>
                        </select>
                    </div>
                    <div class="Change Role Occupant box" style="display: none;margin-top:8px ">
                        <? partySearchAjax($partyID, "selUser2", "changeOccupantSearch"); ?>
                        <select class="form-control" name="changeOccupantSelect">
                            <?
                            $party->echoRoleOptions(false);
                            ?>
                        </select>
                    </div>
                    <div class="Rename Party box" style="display: none">
                        <input class="form-control" type="input" placeholder="Rename Party To" name="renamePartyTo"/>
                    </div>
                    <?
                    if ($loggedInUser->getVariable("party") == $partyID && $loggedInUser->hasPartyPerm("proposeFees")) { ?>
                        <div class="Change Fees box" style="display: none">
                            <input class="form-control" type="number" placeholder="Change Fees To (0-100%)"
                                   name="changeFeesTo"/>
                        </div>
                    <? } ?>
                    <div class="Change Number of Party Votes box" style="display: none">
                        <input class="form-control" type="number" placeholder="Change Party Votes (5-1000)"
                               name="changePartyVotesTo"/>
                    </div>
                </td>
            </tr>
            <tr>
                <td><b class="bold">Propose Vote</b></td>
                <td><input class="btn btn-primary" type="submit" value="Propose Vote" name="proposeVoteSubmit"></td>
            </tr>
        </table>
    </form>
    <script>
        $(document).ready(function () {
            $("#selector").change(function () {
                var selectOption = $(this).val();
                let selectOptionStr = "." + selectOption.replaceAll(" ", ".");
                console.log(selectOptionStr);
                $('.box').not(selectOptionStr).hide()
                $(selectOptionStr).show();
            });
        })
    </script>
    <script>
        $(document).ready(function () {
            $("#selUser").select2({
                placeholder: "Member",
                dropdownAutoWidth: true,
                ajax: {
                    url: "php/ajax/partyUserSearch.php",
                    type: "post",
                    dataType: 'json',
                    delay: 150,
                    data: function (params) {
                        return {
                            searchTerm: params.term, // search term,
                            partyID: '6'
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