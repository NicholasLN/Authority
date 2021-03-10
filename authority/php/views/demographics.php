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