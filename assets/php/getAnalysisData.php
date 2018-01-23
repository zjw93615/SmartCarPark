<?php
  include("connection.php");
  date_default_timezone_set('Australia/Melbourne');
  $result = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
  if(isset($_POST['id'])){
    $id = $_POST['id'];
    $q = "SELECT * FROM parktime WHERE carpark_id = '$id'";
    $r = mysqli_query($dbc, $q);
    while($data = mysqli_fetch_assoc($r)) {
      $sDateTime = new DateTime($data['start_time']);
      $sDate = $sDateTime->format('Y-m-d');
      $sTime = intval($sDateTime->format('H'));

      if($data['end_time'] != null) {
        $eDateTime = new DateTime($data['end_time']);
        $eDate = $eDateTime->format('Y-m-d');
        $eTime = intval($eDateTime->format('H'));
        for($i = $sTime; $i <= $eTime; $i++) {
          $result[$i]++;
        }
        $diff=date_diff($eDateTime,$eDateTime);
        $diff_days = intval($diff->format('%a'));

        for($i = 0; $i < 24; $i++) {
          $result[$i] += $diff_days;
        }
      }
    }
  }
  $myObj['results'] = $result;
  $myJSON = json_encode($myObj);
  echo $myJSON;





// $datetime1 = new DateTime('2009-10-11 12:12:00');
// $datetime2 = new DateTime('2009-10-13 10:12:00');
// $interval = $datetime1->diff($datetime2);
// echo $interval->format('%Y-%m-%d %H:%i:%s');
?>