
<?php
/*
Template Name: genFireInspectionReport
*/
// Generates fire inspection data once the date ranges are selected from View Fire Inspection Report
require_once (dirname(dirname(__FILE__)). '/connect.php');
require_once (dirname(dirname(__FILE__)). '/tables.php');
require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
require_once(dirname(dirname(__FILE__)) . "/authorization.php");

$connection = connectPDO();
$auth = new Authorization\Authorization("Fire Inspection Report", $roles, $connection);
?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/tableSorting.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<hr>
<?php

// If a start date and end date is given
if (isset($_POST['startDate']) && isset($_POST['endDate'])){

  // ADD ASSETS AUTOMATICALLY
  // THIS SHOULD REMAIN COMMENTED Out
  $theArrayOfSmokeDetectors = array();
  $theArrayOfSmokeDetectors[] = explode(",", "Smoke Detector First Level, 2016-04-01, 2026-04-01, 1001, Kidde, 10, 38, None");


    foreach($theArrayOfSmokeDetectors as $contents){
      $name = $contents[0];
      $purchaseDate = $contents[1];
      $warranty = $contents[2];
      $unit = $contents[3];
      $unitSQL = "SELECT idUNIT FROM unit WHERE unit_number='$unit'";
      $query = trackedQuery($connectdb, $unitSQL);

      $row = mysqli_fetch_object($query);
      $idUNIT = $row->idUNIT;

      $mfg = $contents[4];
      $seller_asset = $contents[5];
      $vendor_asset = $contents[6];
      $comment = $contents[7];
      $sql = "INSERT INTO asset (name, purchase_date, warranty, idUNIT_ASSET, mfg, idSELLER_ASSET, idVENDOR_ASSET, comments)
              VALUES ('$name', '$purchaseDate', '$warranty', '$idUNIT', '$mfg', $seller_asset, $vendor_asset, '$comment')";
      $query = trackedQuery($connectdb, $sql);

//      $stmt->execute(['1998',$content_entry_time,$content_date,'old input data','out',0, $content_exit_time,'old input data',$content_total_time]);
    }


  // Get the required variables
  $start_date= $_POST['startDate'];
  $end_date = $_POST['endDate'];
  $selectedUnits = $_POST['selectedUnits'];
  $checkedAll = $_POST['checkedAll'];
  $excelSQL = "SELECT
                f.date AS DATE,
                f.unit AS UNIT,
                f.unit_size AS UNIT_SIZE,
                (CASE f.smoke_detector
                		WHEN 1 THEN 'OKAY'
                 	    WHEN 0 THEN 'NOT OKAY'
                END)AS FIRST_LEVEL_SMOKE_DETECTOR,
                (CASE f.smoke_detector2
                    WHEN 1 THEN 'OKAY'
                      WHEN 0 THEN 'NOT OKAY'
                END)AS SECOND_LEVEL_SMOKE_DETECTOR,
                f.notes as NOTES,
                f.edited as EDITED
              FROM
                fire_inspection f
              WHERE ";
  // If checkedAll selected, run a regular SQL that considers all units
  if ($checkedAll == 1){
    $sql = "SELECT * FROM fire_inspection WHERE is_deleted IS NULL AND `date`>='$start_date' AND `date`<='$end_date' ORDER BY `date` ASC, unit";

    $excelSQL .= "is_deleted IS NULL AND `date` >= '$start_date' AND `date` <= '$end_date'
                ORDER BY
                  `date` ASC,
                  unit";
  }
  // Else if no units are selected, display nothing (this is to prevent errors) AlTERNATIVELY: CAN VIEW ALL UNITS BUT MIGHT BE CONFUSING TO USER
  else if(sizeof($selectedUnits) == 0){
    $sql = "SELECT * FROM fire_inspection WHERE is_deleted IS NULL AND `date`>='$start_date' AND `date`<='$end_date' AND unit=-1 ORDER BY `date` ASC, unit";
    $excelSQL .= " is_deleted IS NULL AND `date`>='$start_date' AND `date`<='$end_date' AND unit=-1 ORDER BY `date` ASC, unit";
  }
  else {
    $selectedUnitsImploded = implode("','", $selectedUnits);
    $sql = "SELECT * FROM fire_inspection WHERE is_deleted IS NULL AND `date`>='$start_date' AND `date`<='$end_date' AND unit IN ('$selectedUnitsImploded') ORDER BY `date` ASC, unit";
    $excelSQL .= " is_deleted IS NULL AND `date`>='$start_date' AND `date`<='$end_date' AND unit IN ('$selectedUnitsImploded') ORDER BY `date` ASC, unit";
  }


  $query = trackedQuery($connectdb, $sql);
  if (!$query){
      die("Failed to get fire inspection " . mysqli_errno($connectdb));
  }

  //get fire file information
  function getFileInfo($connection, $idFIRE){
      $sql = "SELECT file, idFILE FROM fire_files WHERE idFIRE_INSPECTION = '$idFIRE' AND is_deleted='0';";
      foreach($connection->query($sql, PDO::FETCH_OBJ) as $row){
          $files[] = $row->file;
          $fileIDs[] = $row->idFILE;
      }

      return [$files, $fileIDs];
  }
  ?>
  <div class="floors topBarButtons" style="margin-left: 25px !important;">
      <form id="form" action="http://<?php echo $_SERVER['SERVER_NAME']; ?>/wordpress/export-to-excel" method="post">
        <input type="hidden" name="sql" id="sql" value="<?php echo $excelSQL; ?>">
        <input type="hidden" name="filename" id="filename">
        <input type="submit" id="get_excel" value="Excel" data-table-name="myTable">
      </form>
  </div>
  <div class="floors topBarButtons">
    <input type="submit" id="print" value="Print"
         onclick="printTable('myTable', 'hiddenTable', 'Fire Inspection Report')">
  </div>
<script>
  // Create the modal for fire files
  function genFiles(fireArray, unit, idFIRE){
    console.log(fireArray);
    var stringOutput;
    stringOutput = '<div id="myModal'+idFIRE+'" class="modal fade" role="dialog">';
    stringOutput += '<div class="modal-dialog">';
    stringOutput += '<div class="modal-content">';
    stringOutput +=  '<div class="modal-header">';
    stringOutput += '<button type="button" style="color:#330080 !important;" class="close" data-dismiss="modal">&times;</button>';
    stringOutput += '<h1 class="modal-title">Files for Unit: '+unit+'</h1>';
    stringOutput += '</div>';
    stringOutput += '<div class="modal-body">';

    // If array is null, then do not check for files
    if (fireArray[0] == null){
        stringOutput += '<p> No files uploaded </p>';
    }
    else {
      // Create imgs for each file in the array
      for (var i = 0; i < fireArray[0].length; i++){
        var img = "<img src='http://" + "<?php echo $_SERVER['SERVER_NAME'];?>" + "/wp-content/themes/contango/Fire Inspection/uploads/"+fireArray[0][i]+"' alt='fire_picture' width=200 height=200 style='margin-top:2px;'/> ";
        stringOutput += img;
      }
    }
    stringOutput += '</div>';
    stringOutput += '<div class="modal-footer">';
    stringOutput += '<button type="button" style="background-color:#330080 !important;" class="btn btn-default" data-dismiss="modal">Close</button>';
    stringOutput += '</div></div></div></div>';
    $('#modalOfFiles').append(stringOutput);
    console.log($('#modalOfFiles').html());


  }


</script>
  <div id="tablesDiv">
    <div class="moveTable" id="individualTable">
        <div id="table">
            <table id="myTable" class="tablesorter">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Unit</th>
                        <th>Size</th>
                        <th>1st Level Smoke Detector</th>
                        <th>2nd Level Smoke Detector</th>
                        <th>Notes</th>
                        <th>View Files</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                          $columns = array();
                          $columns[] = "Date";
                          $columns[] = "Unit";
                          $columns[] = "Size";
                          $columns[] = "1st Level Smoke Detector";
                          $columns[] = "2nd Level Smoke Detector";
                          $columns[] = "Notes";

                          // Go through each object in the SQL
                          while ($row = mysqli_fetch_object($query)){
                            // Gather required variables
                            $idFIRE = $row->idFIRE_INSPECTION;
                            $date = $row->date;
                            $unit = $row->unit;
                            $size = $row->unit_size;
                            $smoke = $row->smoke_detector;
                            $smoke2 = $row->smoke_detector2;
                            $notes = $row->notes;
                            $edited = $row->edited;

                            echo "<tr>";
                            // Set edit button hyperlink based on weather_entry_id
                            echo  "<td> <a href='http://" . $_SERVER['SERVER_NAME'] ."/wordpress/edit-fire-inspection/?id=$idFIRE'> " . $date;
                            // if edited, add *
                            if ($edited === '*'){
                              echo "*";
                            }
                            // If smoke is 1, okay
                            if ($smoke == 1){
                              $smoke = "Okay";
                            }
                            else {
                              $smoke = "Not Okay";
                            }

                            // If smoke is 1, okay
                            if ($smoke2 == 1){
                              $smoke2 = "Okay";
                            }
                            else if($smoke2 == -1) {
                              $smoke2 = "None";
                            }
                            else {
                              $smoke2 = "Not Okay";
                            }
                            echo "</a></td>";
                            echo  "<td width=50> $unit </td>
                                   <td width=50> $size </td>
                                   <td> $smoke </td>
                                   <td> $smoke2 </td>
                                   <td> $notes </td>";
                                   $fireArray = json_encode(getFileInfo($connection, $idFIRE));
                                   echo "<script> genFiles($fireArray, '$unit', '$idFIRE'); </script>";
                                  ?>

                                   <td width=100> <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal<?php echo $idFIRE;?>" style='width:100px; font-size:15px;'>View Files</button></td>
                            <?php echo "</tr>";
                          }
                        ?>

                      </tbody>
                  </table>
              </div>
          </div>
        </div>
        <!-- this div stores all the modals of files -->
        <div id='modalOfFiles'>

        </div>

        <div id="hiddenTableDiv">
            <table id="hiddenTable">
                <thead>
                <tr>
                  <th>Date</th>
                  <th>Unit</th>
                  <th>Size</th>
                  <th>1st Level Smoke Detector</th>
                  <th>2nd Level Smoke Detector</th>
                  <th>Notes</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <script>
          $(document).ready(function() {
            var startdate = $('#startDate').val();
            var enddate = $('#endDate').val();

            $("#filename").val("fire_inspection_report" + startdate + "_to_" + enddate + ".xls");;

            var numOfRows = document.getElementById('myTable').getElementsByTagName('tr').length;
            // If innerhtml is empty
            if (numOfRows == 1){
              $("#print").hide();
            }
            else {
              $("#print").show();
            }

          });

          // Prints the Table of the Hidden Table
            function printTable(sourceTableName, destinationTableName, title) {
                  var columns = <?php echo json_encode($columns); ?>;
              //    printDiv(sourceTableName, title);
                 fillAndPrintTable(sourceTableName, destinationTableName, columns, title, []);
            }

            // Helper function to print the table
            function fillAndPrintTable(sourceTableName, destinationTableName, columns, title, columnExtracts) {
                fillHiddenTable(sourceTableName, destinationTableName, columns, columnExtracts);
                printDiv(destinationTableName, title);
            }


        </script>
      <?php
      }
      ?>
