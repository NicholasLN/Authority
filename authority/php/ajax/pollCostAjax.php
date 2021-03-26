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

if($pop > 10000){
    $pop = 10000;
}
if($pop === 0){
    $pop = 1;
}

if($confidence === 0){
    $confidence = 0.01;
}
if($confidence >= 99.999999){
    $confidence = 99.999999;
}

$cost = Demographic::pollCost($demographicArray,$confidence,$pop);
echo number_format($cost);
?>