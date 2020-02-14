<?php
/*
Template Name: Fire Inspection
*/
/*
This php file utilzies offline.js which is an opensource Javascript file that deals with internet disconnections
The github can be found at : https://github.com/HubSpot/offline
Documentation of offline.js is found at: https://github.hubspot.com/offline/docs/welcome/
*/
get_header();
require_once(dirname(dirname(__FILE__)) . "/connect.php");
require_once(dirname(dirname(__FILE__)) . "/Unit/selectUnitsForFireInspection.php");
require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
require_once(dirname(dirname(__FILE__)) . "/authorization.php");
$current_user = wp_get_current_user();

$connection = connectPDO();
$auth = new Authorization\Authorization("Fire Inspection", $roles, $connection);
?>

<link href="<?php bloginfo('template_directory'); ?>/styles/print.min.css" rel="stylesheet" type="text/css">
<link href="<?php bloginfo('template_directory'); ?>/styles/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php bloginfo('template_directory'); ?>/styles/view.css" rel="stylesheet" type="text/css"/>
<link href="<?php bloginfo('template_directory'); ?>/Unit/modalcss.css" rel="stylesheet" type="text/css" />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="<?php bloginfo('template_directory'); ?>/styles/offline-theme-default.css"  rel="stylesheet" type="text/css">
<link href="<?php bloginfo('template_directory'); ?>/styles/offline-language-english.css"  rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/offline.js"></script>
<script>
    var run = function(){

    if (Offline.state === 'up'){
      Offline.check();
      console.log('up');
      $('#myModal').modal('hide');
    }
    else {
      $('#myModal').modal('show');
    }

    }
     setInterval(run, 1000);
</script>

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

    #submit{
      margin:auto;
      display:block;
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

    #counter{
      color: white !important;
      font-size: 30px !important;
    }

</style>

<!-- Modal for internet connection -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" style='color:#cc66ff !important'>INTERNET DISCONNECTED</h1>
      </div>
      <div class="modal-body">
        <p style='color:white !important'>PLEASE RECONNECT BEFORE YOU PROCEED</p>
      </div>
      <div class="modal-footer">
      </div>
    </div>

  </div>
</div>

<!-- Modal for submission success connection -->
<div id="myModal2" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" style='color:#cc66ff !important'>SUBMITTED</h1>
      </div>
      <div class="modal-body">
        <p style='color:white !important'>YOU WILL BE REDIRECTED IN 30 SECONDS (TO ENSURE ALL DATA SUBMITS)</p>
      </div>
      <div class="modal-footer">
            <div id='counter'> </div>
      </div>
    </div>

  </div>
</div>

