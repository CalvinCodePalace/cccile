<?php
/*
Template Name: updateFireInspection
*/
// Helper PHP file of FireInspection.php. Used to add data into SQL database
require_once(dirname(dirname(__FILE__)) . '/connect.php');
if (isset($_POST['requestType'])){
  $requestType = $_POST['requestType'];

  switch ($requestType) {
      // Case: Submitting Fire Inspection
      case "submitFire":
        // if valid unit number
        if (isset($_POST['unitNumber'])){
            $unitNumber = $_POST['unitNumber'];
            $date = $_POST['date'];
            $unitSize = $_POST['unitSize'];
            $smokeDetector = $_POST['smokeDetector'];
            $smokeDetector2 = $_POST['smokeDetector2'];
            $note = $_POST['note'];
            $fileIds = json_decode(stripslashes($_POST['fileIds']));

            // Insert into fire inspection
            $sql = "INSERT INTO fire_inspection(unit, unit_size, smoke_detector,smoke_detector2, notes, date) VALUES ('$unitNumber', '$unitSize', $smokeDetector, $smokeDetector2, '$note', '$date')";
            if (!$query = trackedQuery($connectdb, $sql)){
                die("Failed to insert fire inspection " . mysqli_errno($connectdb));
            }
            $lastID = $GLOBALS['connectdb']->insert_id;


            // Update fire_files to include idFIRE_INSPECTION LINK
            if (!empty($fileIds)){
                $listIds = implode("','", $fileIds);
                $sql = "UPDATE fire_files SET idFIRE_INSPECTION = '$lastID' WHERE idFILE IN ('$listIds')";
                $query = trackedQuery($GLOBALS['connectdb'], $sql);
                if (!$query) {
                  die('Invalid query: updates fire files ' . mysqli_errno($connectdb));
                }
            }
        }
        break;

      // Case uploading file
      //file upload function
      case "uploadFile":
          if (isset($_POST['unitNumber'])){
            $unitNumber = $_POST['unitNumber'];

            $fileId = uploadFiles($connection, $unitNumber);
          }

          echo $fileId;

          break;

      // Case grabbing image file
      case "previewImage":
        if (isset($_POST['idFILE'])){
          $idFILE = $_POST['idFILE'];

          $sql = "SELECT file FROM fire_files WHERE idFILE='$idFILE'";
          if (!$query = trackedQuery($connectdb, $sql)){
              die("Failed to get fire files " . mysqli_errno($connectdb));
          }
          $row = mysqli_fetch_object($query);
          echo $row->file;

        }

        break;

  }
}


function uploadFiles($connection, $unitNumber){
    $i = 0;
    $fileId = 0;
    foreach ($_FILES['filesToUpload'.$unitNumber]['tmp_name'] as $key => $tmp_name) {
        if(empty($_FILES['filesToUpload'.$unitNumber]['name'][$key])){
            break;
        }
        $file_name = $_FILES['filesToUpload'.$unitNumber]['name'][$key];
        $file_size = $_FILES['filesToUpload'.$unitNumber]['size'][$key];
        $file_tmp = $_FILES['filesToUpload'.$unitNumber]['tmp_name'][$key];
        $file_type = $_FILES['filesToUpload'.$unitNumber]['type'][$key];
        $desired_dir = ""; //RETRACTED
        $desired_dir_ = ""; //RETRACTED

        if (is_dir($desired_dir_) == false) {
            mkdir($desired_dir_, 0700);
            // Create directory if it does not exist
        }

        if (is_dir($desired_dir . $file_name) == false) {
            $old_name = $file_tmp;
            $new_name = date("Y-m-d_H-m-s") . '_' . $i . '_';
            $new_name = "fire_inspection_"."unit_" . $unitNumber . "_" . $i."_". $new_name . $file_name;
            $i++;

            $exploded = explode('/',$file_type);
            $ext = $exploded[count($exploded) - 1];
            if (preg_match('/jpg|jpeg/i',$ext))
                $source=imagecreatefromjpeg($old_name);
            else if (preg_match('/png/i',$ext))
                $source=imagecreatefrompng($old_name);
            else if (preg_match('/gif/i',$ext))
                $source=imagecreatefromgif($old_name);
            else if (preg_match('/bmp/i',$ext))
                $source=imagecreatefrombmp($old_name);
            else
                return 0;
            imagejpeg($source, $desired_dir .$new_name, 75);
        } else {
            //rename the file if another one exist
            $old_name = $file_tmp;
            $new_name = date("Y-m-d_H-m-s") . '_' . $i . '_';
            $new_name = "fire_inspection_"."unit_" . $unitNumber . "_" . $i."_". $new_name . $file_name;
            $i++;

            $exploded = explode('/',$file_type);
            $ext = $exploded[count($exploded) - 1];
            if (preg_match('/jpg|jpeg/i',$ext))
                $source=imagecreatefromjpeg($old_name);
            else if (preg_match('/png/i',$ext))
                $source=imagecreatefrompng($old_name);
            else if (preg_match('/gif/i',$ext))
                $source=imagecreatefromgif($old_name);
            else if (preg_match('/bmp/i',$ext))
                $source=imagecreatefrombmp($old_name);
            else
                return 0;
            imagejpeg($source, $desired_dir .$new_name, 75);
        }
        $sql = "INSERT INTO fire_files (file, size, type, idFIRE_INSPECTION) VALUES ('$new_name', '$file_size', '$file_type','0')";
        $query = trackedQuery($GLOBALS['connectdb'], $sql);
        if (!$query) {
         die('Invalid query: insert in fire_files ' . mysqli_errno($connectdb));
        }
        $fileId = $GLOBALS['connectdb']->insert_id;
    }

    return $fileId;
}
