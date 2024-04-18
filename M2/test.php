<?php
require(__DIR__ . "/partials/nav.php");

?>

<h2> Cars </h2>

<form>
    <div>
        <label for="make">Make</label>
        <input type="text" id="make" name="make" placeholder="Make">
        </div>
        <div>
        <label for="model">Model</label>
        <input type="text" id="model" name="model" placeholder="Model">
        </div>
        <div>
        <label for="year">Year</label>
        <input type="text" id="year" name="year" placeholder="Year">
        </div>

        <input type="submit" value="Submit">

    
</form>

<table>
    <tr>
        <th>Make</th>
        <th>Model</th>
        <th>Year</th>
    </tr>
    
    <?php

$db = getDB();
$stmt = $db->prepare("SELECT make, model, year from Cars");
try{
    $r = $stmt->execute();
    if ($r){
        $cars = $stmt->fetchALL(PDO::FETCH_ASSOC);
        foreach($cars as $car){
            echo '<tr>';

            echo '<td>' . $car['make'] . '</td>';
            echo '<td>' . $car['model'] . '</td>';
            echo '<td>' . $car['year'] . '</td>';

            echo '</tr>';
        }
    }
}catch (Exception $e) {
    echo var_dump($e);
}


//code to read the data off of the fields
if(isset($_GET["make"]) && isset($_GET["model"]) && isset($_GET["year"])){
    echo $_GET["make"];
    echo $_GET["model"];
    echo $_GET["year"];

$make = $_GET["make"];
$model = $_GET["model"];
$year = $_GET["year"];


        $db = getDB();
        $stmt = $db->prepare("insert into Cars (make, model, year) values (:make,:model,:year)");
        try {
            $stmt->execute([":make" => $make, ":model" => $model, ":year" => $year]);
            echo "Successfully updated!";
        } catch (Exception $e) {
            echo "There was a problem updating";
            "<pre>" . var_export($e, true) . "</pre>";
        }
    }



?>

</table>