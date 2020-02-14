<?php
/*
Template Name: getUnitSize
*/
// Helper PHP file of FireInspection.php. Used to get unit size once selected a unit
require_once(dirname(dirname(__FILE__)) . '/connect.php');

// If unitNumber given,
if (isset($_POST['unitNumber'])){
  $unitNumber = $_POST['unitNumber'];

  // Grab Unit size
  $sql = "SELECT unit_size FROM unit WHERE unit_number='$unitNumber'";
//echo $sql;
  if (!$query = trackedQuery($connectdb, $sql)){
      die("Failed to get unit size " . mysqli_errno($connectdb) . " - " . mysqli_error($connectdb));
  }
  $row = mysqli_fetch_object($query);
  $unit_size = $row->unit_size;
  echo $unit_size;
}

?>