<div id="content" class="site-content" role="main">

        <form id="fireForm" class="padded-form" action="" method="post" name="insertform" enctype="multipart/form-data">
        <div class="row">
            <h1 style='margin-left:25px'>Fire Inspection</h1>
            <!-- warning messages for invalid adds -->
          <div class="form-group col-sm-12" id='warningForm' hidden>
              <h2 style='margin-left:10px'> Warning </h2>
              <div id='warnings'> </div>
          </div>
        </div>
          <div class="row">
              <div class="form-group col-sm-4">
                  <label for="date" class="col-sm-4 col-form-label">DATE:<span style='color: red;'>*</span></label>
                    <div class="col-sm-8">
                      <input type="text" id="date" class="datepicker form-control" required>
                  </div>
              </div>
          </div>
          <input id="selectUnits" class="selectUnits" type="button" value="Add Units">
          <div id='hiddenUnits' hidden> </div>

        <div class="numberOfEntry">
          <!-- 0 should not show, but used to clone-->
          <table id="formTable0" hidden>
            <tbody>
              <thead>
                <th><strong> Unit </strong><span style='color: red;'>*</span></th>
                <th><strong> Size </strong></th>
                <th><strong> Smoke Detector </strong><span style='color: red;'>*</span></th>
                <th><strong> Second Smoke Detector </strong><span style='color: red;'>*</span></th>
                <th><strong> Notes </strong></th>
              </thead>

            <tr>
              <td>
                  <strong> <div id='unitNumber0' class='unitNumber'></div></strong>
                  <div id='delete-unit0'>
                    <span class='glyphicon glyphicon-minus' aria-hidden='true'></span>
                    Delete Unit
                  </div>
              </td>
              <td><div id='unitSize0' class='unitSize'></div></td>
              <td width=180>
                <label><input id='smokeCheckListYes0' name='smokeCheck0' class="smokeCheck" type="radio">Okay</label>
                <label><input id='smokeCheckListNo0' name='smokeCheck0'  class="smokeCheck" type="radio">Not Okay</label>
              </td>
              <td id='secondSmokeDetector0' width=192>
                <label><input id='smokeSecondCheckListYes0' name='smokeSecondCheck0' class="smokeCheck" type="radio">Okay</label>
                <label><input id='smokeSecondCheckListNo0' name='smokeSecondCheck0'  class="smokeCheck" type="radio">Not Okay</label>
              </td>
              <td>
                 <div class='notes'> <input type="text" name="note" id="note1" placeholder="Write notes here"> </div>
              </td>
            </tr>
            <tr>
              <td colspan='5'>
                <div class="row" style='width:450px;'>
                    <input type="hidden" name="MAX_FILE_SIZE" value="4294967295">
                    <div class="files0">
                        <input name="filesToUpload0[]" id="filesToUpload0" type="file"
                            class="form-control filesToUpload0" style="margin-left: 15px; border: none; -webkit-box-shadow: unset;" accept="image/*">
                    </div>
                  </div>

                <div class="row" style='margin-left:15px;'>
                  <div id="add-photo0">
                      <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                      Add Another Photo
                  </div>
                  <div id='uploadMessage0' class="uploadMessage" hidden> Please wait, files are uploading </div>
                </div>
            </tr>
            <tr>
                <!-- make td span whole row -->
                <td colspan='5'><div id='photoRow0'> <span style='margin-left: 5px;'> Photos: </span><br></td>
            </tr>
          <tbody>
          </tbody>
          </table>
        </div>
              <input type="submit" value="Submit" id="submit" style="display:none !important;">
        </form>

</div>


