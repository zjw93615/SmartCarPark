<?php
  include("assets/php/connection.php");
  $key = 'AIzaSyBafCANRIdz41dwRNf_GnNVCN4Mbeg3uCw';
  if(isset($_POST['address']) && isset($_POST['lat'])&&isset($_POST['lng'])){

    $json = file_get_contents("https://maps.googleapis.com/maps/api/place/nearbysearch/json?keyword=".$_POST['address']."&location=".$_POST['lat'].",".$_POST['lng']."&radius=500&types=food&name=cruise&key=".$key);
    $obj=json_decode($json,true);
    $results = $obj['results'];
    foreach ($results as $value) {
      $geomestry = $value['geomestry'];
      $location = $geomestry['location'];
      $lat = $location['lat'];
      $lng = $location['lng'];
      $name = $value['name'];

    }
    $myJSON = json_encode($myObj);
    echo $result;
  }
?>