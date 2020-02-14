<?php
/*
Template Name: updateEditFireInspection
*/
// Helper PHP file of EditFireInspection.php. Used to add data into SQL database
require_once(dirname(dirname(__FILE__)) . '/connect.php');

if (isset($_POST['requestType'])){
  $requestType = $_POST['requestType'];

  switch ($requestType) {
      // Case: Submitting Fire Inspection
      case "submitFire":
        // if valid unit number
        if (isset($_POST['id'])){
            $id = $_POST['id'];
            $unitNumber = $_POST['unitNumber'];
            $date = $_POST['date'];
            $unitSize = $_POST['unitSize'];
            $smokeDetector = $_POST['smokeDetector'];
            $smokeDetector2 = $_POST['smokeDetector2'];
            $note = $_POST['note'];
            $fileIds = json_decode(stripslashes($_POST['fileIds']));

            // Insert into fire inspection
            $sql = "UPDATE fire_inspection SET unit='$unitNumber', date='$date', unit_size='$unitSize', smoke_detector='$smokeDetector', smoke_detector2='$smokeDetector2', notes='$note', edited='*' WHERE idFIRE_INSPECTION='$id'";
            if (!$query = trackedQuery($connectdb, $sql)){
                die("Failed to update fire inspection " . mysqli_errno($connectdb));
            }
      //      $lastID = $GLOBALS['connectdb']->insert_id;

            // Update fire_files to include idFIRE_INSPECTION LINK
            if (!empty($fileIds)){
                $listIds = implode("','", $fileIds);
                // Reset all fire files, change deleted ones to 0
                $sql = "UPDATE fire_files SET idFIRE_INSPECTION = '0' WHERE idFIRE_INSPECTION='$id'";
                $query = trackedQuery($GLOBALS['connectdb'], $sql);
                if (!$query) {
                  die('Invalid query: reset fire files ' . mysqli_errno($connectdb));
                }

                $sql = "UPDATE fire_files SET idFIRE_INSPECTION = '$id' WHERE idFILE IN ('$listIds')";
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

        // Case deleting fire inspection
        case "deleteFire":
          if (isset($_POST['id'])){
            $idFIRE = $_POST['id'];
            $sql = "UPDATE fire_inspection SET is_deleted=1 WHERE idFIRE_INSPECTION='$idFIRE'";
            if (!$query = trackedQuery($connectdb, $sql)){
                die("Failed to delete fire inspection " . mysqli_errno($connectdb));
            }
          }
          break;

        // Case adding first level smoke detector
        case "addSmokeDetector1":
          if(isset($_POST['id'])){
            $id = $_POST['id'];
            // Get the array and start adding contents
            $installationArray = $_POST['installationArray'];

            $smokeName = $installationArray[0];
            $purchaseDate = $installationArray[1];
            $unit = $installationArray[2];
            $manufacturer = $installationArray[3];
            $purchasedFrom = $installationArray[4];
            $serviceVendor = $installationArray[5];
            $warranty = $installationArray[6];
            $comments = $installationArray[7];

            // Update the old smoke. For some reason, certain queries to try to update assets complains about cannot finding ID but it works fine on phpmyadmin when I run it.
            // Therefore, instead of dealing with it, I will split the queries
            $findUnitIDSQL = "SELECT idUNIT FROM unit WHERE unit_number = '$unit'";
            if (!$query = trackedQuery($connectdb, $findUnitIDSQL)){
               die("Failed to get IDUNIT " . mysqli_errno($connectdb));
            }
            $row = mysqli_fetch_object($query);
            $idUNIT = $row->idUNIT;

            // Update the old smoke
            $oldSmokeSQL = "UPDATE
                            asset
                           SET
                            expiry_date = '$purchaseDate'
                           WHERE
                            name = 'Smoke Detector First Level' AND idUNIT_ASSET='$idUNIT' AND expiry_date IS NULL";
           if (!$query = trackedQuery($connectdb, $oldSmokeSQL)){
                die("Failed to UPDATE Smoke  Detectors in Assets " . mysqli_errno($connectdb));
           }

           // Insert into Assets
           $newSmokeSQL = "INSERT INTO asset (idUNIT_ASSET,idVENDOR_ASSET, name, mfg, purchase_date, warranty, comments, idSELLER_ASSET)
                          VALUES ($idUNIT, $serviceVendor, '$smokeName', '$manufacturer', '$purchaseDate', '$warranty', '$comments', $purchasedFrom)";
          if (!$query = trackedQuery($connectdb, $newSmokeSQL)){
             die("Failed to add New Smoke Detectors in Assets " . mysqli_errno($connectdb));
          }
          $lastAssetId = $GLOBALS['connectdb']->insert_id; // Get the last asset ID inserted
          // Update fire inspection by linking asset ID
          $updateFireInspectionSQL = "UPDATE fire_inspection SET installed_smoke1='$lastAssetId' WHERE idFIRE_INSPECTION='$id'";
          if (!$query = trackedQuery($connectdb, $updateFireInspectionSQL)){
             die("Failed to link smoke detector with fire inspection " . mysqli_errno($connectdb));
          }
        }
        break;

      // Case adding first level smoke detector
      case "addSmokeDetector2":
          if(isset($_POST['id'])){
            $id = $_POST['id'];
            // Get the array and start adding contents
            $installationArray = $_POST['installationArray'];

            $smokeName = $installationArray[0];
            $purchaseDate = $installationArray[1];
            $unit = $installationArray[2];
            $manufacturer = $installationArray[3];
            $purchasedFrom = $installationArray[4];
            $serviceVendor = $installationArray[5];
            $warranty = $installationArray[6];
            $comments = $installationArray[7];
            // Update the old smoke. For some reason, certain queries to try to update assets complains about cannot finding ID but it works fine on phpmyadmin when I run it.
            // Therefore, instead of dealing with it, I will split the queries
            $findUnitIDSQL = "SELECT idUNIT FROM unit WHERE unit_number = '$unit'";
            if (!$query = trackedQuery($connectdb, $findUnitIDSQL)){
               die("Failed to get IDUNIT " . mysqli_errno($connectdb));
            }
            $row = mysqli_fetch_object($query);
            $idUNIT = $row->idUNIT;
            // Update the old smoke
            $oldSmokeSQL = "UPDATE
                            asset
                           SET
                            expiry_date = '$purchaseDate'
                           WHERE
                            name = 'Smoke Detector Second Level' AND idUNIT_ASSET='$idUNIT' AND expiry_date IS NULL";
           if (!$query = trackedQuery($connectdb, $oldSmokeSQL)){
                die("Failed to update Smoke Detectors in Assets " . mysqli_errno($connectdb));
           }

           // Insert into Assets
           $newSmokeSQL = "INSERT INTO asset (idUNIT_ASSET,idVENDOR_ASSET, name, mfg, purchase_date, warranty, comments, idSELLER_ASSET)
                          VALUES ($idUNIT, $serviceVendor, '$smokeName', '$manufacturer', '$purchaseDate', '$warranty', '$comments', $purchasedFrom)";
          if (!$query = trackedQuery($connectdb, $newSmokeSQL)){
             die("Failed to add New Smoke Detectors in Assets " . mysqli_errno($connectdb));
          }
          $lastAssetId = $GLOBALS['connectdb']->insert_id; // Get the last asset ID inserted
          // Update fire inspection by linking asset ID
          $updateFireInspectionSQL = "UPDATE fire_inspection SET installed_smoke2='$lastAssetId' WHERE idFIRE_INSPECTION='$id'";
          if (!$query = trackedQuery($connectdb, $updateFireInspectionSQL)){
             die("Failed to link smoke detector with fire inspection " . mysqli_errno($connectdb));
          }
        }
        break;

     // Case Editing Smoke Detector that has been submitted
     case "editSmokeDetector":
      if (isset($_POST['idASSET'])){
          $idASSET = $_POST['idASSET']; // Get asset ID

          $id = $_POST['id'];
          // Get the array and start adding contents
          $installationArray = $_POST['installationArray'];

          $smokeName = $installationArray[0];
          $purchaseDate = $installationArray[1];
          $unit = $installationArray[2];
          $manufacturer = $installationArray[3];
          $purchasedFrom = $installationArray[4];
          $serviceVendor = $installationArray[5];
          $warranty = $installationArray[6];
          $comments = $installationArray[7];

          $sql = "UPDATE
                    asset
                  SET
                    purchase_date='$purchaseDate',
                    mfg='$manufacturer',
                    idVENDOR_ASSET='$serviceVendor',
                    idSELLER_ASSET='$purchasedFrom',
                    warranty='$warranty',
                    comments='$comments'
                  WHERE
                    idASSET = '$idASSET'";
        if (!$query = trackedQuery($connectdb, $sql)){
             die("Failed to update second floor smoke detector asset " . mysqli_errno($connectdb));
        }
      }
      break;
  }
}


function uploadFiles($connection, $unitNumber){
    $i = 0;
    $fileId = 0;
    foreach ($_FILES['filesToUpload']['tmp_name'] as $key => $tmp_name) {
        if(empty($_FILES['filesToUpload']['name'][$key])){
            break;
        }
        $file_name = $_FILES['filesToUpload']['name'][$key];
        $file_size = $_FILES['filesToUpload']['size'][$key];
        $file_tmp = $_FILES['filesToUpload']['tmp_name'][$key];
        $file_type = $_FILES['filesToUpload']['type'][$key];
        $desired_dir = "wp-content/themes/contango/Fire Inspection/uploads/";
        $desired_dir_ = "wp-content/themes/contango/Fire Inspection/uploads/";

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
