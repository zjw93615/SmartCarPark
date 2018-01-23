<?php
  include("connection.php");
  date_default_timezone_set('Australia/Melbourne');
  $result = array();
  if(isset($_POST['id'])&&isset($_POST['bpID'])) {
    $q = "UPDATE smartpark SET bpID = '$_POST[bpID]' WHERE id = '$_POST[id]'";
    $r = mysqli_query($dbc, $q);
  }else {
    echo "Error";
  }

?>