<?php
  $path = parse_path();

  if(!isset($path['call_parts'][0]) || $path['call_parts'][0] == '') {
    $path['call_parts'][0] = 'home';
  }

  function getPageData($path,$dbc)
  {
    if(!isset($path['call_parts'][0]) || $path['call_parts'][0] == '') {
      include("home.php");
    }else if($path['call_parts'][0] == 'home') {
      include("home.php");
    }else if($path['call_parts'][0] == 'analysis') {
      include("analysis.php");
    }
  }
?>