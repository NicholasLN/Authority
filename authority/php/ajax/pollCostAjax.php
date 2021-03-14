<?
include './ajaxWrapper.php';


//print_r($_POST);
$stateAbbrv = $_POST['state'];
$state = new State($stateAbbrv);

$gender = $_POST['gender'];
$race = $_POST['race'];


$demographicArray = $state->getDemographics($gender,$race);
$confidence = $_POST['confidence'];
$pop = str_replace(",","",$_POST['sample']);

if($pop > Demographic::demoSetPopulation($demographicArray)){ $pop = Demographic::demoSetPopulation($demographicArray); }
if($confidence > 99) { $confidence = 99; }

$cost = Demographic::pollCost($demographicArray,$confidence,$pop);
echo number_format($cost);
?>