<script>

  var arrayOfUnits = []; // used to store units chosen, used when submitting
  var arrayOfArrayOfFileIds = []; // used to restore arrays of array of file ids based on unit number
  var numberOfFormTables = 0; // Track number of form tables created

  // There exists an issue where if you delete a unit after uploading a file and add the same unit and upload a file again, it will double upload. This array
  // Here is meant to prevent that issue
  var arrayOfFunctionFile = [];

  // Home page is used instead of the default one given in offline.js to avoid logging out issues
  Offline.options = {
      checks: {
          xhr: {
              url: "http://<?php echo $_SERVER['SERVER_NAME']; ?>/wordpress/"
          }
      }
    };

  $('#fireForm').submit(function (event){
    $('#submit').prop('disabled', true); // Disable to ensure files are uploaded before submitting
    $('#submit').css('opacity', "20%");
    var date = document.getElementById('date').value;

    for (i = 0; i < arrayOfUnits.length; i++){
      var unitNumber = arrayOfUnits[i];
      var unitSize = $('#unitSize'+unitNumber).text(); // Get unit size
      var smokeCheck = $('input[name="smokeCheck'+unitNumber+'"]:checked'); // Get which radio button checked
      var secondSmokeCheck = $('input[name="smokeSecondCheck'+unitNumber+'"]:checked'); // Get which radio button checked

      // Yes, is 1
      if (smokeCheck.attr('id') === "smokeCheckListYes"+unitNumber){
        smokeDetector = 1;
      }
      // No is 0
      else {
        smokeDetector = 0;
      }

      // Yes, is 1
      if (secondSmokeCheck.attr('id') !== undefined  && secondSmokeCheck.attr('id') === "smokeSecondCheckListYes"+unitNumber){
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
      var notes = $('#note'+unitNumber).val();
      if (notes === ""){
        notes = "None";
      }

      // Get file array
      var fileIds = JSON.stringify(arrayOfArrayOfFileIds[unitNumber]);

      event.preventDefault();
         $.ajax({
              type: 'post',
              data: {
                "date": date,
                "unitNumber": unitNumber,
                "unitSize": unitSize,
                "smokeDetector": smokeDetector,
                "smokeDetector2": smokeDetector2,
                "note": notes,
                "fileIds": fileIds,
                "requestType": "submitFire" // Used to run submission code only
              },
              url:"http://<?php echo $_SERVER['SERVER_NAME']; ?>/wordpress/updateFireInspection/", //the page containing php script
              success: function(output){
                // console.log(output);
                $('#myModal2').modal('show');
        }
      });
    }
    var timer = 0;
    function timeCounter(){
      console.log(timer);
      timer++;
      $('#counter').html(timer);
      if (timer == 30){
        document.location.href = 'http://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/fire-inspection-report/';
      }
    }
    setInterval(timeCounter, 1000);

});

  $(document).ready(function () {

      // For adding untis
      $("#selectUnits").on("click", function(){
          // Show user options
          $("#recipients").show();
          // Show pop up when clicked
          $("#selection-popup").css({
              "visibility": "visible",
              "top": $(document).scrollTop() + 'px'
          });
          $("#selection-popup").show();
      });

      // When clicking apply, save the units selected
      $('#apply').on('click', function(){
          $('#hiddenUnits').empty();
          $.each($("div.units input[type='checkbox']:checked"), function(){
              // Show submit button
              $('#submit').css('display', "");

              // If not exclude all
              if ($(this).val() !== "All"){
                $('#hiddenUnits').append($(this).val() + ', ');
                var num = $(this).val(); // Use the unit number for id
                numberOfFormTables++;

                // If the table of that unit number already exists, show a warning
                if ($("#formTable"+num).length != 0){
                  $('#warningForm').show();
                  $('#warnings').append(' - Warning! Unit ' + num + " is already added <br>");
                  $(this).prop('checked', false); // Uncheck all
                }
                else {
                  // Clone the table
                  var tableID = $('table[id^="formTable"]:first'); // get the first item of ID of formTable which is the hidden table to clone

                  //var num = parseInt(tableID.prop("id").match(/\d+/g),10)+1; // Increment the ID by 1

                  // use num as ID to differentiate tables
                  var tableDup = tableID.clone();
                  tableDup.prop('id','formTable'+num);
                  tableDup.show();

                  // Clone all the different IDs in the table
                  var unitNumber = tableDup.find('div[id^="unitNumber"]:first').prop('id', 'unitNumber'+num);
                  unitNumber.html($(this).val());
                  arrayOfUnits.push(num);

                  // Get empty Unit Size to fill it in
                  var unitSize = tableDup.find('div[id^="unitSize"]:first').prop('id', 'unitSize'+num);
                  var unitSizeValue;

                  // Use AJAX to get UNITSIZE based on UNIT NUMBER
                  $.ajax({
                      type: 'post',
                      data: {
                        "unitNumber": num,
                      },
                      url:"http://<?php echo $_SERVER['SERVER_NAME']; ?>/wordpress/getUnitSize/", //the page containing php script
                      success: function(output){
                        console.log(output);
                        unitSize.html(output.slice(-2));
                        unitSizeValue = output.slice(-2);

                        var secondSmokeDetector = tableDup.find('td[id^="secondSmokeDetector"]:first').prop('id', 'secondSmokeDetector'+num);
                        // If unit size is 2B. 3B, 4B, or TH, duplicate second smoke detector.
                        if ((num != 103 && num != 111 && num!=221) && (unitSizeValue === "2B" || unitSizeValue === "3B" || unitSizeValue === "4B" || unitSizeValue === "TH")){
                          var smokeDetectorYes = tableDup.find('input[id^="smokeSecondCheckListYes"]:first').prop('id', 'smokeSecondCheckListYes'+num);
                          smokeDetectorYes.prop('name', 'smokeSecondCheck'+num);
                          smokeDetectorYes.prop('checked', '');
                          smokeDetectorYes.prop('required', true); // make it required
                          var smokeDetectorNo = tableDup.find('input[id^="smokeSecondCheckListNo"]:first').prop('id', 'smokeSecondCheckListNo'+num);
                          smokeDetectorNo.prop('name', 'smokeSecondCheck'+num);
                          smokeDetectorNo.prop('checked', '');
                          smokeDetectorNo.prop('required', true); // make it required
                        }
                        else {
                          // Else delete it
                          secondSmokeDetector.html("None");
                        }
                    },
                  });

                  // get smoke detectors and modify ID and NAME using the hidden table
                  var smokeDetectorYes = tableDup.find('input[id^="smokeCheckListYes"]:first').prop('id', 'smokeCheckListYes'+num);
                  smokeDetectorYes.prop('name', 'smokeCheck'+num);
                  smokeDetectorYes.prop('checked', '');
                  smokeDetectorYes.prop('required', true); // make it required because had to remove default required in smokeDetectorYes0 due to focusing issue
                  var smokeDetectorNo = tableDup.find('input[id^="smokeCheckListNo"]:first').prop('id', 'smokeCheckListNo'+num);
                  smokeDetectorNo.prop('name', 'smokeCheck'+num);
                  smokeDetectorNo.prop('checked', '');
                  smokeDetectorNo.prop('required', true); // make it required
                //  alert(unitSizeValue);



                  var newNote = tableDup.find('input[id^="note"]:first').prop('id','note'+num);
                  newNote.val('');
                  var delUnit = tableDup.find('div[id^="delete-unit"]:first').prop('id', 'delete-unit'+num);

                  // Clone file files and add photo options
                  var files = tableDup.find('div[class^="files"]:first').prop('class', 'files'+num);
                  var numberOfFilesToUpload = $(".files"+num).find($("input")).length + 1;
                  var filesToUpload = tableDup.find('input[id^="filesToUpload"]:first').prop('id', 'filesToUpload'+num+'-'+numberOfFilesToUpload);
                  filesToUpload.prop('name', 'filesToUpload'+num);
                  filesToUpload.prop('class', 'form-control  filesToUpload'+num);

                  var addPhoto = tableDup.find('div[id^="add-photo"]:first').prop('id', "add-photo"+num);
                  var uploadMessage = tableDup.find('div[id^="uploadMessage"]:first').prop('id', "uploadMessage"+num);

                  // Clone preview of row
                  var photoRow = tableDup.find('div[id^="photoRow"]:first').prop('id', "photoRow"+num);

                  tableDup.appendTo('.numberOfEntry');

                  // If delete is clicked, delete, do it for every formTable created
                  $('#delete-unit'+num).on("click", function(){
                    // Because maintenance will be doing on tablet, it's safer to ask a warning
                    if (window.confirm("Are you sure you want to delete Fire Inspection of Unit "+ num)) {
                          // Delete the unit selection
                          var tableID = $('#formTable'+num); // get the id of the formTable
                          // Remove the ID from the array
                          var indexOfNum = arrayOfUnits.indexOf(num);
                          if (indexOfNum > -1){
                            arrayOfUnits.splice(indexOfNum, 1);
                          }

                          var smokeDetectorYes = tableID.find('#smokeDetectorYes'+num);
                          var smokeDetectorNo = tableID.find('#smokeDetectorNo'+num);
                          var newNote = tableID.find('#note'+num);
                          var delUnit = tableID.find('#delete-unit'+num);
                          // Clone file files and add photo options
                          var files = tableDup.find('.files'+num);
                          var numberOfFilesToUpload = $(".files"+num).find($("input")).length;
                          // Delete every single number of files to upload
                          for (var i = 0; i < numberOfFilesToUpload; i++){
                            var filesToUpload = tableDup.find('#filesToUpload'+num+'-'+numberOfFilesToUpload);
                            filesToUpload.remove();
                          }

                          var addPhoto = tableDup.find('#add-photo'+num);
                          var uploadMessage = tableDup.find('#uploadMessage'+num);

                          // Clone preview of row
                          var photoRow = tableDup.find("#photoRow"+num);

                          smokeDetectorYes.remove();
                          smokeDetectorNo.remove();
                          newNote.remove();
                          delUnit.remove();

                          addPhoto.remove();
                          uploadMessage.remove();
                          photoRow.remove();
                          arrayOfArrayOfFileIds.splice(num, 1);
                          numberOfFormTables--; // A form table is removed
                          // If numberOfFormTables is 0, hide submit
                          if (numberOfFormTables==0){
                            $('#submit').css('display', "none");
                          }
                          tableID.remove();
                    }

                  });

                /*
                For adding more photos
                */
                $('#add-photo'+num).on('click', function(){
            //        var numberOfFilesToUpload = parseInt($('.files'+num).find('input[id^="filestoUpload'+num+'"]:last').prop("id").match(/\d+/g),10)+1; // Increment the ID by 1
                    var numberOfFilesToUpload = $(".files"+num).find($("input")).length + 1;
                    var fileInput = "<input name='filesToUpload"+num+"[]' id='filesToUpload"+num+"-"+numberOfFilesToUpload+"' type='file'";
                    fileInput += "class='form-control filesToUpload"+num+"' style='margin-left: 15px; border: none; -webkit-box-shadow: unset;'>";
                    $("#filesToUpload"+num+"-"+numberOfFilesToUpload).prop('disabled', false);
                    $('.files'+num).append(fileInput);
                    if (arrayOfFunctionFile[num+"-"+numberOfFilesToUpload] === undefined){
                      arrayOfFunctionFile[num+"-"+numberOfFilesToUpload] = "defined";
                      createUploadFile("#filesToUpload"+num+"-"+numberOfFilesToUpload); // Create the upload function
                    }
                });



                var numberOfFilesToUpload = $(".files"+num).find($("input")).length;
                var idName = "#filesToUpload"+num+"-"+numberOfFilesToUpload;
                // if not defined the first time, define the function. Else when deleted again, it won't duplicate this function.
                if (arrayOfFunctionFile[num+"-"+numberOfFilesToUpload] === undefined){
                  arrayOfFunctionFile[num+"-"+numberOfFilesToUpload] = "defined";
                  createUploadFile(idName);
                }
                /*
                Ajax function for uploading fire photos to the server
                */
                function createUploadFile(idName){
                  $('body').on("change", idName, function(){
                      $(idName).prop('disabled', true);
                      $('#submit').prop('disabled', true); // Disable to ensure files are uploaded before submitting
                      $('#submit').css('opacity', "20%");
                      $('#uploadMessage'+num).show();
                      $('body').css('pointer-events', 'none');
                  //    var fileIds = [];
                      var formData = new FormData();
                      var file = $(this).prop('files')[0];
                      formData.append('requestType', 'uploadFile');
                      formData.append('filesToUpload'+num+'[]', file);
                      formData.append('unitNumber', num);

                      $.ajax({
                          type: "POST",
                          url: "/wordpress/updateFireInspection/",
                          contentType: false,
                          processData: false,
                          cache: false,
                          data: formData,
                          complete: function (ret){
                            // This large text is no longer viable and must be removed
                            var lastIndex = ret.responseText.lastIndexOf('>');
                            var lengthOfString = ret.responseText.length;
                            var newString = ret.responseText.substring(lastIndex + 1, lengthOfString).replace(/\s/g, "")
                        //    fileIds.push(newString);
                            // if not defined, make it defined
                            if (arrayOfArrayOfFileIds[num] === undefined){
                              arrayOfArrayOfFileIds[num] = [];
                              arrayOfArrayOfFileIds[num].push(newString);
                            }
                            else {
                              arrayOfArrayOfFileIds[num].push(newString);
                            }
                            // Call another AJAX to display the img
                            var formData2 = new FormData();
                            formData2.append('requestType', 'previewImage');
                            formData2.append('idFILE', newString);

                            $.ajax({
                                type: "POST",
                                url: "/wordpress/updateFireInspection/",
                                contentType: false,
                                processData: false,
                                cache: false,
                                data: formData2,
                                complete: function (ret){
                                  console.log(ret.responseText);
                                  // This large text is no longer viable and must be removed
                                  var lastIndex = ret.responseText.lastIndexOf('>');
                                  var lengthOfString = ret.responseText.length;
                                  var newString2 = ret.responseText.substring(lastIndex + 1, lengthOfString).replace(/\s/g, "");
                                  $('#photoRow'+num).append("<img src='http://" + "<?php echo $_SERVER['SERVER_NAME'];?>" + "/wp-content/themes/contango/Fire Inspection/uploads/" + newString2 + "' alt='fire_picture' width=200 height=200 style='margin-top:2px;'/> ");
                                  $('#submit').prop('disabled', false); // Disable to ensure files are uploaded before submitting
                                  $('#uploadMessage'+num).hide();
                                  $('#submit').css('opacity', "");
                                  $('body').css('pointer-events', '');
                                }
                              });
                          }
                      });
                    });
                }
              }
            }
            // Uncheck selection pop up checkboxes
            $(this).prop('checked', false);
            // Trigger clicks to floor selections if visible
            // There are 9 floors so create the functions 9 times, use let to remember the scope rather than var
            // If all checkboxes selected.
            for (let i = 1; i < 10; i++){
              if ($("#floor"+i+"units").is(":visible")) {
                $("#floor"+i).trigger("click");
              }
            }

            for (let i = 1; i <= 2; i++){
              if ($("#th"+i+"units").is(":visible")) {
                $("#th"+i).trigger("click");
              }
            }


          });
          // Hide popup
          $("#selection-popup").hide();
      });
  });

</script>
