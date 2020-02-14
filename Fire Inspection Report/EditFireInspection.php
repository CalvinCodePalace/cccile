
<?php
/*
Template Name: Edit Fire Inspection
*/
// Layout for editing the fire inspection
get_header();
require_once (dirname(dirname(__FILE__)). '/connect.php');
require_once (dirname(dirname(__FILE__)). '/tables.php');
require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
require_once(dirname(dirname(__FILE__)) . "/authorization.php");

$connection = connectPDO();
$auth = new Authorization\Authorization("Fire Inspection Report", $roles, $connection);
?>

<link href="<?php bloginfo('template_directory'); ?>/styles/print.min.css" rel="stylesheet" type="text/css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="<?php bloginfo('template_directory'); ?>/styles/view.css" rel="stylesheet" type="text/css"/>

<style>
    .selectUnits{
      margin-left: 25px !important;
      margin-top: 15px;
    }
    h1{
        font-family: 'Nixie One', cursive;
        color: #333366;
        font-weight: normal;
        clear: both;
        font-size: 36px;
        margin-top: 10px;
        margin-left: 10px;
    }

    .smokeCheck{
      width: 20px;
      height: 20px;
    }

    input[type="text"], input[type="time"],
    input[type="password"], input[type="number"],
    input[type="date"] {
        width: auto;
    }

    table th {
      width: auto !important;
      font-size: 15px;
    }

    #bottomButtons{
      text-align: center;
    }

    .unitNumber{
      font-size:25px;
      margin-left:5px;
    }
    #warnings{
      margin-left: 15px;
    }

    .unitSize{
      margin-left:5px;
      font-size: 25px;
    }

    .notes{
      margin-left:5px;
    }

    .uploadMessage{
      margin-left:10px;
    }

    .buttonDiv{
      display:inline-block;
      color:white;
      border:1px solid #CCC;
      background:#330080;
      box-shadow: 0 0 5px -1px rgba(0,0,0,0.2);
      cursor:pointer;
      vertical-align:middle;
      max-width: 100px;
      padding: 5px;
      text-align: center;
    }

    .installationButton{
      color:white;
      border:1px solid #CCC;
      background:#330080;
      box-shadow: 0 0 5px -1px rgba(0,0,0,0.2);
      cursor:pointer;
      vertical-align:middle;
      max-width: 240px;
      padding: 5px;
      text-align: center;
      visibility: visible;
    }

