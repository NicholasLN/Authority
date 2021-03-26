<?

function charts($demographicsArray){
    ?>
    <h5>Social Position Distribution</h5>
    <div class="chart" id="socialPositionsChart"></div>
    <h5>Economic Position Distribution</h5>
    <div class='chart' id="economicPositionsChart"></div>
    <script>

            var chart = new CanvasJS.Chart("socialPositionsChart", {
                animationEnabled: true,
                axisY: {
                    title: "Population"
                },
                data: [{
                    type: "column",
                    startAngle: 0,
                    dataPoints: <?=Demographic::generatePoliticalLeanings($demographicsArray,"social") ?>
                }],

                backgroundColor: "transparent"
            });
            chart.render();
    </script>
    <script>

            var chart = new CanvasJS.Chart("economicPositionsChart", {
                animationEnabled: true,
                axisY: {
                    title: "Population"
                },
                data: [{
                    type: "column",
                    startAngle: 0,
                    dataPoints: <?=Demographic::generatePoliticalLeanings($demographicsArray,"economic") ?>
                }],

                backgroundColor: "transparent"
            });
            chart.render();
    </script>

    <?
}
function genderChart($demographicsArray){
    ?>
    <h5>Gender Distribution</h5>
    <div class='chart' id="genderDistrChart"></div>
    <script>

            var chart = new CanvasJS.Chart("genderDistrChart", {
                animationEnabled: true,
                data: [{
                    type: "pie",
                    startAngle: 0,
                    dataPoints: <?=Demographic::generateGenderShare($demographicsArray); ?>
                }],

                backgroundColor: "transparent"
            });
            chart.render();
    </script>
    <?
}
function raceChart($demographicsArray){
    ?>
    <h5>Race Distribution</h5>
    <div class='chart' id="raceDistrChart"></div>
    <script>

            var chart = new CanvasJS.Chart("raceDistrChart", {
                animationEnabled: true,
                data: [{
                    type: "pie",
                    startAngle: 0,
                    dataPoints: <?=Demographic::generateRaceShare($demographicsArray); ?>
                }],

                backgroundColor: "transparent"
            });
            chart.render();
    </script>
    <?
}
function demographicTable($stateDemographics){
    ?>

    <table class='table table-striped' id="demographicTable" style="text-align: left">
        <thead class='dark'>
        <tr>
            <th style="width:13%;">Race</th>
            <th style="width:20%;">Gender</th>
            <th>Population</th>
            <th>Turnout</th>
            <th>Economic Position</th>
            <th>Social Position</th>
        </tr>
        </thead>
        <?
        foreach ($stateDemographics as $demographic) {
            ?>
            <tr>
                <td><?=$demographic['Race'] ?></td>
                <td><?=$demographic['Gender'] ?></td>
                <td><?=number_format($demographic['Population']); ?>
                <td>100%</td>
                <td><?=ecoPositionString($demographic['EcoPosMean']) ?>
                    (<?= $demographic['EcoPosMean'] ?>)
                </td>
                <td><?=socPositionString($demographic['SocPosMean']) ?>
                    (<?= $demographic['SocPosMean'] ?>)
                </td>
            </tr>
            <?
        }
        ?>
    </table>
    <?php
}