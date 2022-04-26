<?php 

function alert(String $message) {
  print "<script>alert('" . $message . "');</script>";
}

function console_log(String $message) {
  print "<script>console.log('" . $message . "');</script>";
}

?>