</style>
<?php
  //get fire file information
  function getFileInfo($connection, $idFIRE){
      $sql = "SELECT file, idFILE FROM fire_files WHERE idFIRE_INSPECTION = '$idFIRE' AND is_deleted='0';";
      foreach($connection->query($sql, PDO::FETCH_OBJ) as $row){
          $files[] = $row->file;
          $fileIDs[] = $row->idFILE;
      }

      return [$files, $fileIDs];
  }

  if (isset($_GET['id'])){
    $id = $_GET['id'];

    $sql = "SELECT * FROM fire_inspection WHERE idFIRE_INSPECTION='$id'";
    $query = trackedQuery($connectdb, $sql);
    if (!$query){
      die("Failed to get fire inspection " . mysqli_errno($connectdb));
    }
    $row = mysqli_fetch_object($query); // Get SQL object
    // Gather required variables
    $idFIRE = $row->idFIRE_INSPECTION;
    $date = $row->date;
    $unit = $row->unit;
    $size = $row->unit_size;
    $smoke = $row->smoke_detector;
    $smoke2 = $row->smoke_detector2;
    $notes = $row->notes;
    $is_deleted = $row->is_deleted;
    $installed_smoke1 = $row->installed_smoke1;
    $installed_smoke2 = $row->installed_smoke2;

    // If installed smokes exists, fetch data
    if ($installed_smoke1 != null) {
      $sql = "SELECT * FROM asset WHERE idASSET='$installed_smoke1'";
      $query = trackedQuery($connectdb, $sql);
      if (!$query){
        die("Failed to get assets for first level smoke " . mysqli_errno($connectdb));
      }
      $row = mysqli_fetch_object($query);

      $manufacturer1 = $row->mfg;
      $smokePurchaseDate1 = $row->purchase_date;
      $warranty1 = $row->warranty;
      $idSELLER_ASSET1 = $row->idSELLER_ASSET;
      $idVENDOR_ASSET1 = $row->idVENDOR_ASSET;
      $comments1 = $row->comments;
      $expiryDate1 = $row->expiry_date;
    }

    // If installed smokes exists, fetch data
    if ($installed_smoke2 != null) {
      $sql = "SELECT * FROM asset WHERE idASSET='$installed_smoke2'";
      $query = trackedQuery($connectdb, $sql);
      if (!$query){
        die("Failed to get assets for second level smoke " . mysqli_errno($connectdb));
      }
      $row = mysqli_fetch_object($query);

      $manufacturer2 = $row->mfg;
      $smokePurchaseDate2 = $row->purchase_date;
      $warranty2 = $row->warranty;
      $idSELLER_ASSET2 = $row->idSELLER_ASSET;
      $idVENDOR_ASSET2 = $row->idVENDOR_ASSET;
      $comments2 = $row->comments;
      $expiryDate2 = $row->expiry_date;

    }


    function alreadyDeleted(){
      ?>
          <div id="content" class="site-content" role="main" style="text-align: center">
          <section style="background: #ffffff; padding: 2rem; margin: 1rem 13%;
              border: 2px solid #cccccc;">
              <h1>This Fire Inspection is deleted</h1>
              <p>You cannot view deleted Fire Inspections</p>
              <p>If you want to restore this Fire Inspection, please see Programmer or Hong about restoring deleted
              Fire Inspection for Fire Inspection</p>
          </section>
          </div>
        <?php
    }

    // If is_deleted is not null, break
    if ($is_deleted != null){
        alreadyDeleted();
        die();
    }


?>

<script>
    var arrayOfArrayOfFileIds = [];
    function genFiles(fireArray, unit, idFIRE){
      var stringOutput = "";
      // Create imgs for each file in the array if array is not null
        if (fireArray[0] != null){
        for (let i = 0; i < fireArray[0].length; i++){
          var img = "<img src='http://" + "<?php echo $_SERVER['SERVER_NAME'];?>" + "/wp-content/themes/contango/Fire Inspection/uploads/"+fireArray[0][i]+"' alt='fire_picture' width=850 height=500 style='margin-top:2px;'/> ";
           $('#photoRow').append("<tr id='photo-row-"+fireArray[1][i]+"'><td>"+ img + "</td><td><div class='buttonDiv' id='delete-"+fireArray[1][i]+"'>Delete</div></td></tr>");
           arrayOfArrayOfFileIds.push(fireArray[1][i]);

           // Function to delete files
           $('#delete-'+fireArray[1][i]).on("click", function(){
             if (window.confirm("Are you sure you want to delete this file?")) {
                console.log(arrayOfArrayOfFileIds[i]);
                console.log(JSON.stringify(arrayOfArrayOfFileIds));
                 $('#photo-row-'+fireArray[1][i]).remove(); // remove row
                 arrayOfArrayOfFileIds.splice(i,1); // Remove from array
                 console.log(JSON.stringify(arrayOfArrayOfFileIds));
             }

           });
        }
      }
    }
</script>

<div id="content" class="site-content" role="main">

        <form id="fireForm" class="padded-form" action="" method="post" name="insertform" enctype="multipart/form-data">
        <div class="row">
            <h1 style='margin-left:25px'>Editing Fire Inspection</h1>
        </div>
          <div class="row">
              <div class="form-group col-sm-4">
                  <label for="date" class="col-sm-4 col-form-label">DATE:<span style='color: red;'>*</span></label>
                    <div class="col-sm-8">
                      <input type="text" id="date" class="datepicker form-control" value='<?php echo $date; ?>' required>
                  </div>
              </div>
          </div>

        <div class="numberOfEntry">
          <table id="formTable">
            <tbody>
              <thead>
                <th><strong> Unit </strong><span style='color: red;'>*</span></th>
                <th><strong> Size </strong></th>
                <th><strong> First Level Smoke Detector </strong><span style='color: red;'>*</span></th>
                <th><strong> Second Level Smoke Detector </strong></th>
                <th><strong> Notes </strong></th>
              </thead>

            <tr>
              <td>
                  <strong> <div id='unitNumber' class='unitNumber'><?php echo $unit; ?></div></strong>
              </td>
              <td><div id='unitSize' class='unitSize'><?php echo $size; ?></div></td>
              <td width=240>
                <?php
                  // If Smoke
                  if ($smoke == 1){
                    ?>
                    <label><input id='smokeCheckListYes' name='smokeCheck' class="smokeCheck" type="radio" checked>Okay</label>
                    <label><input id='smokeCheckListNo' name='smokeCheck' class="smokeCheck" type="radio">Not Okay</label>
                    <div id='installation1' class='installationButton' style='pointer-events:none; opacity: 0.2;'>Install Smoke Detector</div>
                  <?php
                  }
                  // It's not okay but also check for already installed smoke detector
                  else {
                      // If null, add the install button
                      if ($installed_smoke1 == null){
                        ?>
                        <label><input id='smokeCheckListYes' name='smokeCheck' class="smokeCheck" type="radio">Okay</label>
                        <label><input id='smokeCheckListNo' name='smokeCheck' class="smokeCheck" type="radio" checked>Not Okay</label>
                        <div id='installation1' class='installationButton'>Install Smoke Detector</div>
                    <?php
                      }
                      // Edit button
                      else {
                        ?>
                        <label><input id='smokeCheckListYes' name='smokeCheck' class="smokeCheck" type="radio" disabled>Okay</label>
                        <label><input id='smokeCheckListNo' name='smokeCheck' class="smokeCheck" type="radio" checked disabled>Not Okay</label>
                          <div id='installation1' class='installationButton'>Edit Installed Smoke Detector</div>
                      <?php
                        // If expiry date isn't null, display an expired message
                        if ($expiryDate1 != null){
                          echo "The new smoke detector installed during this inspection expired on " . $expiryDate1;
                        }
                      }
                    ?>

                    <?php
                  }

                ?>
              </td>
              <td id='secondSmokeDetector' width=240>
                <?php
                  // If Smoke 2
                  if ($smoke2 == -1){
                    echo "None";
                  }
                  else if ($smoke2 == 1){
                    ?>
                    <label><input id='smokeSecondCheckListYes' name='smokeSecondCheck' class="smokeCheck" type="radio" checked>Okay</label>
                    <label><input id='smokeSecondCheckListNo' name='smokeSecondCheck' class="smokeCheck" type="radio">Not Okay</label>
                    <div id='installation2' class='installationButton' style='pointer-events:none; opacity: 0.2;'>Install Smoke Detector</div>
                  <?php
                  }
                  else {
                      // If null, add the install button
                      if ($installed_smoke2 == null){
                        ?>
                        <label><input id='smokeSecondCheckListYes' name='smokeSecondCheck' class="smokeCheck" type="radio">Okay</label>
                        <label><input id='smokeSecondCheckListNo' name='smokeSecondCheck' class="smokeCheck" type="radio" checked>Not Okay</label>
                        <div id='installation2' class='installationButton'>Install Smoke Detector</div>
                    <?php
                      }
                      // Edit button
                      else {
                        ?>
                        <label><input id='smokeSecondCheckListYes' name='smokeSecondCheck' class="smokeCheck" type="radio" disabled>Okay</label>
                        <label><input id='smokeSecondCheckListNo' name='smokeSecondCheck' class="smokeCheck" type="radio" checked disabled>Not Okay</label>
                          <div id='installation2' class='installationButton'>Edit Installed Smoke Detector</div>
                      <?php
                        // If expiry date isn't null, display an expired message
                        if ($expiryDate2 != null){
                          echo "The new smoke detector installed during this inspection expired on " . $expiryDate2;
                        }
                      }
                    ?>

                    <?php
                  }

                ?>
              </td>
              <td>
                 <div class='notes'> <input type="text" name="note" id="note" placeholder="Write notes here" value="<?php echo $notes;?>"> </div>
              </td>
            </tr>
            <tr>
              <td colspan='5'>
                <div class="row" style='width:450px;'>

                    <input type="hidden" name="MAX_FILE_SIZE" value="4294967295">
                    <div class="files">
                        <div style='margin-left:25px;'><strong>Photos:</strong></div>
                        <input name="filesToUpload[]" id="filesToUpload" type="file"
                            class="form-control filesToUpload" style="margin-left: 15px; border: none; -webkit-box-shadow: unset; display:none;" accept="image/*">
                        <label for="filesToUpload"><div class='buttonDiv' style='margin-left: 15px; max-width:500px !important;'> Upload File</div></label>
                    </div>
                  </div>

                <div class="row" style='margin-left:15px;'>
                  <div id='uploadMessage' class="uploadMessage" hidden> Please wait, files are uploading </div>
                </div>
            </tr>
          <tbody>
          </tbody>
          </table>
          <table id='photoRow'>
            <th colspan='2'><strong> Photos </strong></th>

                <?php
                  $fireArray = json_encode(getFileInfo($connection, $idFIRE));
                  echo "<script> genFiles($fireArray, '$unit', '$idFIRE'); </script>"
                ?>
          </table>
        </div>
          <div id='bottomButtons'>
              <input type="submit" value="Confirm" id="submit">
              <div class='buttonDiv' id='deleteFireInspection'>Delete</div>
          </div>
        </form>

        <!-- Modal for Installation Smoke Detector1 -->
        <div id="myModalSmoke1" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg" style='width: 900px !important'>

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style='color:black !important;'>&times;</button>
                <h4 class="modal-title">Modal Header</h4>
              </div>
              <div id="myModalSmoke1Body" class="modal-body">
                <!-- Default name, should not change -->
                <div class="row">
                  <div class="col-sm-6">
                      <label>Name:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" style="width:250px; opacity:50%;" id="nameOfSmokeDetector1" value='Smoke Detector First Level' disabled>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Purchase Date:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="smokePurchaseDate1" class="datepicker form-control" value='<?= $smokePurchaseDate1!=null ? $smokePurchaseDate1 : date('Y-m-d'); ?>'>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Unit:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="smokeUnitNumber1" value='<?php echo $unit; ?>' disabled>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Manufacturer:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="manufacturer1" value='<?= $manufacturer1!=null ? $manufacturer1 : ''; ?>'>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Purchased From:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <select name='purchaseVendor' id='smokePurchaseVendor1'>
                      <option value=''>--- Select ---</option>
                      <?php echo getPurchaseVendor(); ?>
                    </select>
                    <script>
                      // Automatically select HD Supplee
                      $('#smokePurchaseVendor1').val(<?= $idSELLER_ASSET1!=null ? $idSELLER_ASSET1 : 10 ?>);
                    </script>
                  </div>
                </div>

                  <div class="row">
                    <div class="col-sm-6">
                        <label>Service Vendor:<span style='color: red;'>*</span></label>
                    </div>
                    <div class="col-sm-6">
                      <select name='serviceVendor' id='smokeServiceVendor1'>
                        <option value=''>--- Select ---</option>
                        <?php echo getVendor(); ?>
                      </select>
                      <script>
                        // Automatically select N/A
                        $('#smokeServiceVendor1').val(<?= $idVENDOR_ASSET1!=null ? $idVENDOR_ASSET1 : 38?>);
                      </script>
                    </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Warranty<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="smokeWarranityDate1" class="datepicker form-control" value='<?= $warranty1!=null ? $warranty1 : date('Y-m-d', strtotime('+10 years')); ?>'>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Comments</label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="comments1" value=<?= $comments1=='None' ? '' : $comments1; ?>>
                  </div>
                </div>

                <div style='display:block; text-align:center;'><div id='installSmokeDetector1' class='buttonDiv' style='width:100px;'>Install</div></div>
                <div id='success1' style='display:block; text-align:center; color:green; display:none;'>Installation Data Saved</div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style='background-color:#330080 !important;'>Close</button>
              </div>
            </div>



          </div>
        </div>
        <?php
        if ($smoke2 != -1) {
          ?>

        <!-- Modal for Installation Smoke Detector2 -->
        <div id="myModalSmoke2" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style='color:black !important;'>&times;</button>
                <h1 class="modal-title">Install Smoke Detector</h1>
              </div>
              <div id="myModalSmoke2Body" class="modal-body">
                <!-- Default name, should not change -->
                <div class="row">
                  <div class="col-sm-6">
                      <label>Name:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" style="width:250px; opacity:50%;" id="nameOfSmokeDetector2" value='Smoke Detector Second Level' disabled>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Purchase Date:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="smokePurchaseDate2" class="datepicker form-control" value='<?= $smokePurchaseDate2!=null ? $smokePurchaseDate2 : date('Y-m-d'); ?>'>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Unit:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="smokeUnitNumber2" value='<?php echo $unit; ?>'disabled>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Manufacturer:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="manufacturer2" value='<?= $manufacturer2!=null ? $manufacturer2 : ''; ?>'>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Purchased From:<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <select name='purchaseVendor' id='smokePurchaseVendor2'>
        							<option value=''>--- Select ---</option>
        							<?php echo getPurchaseVendor(); ?>
        						</select>
                    <script>
                      // Automatically select HD Supplee
                      $('#smokePurchaseVendor2').val(<?= $idSELLER_ASSET2!=null ? $idSELLER_ASSET2 : 10 ?>);
                    </script>
                  </div>
                </div>

                  <div class="row">
                    <div class="col-sm-6">
                        <label>Service Vendor:<span style='color: red;'>*</span></label>
                    </div>
                    <div class="col-sm-6">
                      <select name='serviceVendor' id='smokeServiceVendor2'>
            						<option value=''>--- Select ---</option>
            						<?php echo getVendor(); ?>
            					</select>
                      <script>
                        // Automatically select N/A
                        $('#smokeServiceVendor2').val(<?= $idVENDOR_ASSET2!=null ? $idVENDOR_ASSET2 : 38?>);
                      </script>
                    </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Warranty<span style='color: red;'>*</span></label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="smokeWarranityDate2" class="datepicker form-control" value='<?= $warranty2!=null ? $warranty2 : date('Y-m-d', strtotime('+10 years')); ?>'>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                      <label>Comments</label>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" id="comments2" value='<?= $comments2=='None' ? '' : $comments2; ?>'>
                  </div>
                </div>

                <div style='display:block; text-align:center;'><div id='installSmokeDetector2' class='buttonDiv' style='width:100px;'>Install</div></div>
                <div id='success2' style='text-align:center; color:green; display:none !important;'>Installation Data Saved</div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style='background-color:#330080 !important;'>Close</button>
              </div>
            </div>
          <?php
        }
          ?>
          </div>
        </div>

</div>

<script>
var installation1 = []; // Used to store data into installation smoke detector 1
var installation2 = []; // Used to store data into installation smoke detector 2

var isInstalled1 = false; // Locking variable for determining if 1st smoke detectors have been installed or not
var isInstalled2 = false; // Locking variable for determining if 2nd smoke detectors have been installed or not

$(document).ready(function (){
  // if smoke exists, update isInstalled1 and isInstalled2 Automatically
  var installed_smoke1 = '<?php echo $installed_smoke1; ?>';
  var installed_smoke2 = '<?php echo $installed_smoke2; ?>';
  if (installed_smoke1 != ''){
    isInstalled1 = true;
  }
  if (installed_smoke2 != ''){
    isInstalled2= true;
  }

  for (let i = 1; i <= 2; i++){
    // When purchase date changs, modify warranity
    $('#smokePurchaseDate'+i).on('change', function(){
      var date = new Date($('#smokePurchaseDate'+i).val());
      var date = new Date(date.getTime() + Math.abs(date.getTimezoneOffset()*60000));
      date.setFullYear(date.getFullYear() + 10);
      if (date.getDate() < 10){
        var day = "0" + date.getDate();
      }
      else {
        var day = date.getDate();
      }
      $('#smokeWarranityDate'+i).val(date.getFullYear() + '-' + date.getMonth()+1 + '-' + day);
      console.log(date.getFullYear() + '-' + date.getMonth()+1 + '-' + day);

    });

    // Save inputted elements for new smoke detector
    $('#installSmokeDetector'+i).on('click', function(){
      // Verify if all elements are inputted
        if (i == 1){
          installation1 = [];
          installation1.push($('#nameOfSmokeDetector'+i).val());
          if ($('#smokePurchaseDate'+i).val() != ""){
            installation1.push($('#smokePurchaseDate'+i).val());
          }
          else {
            alert('Fill in the required data: Purchase Date');
          }
          if ($('#smokeUnitNumber'+i).val() != ""){
            installation1.push($('#smokeUnitNumber'+i).val());
          }
          else{
            alert('Fill in the required data: Unit Number');
          }
          if ($('#manufacturer'+i).val() != ""){
            installation1.push($('#manufacturer'+i).val());
          }
          else{
            alert('Fill in the required data: Manufacturer');
          }
          if ($('#smokePurchaseVendor'+i).val() != ""){
            installation1.push($('#smokePurchaseVendor'+i).val());
          }
          else {
            alert('Fill in the required data: Purchase Vendor');
          }
          if ($('#smokeServiceVendor'+i).val() != ""){
            installation1.push($('#smokeServiceVendor'+i).val());
          }
          else {
            alert('Fill in the required data: Service Vendor');
          }
          if ($('#smokeWarranityDate'+i).val() != ""){
            installation1.push($('#smokeWarranityDate'+i).val());
          }
          else{
            alert('Fill in the required data: Warranity');
          }
          if ($('#comments'+i).val() != ""){
            installation1.push($('#comments'+i).val());
          }
          else {
            installation1.push("None");
          }

          console.log(installation1);
          console.log(installation1.length);

          // Length of the array would be 8. Unlock the lock for smoke detector 2.
          if (installation1.length == 8){
            isInstalled1 = true;
            // Display success message and change the button name
            $('#success1').css('display', '');
            $('#installation1').html('Edit Smoke Detector Data');
            $('input[type=radio][name=smokeCheck]').prop('disabled', true); // Disable the ability to change once you install
          }
        }

        else if (i == 2){
          installation2 = [];
          installation2.push($('#nameOfSmokeDetector'+i).val());
          if ($('#smokePurchaseDate'+i).val() != ""){
            installation2.push($('#smokePurchaseDate'+i).val());
          }
          else {
            alert('Fill in the required data: Purchase Date');
          }
          if ($('#smokeUnitNumber'+i).val() != ""){
            installation2.push($('#smokeUnitNumber'+i).val());
          }
          else{
            alert('Fill in the required data: Unit Number');
          }
          if ($('#manufacturer'+i).val() != ""){
            installation2.push($('#manufacturer'+i).val());
          }
          else{
            alert('Fill in the required data: Manufacturer');
          }
          if ($('#smokePurchaseVendor'+i).val() != ""){
            installation2.push($('#smokePurchaseVendor'+i).val());
          }
          else {
            alert('Fill in the required data: Purchase Vendor');
          }
          if ($('#smokeServiceVendor'+i).val() != ""){
            installation2.push($('#smokeServiceVendor'+i).val());
          }
          else {
            alert('Fill in the required data: Service Vendor');
          }
          if ($('#smokeWarranityDate'+i).val() != ""){
            installation2.push($('#smokeWarranityDate'+i).val());
          }
          else{
            alert('Fill in the required data: Warranity');
          }
          if ($('#comments'+i).val() != ""){
            installation2.push($('#comments'+i).val());
          }
          else {
            installation2.push("None");
          }

          console.log(installation2);
          console.log(installation2.length);

          // Length of the array would be 8. Unlock the lock for smoke detector 2.
          if (installation2.length == 8){
            isInstalled2 = true;
            // Display success message and change the button name
            $('#success2').css('display', '');
            $('#installation2').html('Edit Smoke Detector Data');
            $('input[type=radio][name=smokeSecondCheck]').prop('disabled', true); // Disable the ability to change once you install
          }
        }

    });
  }

  /* For deleting the fire inspection */
  $('#deleteFireInspection').on('click', function(){
      if (window.confirm("Are you sure you want to delete this fire inspection?")) {
        $.ajax({
             type: 'post',
             data: {
               "id": '<?php echo $id; ?>',
               "requestType": "deleteFire" // Used to run submission code only
             },
             url:"http://<?php echo $_SERVER['SERVER_NAME']; ?>/wordpress/updateEditFireInspection/", //the page containing php script
             success: function(output){
               console.log(output);
             //  alert(output);
               document.location.href = 'http://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/fire-inspection-report/';
       }
     });
    }
  });
  // If there was a change in display, modify the installation button display
  $('input[type=radio][name=smokeCheck]').change(function(){
    // If no is selected, show installation button
    if ($(this).attr('id') === "smokeCheckListNo"){
      $('#installation1').css('pointer-events', "");
      $('#installation1').css('opacity', "");
    }
    // Else hide it
    else{
        $('#installation1').css('pointer-events', "none");
        $('#installation1').css('opacity', "0.2");
    }
  });

  // If there was a change in display, modify the installation button display
  $('input[type=radio][name=smokeSecondCheck]').change(function(){
    // If no is selected, show installation button
    if ($(this).attr('id') === "smokeSecondCheckListNo"){
      $('#installation2').css('pointer-events', "");
      $('#installation2').css('opacity', "");
    }
    // Else hide it
    else{
      $('#installation2').css('pointer-events', "none");
      $('#installation2').css('opacity', "0.2");
    }
  });

  $('#installation1').on('click', function(){
    $('#success1').css("display", "none");
    $('#myModalSmoke1').modal('show');
  });

  $('#installation2').on('click', function(){
    $('#success2').css("display", "none");
    $('#myModalSmoke2').modal('show');
  });

  $('#fireForm').submit(function (event){
      var date = document.getElementById('date').value;
      var unitNumber = $('#unitNumber').text();
      var unitSize = $('#unitSize').text(); // Get unit size
      var smokeCheck = $('input[name="smokeCheck"]:checked'); // Get which radio button checked
      var secondSmokeCheck = $('input[name="smokeSecondCheck"]:checked'); // Get which radio button checked

      // Yes, is 1
      if (smokeCheck.attr('id') === "smokeCheckListYes"){
        smokeDetector = 1;
      }
      // No is 0
      else {
        smokeDetector = 0;
      }

      // Yes, is 1
      if (secondSmokeCheck.attr('id') !== undefined  && secondSmokeCheck.attr('id') === "smokeSecondCheckListYes"){
        smokeDetector2 = 1;
      }
      // No is 0
      else if (secondSmokeCheck.attr('id') !== undefined){
        smokeDetector2 = 0;
      }
      // -1 will be used to say no second smoke detector
      else {
        smokeDetector2 = -1;
      }

      // If notes is empty, change it to None
      var notes = $('#note').val();
      if (notes === ""){
        notes = "None";
      }

      // Get file array
      var fileIds = JSON.stringify(arrayOfArrayOfFileIds);

      event.preventDefault();
      // if smokeDetector1 Lock is triggered, run an AJAX to add it to assets and update old assets
      if (isInstalled1 == true){
        // Depending on installed_smoke1 == '', change the requestType to either insert or update
        if(installed_smoke1 == ''){
          // If not installed, add
          var requestTypeSmoke = 'addSmokeDetector1';
        }
        // Else, edit
        else {
            var requestTypeSmoke = 'editSmokeDetector';
        }
         $.ajax({
              type: 'post',
              data: {
                "id": '<?php echo $id; ?>',
                "idASSET": '<?php echo $installed_smoke1; ?>', // This is only used for edit type. Will be empty for adding.
                "installationArray": installation1,
                "requestType": requestTypeSmoke // Used to run submission code only
              },
              url:"http://<?php echo $_SERVER['SERVER_NAME']; ?>/wordpress/updateEditFireInspection/", //the page containing php script
              success: function(output){
                console.log(output);
              }
        });
      }

      // if smokeDetector2 Lock is triggered, run an AJAX to add it to assets and update old assets
      if (isInstalled2 == true){
          // Depending on installed_smoke2 == '', change the requestType to either insert or update
          if(installed_smoke2 == ''){
            // If not installed, add
            var requestTypeSmoke = 'addSmokeDetector2';
          }
          // Else, edit
          else {
            var requestTypeSmoke = 'editSmokeDetector';
          }
         $.ajax({
              type: 'post',
              data: {
                "id": '<?php echo $id; ?>',
                "idASSET": '<?php echo $installed_smoke2; ?>', // This is only used for edit type. Will be empty for adding.
                "installationArray": installation2,
                "requestType": requestTypeSmoke // Used to run submission code only
              },
              url:"http://<?php echo $_SERVER['SERVER_NAME']; ?>/wordpress/updateEditFireInspection/", //the page containing php script
              success: function(output){
                console.log(output);
              }
        });
      }

       $.ajax({
            type: 'post',
            data: {
              "id": '<?php echo $id; ?>',
              "date": date,
              "unitNumber": unitNumber,
              "unitSize": unitSize,
              "smokeDetector": smokeDetector,
              "smokeDetector2": smokeDetector2,
              "note": notes,
              "fileIds": fileIds,
              "requestType": "submitFire" // Used to run submission code only
            },
            url:"http://<?php echo $_SERVER['SERVER_NAME']; ?>/wordpress/updateEditFireInspection/", //the page containing php script
            success: function(output){
            //  console.log(output);
            //  alert(output);
            document.location.href = 'http://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/fire-inspection-report/';
      }
    });
  });

    /*
    Ajax function for uploading fire photos to the server
    */
    $('body').on("change", '#filesToUpload', function(){
          $('#submit').prop('disabled', true); // Disable to ensure files are uploaded before submitting
          $('#submit').css('opacity', "0.2");
          $('#uploadMessage').show();
          $('body').css('pointer-events', 'none');
      //    var fileIds = [];
          var formData = new FormData();
          var file = $(this).prop('files')[0];
          formData.append('requestType', 'uploadFile');
          formData.append('filesToUpload[]', file);
          formData.append('unitNumber', <?php echo $unit ?>);

          $.ajax({
              type: "POST",
              url: "/wordpress/updateEditFireInspection/",
              contentType: false,
              processData: false,
              cache: false,
              data: formData,
              complete: function (ret){
                // This large text is no longer viable and must be removed
                var lastIndex = ret.responseText.lastIndexOf('>');
                var lengthOfString = ret.responseText.length;
                var idFILE = ret.responseText.substring(lastIndex + 1, lengthOfString).replace(/\s/g, "")

                // if not defined, make it defined
                if (arrayOfArrayOfFileIds === undefined){
                  arrayOfArrayOfFileIds.push(idFILE);
                }
                else {
                  arrayOfArrayOfFileIds.push(idFILE);
                }

                // Call another AJAX to display the img
                var formData2 = new FormData();
                formData2.append('requestType', 'previewImage');
                formData2.append('idFILE', idFILE);

                $.ajax({
                    type: "POST",
                    url: "/wordpress/updateEditFireInspection/",
                    contentType: false,
                    processData: false,
                    cache: false,
                    data: formData2,
                    complete: function (ret){
                      console.log(ret.responseText);
                      // This large text is no longer viable and must be removed
                      var lastIndex = ret.responseText.lastIndexOf('>');
                      var lengthOfString = ret.responseText.length;
                      var file_name = ret.responseText.substring(lastIndex + 1, lengthOfString).replace(/\s/g, "");
                      $('#photoRow').append("<tr id='photo-row-"+idFILE+"'><td><img src='http://" + "<?php echo $_SERVER['SERVER_NAME'];?>" + "/wp-content/themes/contango/Fire Inspection/uploads/" + file_name + "' alt='fire_picture' width=850 height=500 style='margin-top:2px;'/></td><td><div class='buttonDiv' id='delete-"+idFILE+"'>Delete</div></tr>");
                      // Function to delete files
                      console.log("<tr id='photo-row-"+idFILE+"'><td><img src='http://" + "<?php echo $_SERVER['SERVER_NAME'];?>" + "/wp-content/themes/contango/Fire Inspection/uploads/" + file_name + "' alt='fire_picture' width=850 height=500 style='margin-top:2px;'/></td><td><div class='buttonDiv' id='delete-"+idFILE+"'>Delete</div></tr>");
                      $('#delete-'+idFILE).on("click", function(){
                        if (window.confirm("Are you sure you want to delete this file?")) {
                           console.log(JSON.stringify(arrayOfArrayOfFileIds));
                            $('#photo-row-'+idFILE).remove(); // remove row
                            arrayOfArrayOfFileIds.splice(arrayOfArrayOfFileIds.indexOf(idFILE),1); // Remove from array
                            console.log(JSON.stringify(arrayOfArrayOfFileIds));
                        }
                      });

                      $('#submit').prop('disabled', false); // Disable to ensure files are uploaded before submitting
                      $('#uploadMessage').hide();
                      $('#submit').css('opacity', "");
                      $('body').css('pointer-events', '');
                    }
                  });
              }
          });
        });

});

</script>

<?php
}
else {
  echo "Invalid Selection";
}
