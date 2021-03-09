<?

function charts($demographicsArray){
    ?>
    <h5>Social Position Distribution</h5>
    <div class="chart" id="socialPositionsChart"></div>
    <h5>Economic Position Distribution</h5>
    <div class="chart" id="economicPositionsChart"></div>
    <script>

            var chart = new CanvasJS.Chart("socialPositionsChart", {
                animationEnabled: false,
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
                animationEnabled: false,
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