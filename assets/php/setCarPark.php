<?php
  include("connection.php");
  date_default_timezone_set('Australia/Melbourne');
  $result = array();
  if(isset($_POST['id'])&&isset($_POST['state'])) {
    if($_POST['state'] == "parked") {
      $q = "SELECT vacant FROM smartpark WHERE id = '$_POST[id]'";
      $r = mysqli_query($dbc, $q);
      $data = mysqli_fetch_assoc($r);
      if($data['vacant'] == 0) {
        $nowFormat = date('Y-m-d H:i:s');
        $q = "INSERT INTO parktime (carpark_id, start_time) VALUES('$_POST[id]', '$nowFormat')";
        $r = mysqli_query($dbc, $q);
        $q = "UPDATE smartpark SET vacant = '1' WHERE id = '$_POST[id]'";
        $r = mysqli_query($dbc, $q);
        echo "Success";
      }else {
        echo "Error";
      }
    }elseif($_POST['state'] == "available") {
      $q = "SELECT vacant FROM smartpark WHERE id = '$_POST[id]'";
      $r = mysqli_query($dbc, $q);
      $data = mysqli_fetch_assoc($r);
      if($data['vacant'] == 1) {
        $nowFormat = date('Y-m-d H:i:s');
        $q = "SELECT * FROM parktime WHERE carpark_id = '$_POST[id]' ORDER BY start_time DESC LIMIT 1";
        $r = mysqli_query($dbc, $q);
        $data = mysqli_fetch_assoc($r);
        $id = $data['id'];
        $q = "UPDATE parktime SET end_time = '$nowFormat' WHERE id = '$id'";
        $r = mysqli_query($dbc, $q);
        $q = "UPDATE smartpark SET vacant = '0' WHERE id = '$_POST[id]'";
        $r = mysqli_query($dbc, $q);
        echo "Success";
      }else {
        echo "Error";
      }
    }else {
      echo "Error";
    }
  }else {
    echo "Error";
  }

?>