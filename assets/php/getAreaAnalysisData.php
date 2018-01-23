<?php
  include("connection.php");
  $key = 'AIzaSyBafCANRIdz41dwRNf_GnNVCN4Mbeg3uCw';
  $result = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
  if(isset($_POST['lat'])&&isset($_POST['lng'])){
    $num = 3000;
    if(isset($_POST['num'])) {
      $num = $_POST['num'];
    }
    $q = "SELECT * FROM smartpark";
    $r = mysqli_query($dbc, $q);
    while($data = mysqli_fetch_assoc($r)) {
      $dis = distanceSimplify($_POST['lat'], $_POST['lng'], $data['lat'], $data['lng']);
      if(sizeof($result) < $num && $dis <= 500) {
        $id = $data['id'];
        $q2 = "SELECT * FROM parktime WHERE carpark_id = '$id'";
        $r2 = mysqli_query($dbc, $q2);
        while($data2 = mysqli_fetch_assoc($r2)) {
          $sDateTime = new DateTime($data2['start_time']);
          $sDate = $sDateTime->format('Y-m-d');
          $sTime = intval($sDateTime->format('H'));

          if($data2['end_time'] != null) {
            $eDateTime = new DateTime($data2['end_time']);
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
    }
  }
  $myObj['results'] = $result;
  $myJSON = json_encode($myObj);
  echo $myJSON;

  function distanceSimplify($lat1, $lng1, $lat2, $lng2) {
    $EARTH_RADIUS = 6378137;
    $radLat1 = rad($lat1);
    //echo $radLat1;
    $radLat2 = rad($lat2);
    $a = $radLat1 - $radLat2;
    $b = rad($lng1) - rad($lng2);
    $s = 2 * asin(sqrt(pow(sin($a/2),2) +
    cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
    $s = $s * $EARTH_RADIUS;
    $s = round($s * 10000) / 10000;
    return $s;
  }

  function rad($d)
  {
    return $d * 3.1415926535898 / 180.0;
  }

?>