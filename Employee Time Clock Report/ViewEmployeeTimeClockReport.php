<?php
/*
Template Name: View Employee Time Clock Report
*/
get_header();
require_once(dirname(dirname(__FILE__)) . '/connect.php');
require_once(dirname(dirname(__FILE__)) . '/tables.php');
require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
require_once(dirname(dirname(__FILE__)) . "/authorization.php");

$connection = connectPDO();
$auth = new Authorization\Authorization("Employee Time Clock Report", $roles, $connection);
?>
<link href="<?php bloginfo('template_directory'); ?>/styles/view.css" rel="stylesheet" type="text/css"/>
<script>

    $(document).ready(function () {

    //  alert("<?php bloginfo('template_directory'); ?>");

        // This is to prevent setting from start_date to a day after exit_date
        $("#startDateTimeClock").change(function(){
            if($("#startDateTimeClock").val() > $("#endDateTimeClock").val()) {
                $("#endDateTimeClock").val($("#startDateTimeClock").val());
            }
        });

      // This is to prevent setting from exit_date to a day before start_date
        $("#endDateTimeClock").change(function(){
            if($("#startDateTimeClock").val() > $("#endDateTimeClock").val()) {
                $("#endDateTimeClock").val($("#startDateTimeClock").val());
              }
        });

        $("#viewTimeClock").click(function () {
            // Collect all the staff names
            var selectedStaffNames = [];
            $('.checkmark:checked').each(function () {
              selectedStaffNames.push($(this).val());
            });
            // alert(selectedStaffNames.length);
            // Only run if startDate and endDate is given
            if ($("#endDateTimeClock").val() != null && $("#startDateTimeClock").val() != null){
              $.ajax({
                  type: "POST",
                //  crossDomain: true,
                //  dataType: "html",   //expect html to be returned
                //  url: "http://<//?php echo $_SERVER['SERVER_NAME'];?>/wordpress/genTC/",
                 url: "http://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/genTC/",
                //  url: "<?php bloginfo('template_directory'); ?>/Employee Time Clock Report/genTC.php/",
                  data: {
                      startDateTimeClock: $("#startDateTimeClock").val(),
                      endDateTimeClock: $("#endDateTimeClock").val(),
                      selectedStaffNames: selectedStaffNames
                  },
                  ///cache: false,
                  success: function (output) {
                      //alert("SAFAVID");
                      console.log(output);
                      $("#empty").empty();
                      $("#empty").append(output);
                  }
              });
          }
        });

        $("#viewSummary").click(function () {
            // Collect all the staff names
            var selectedStaffNames = [];
            var isSummary = true; // This is for checking if the summary button is clicked, used to utilize genTC
            $('.checkmark:checked').each(function () {
              selectedStaffNames.push($(this).val());
            });
            // alert(selectedStaffNames.length);
            // Only run if startDate and endDate is given
            if ($("#endDateTimeClock").val() != null && $("#startDateTimeClock").val() != null){
              $.ajax({
                  type: "POST",
                //  crossDomain: true,
                //  dataType: "html",   //expect html to be returned
                //  url: "http://<//?php echo $_SERVER['SERVER_NAME'];?>/wordpress/genTC/",
                 url: "http://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/genTC/",
                //  url: "<?php bloginfo('template_directory'); ?>/Employee Time Clock Report/genTC.php/",
                  data: {
                      startDateTimeClock: $("#startDateTimeClock").val(),
                      endDateTimeClock: $("#endDateTimeClock").val(),
                      selectedStaffNames: selectedStaffNames,
                      isSummary: isSummary
                  },
                  ///cache: false,
                  success: function (output) {
                      //alert("SAFAVID");
                      console.log(output);
                      $("#empty").empty();
                      $("#empty").append(output);
                  }
              });
          }
        });
        // If view past employee
        $('#viewPastEmployee').click(function (){
          if ($('#viewPastEmployee').val() === "Past") {

            // Change button options
            $('#currentStaff').hide();
            $('#pastStaff').show();
            $('#viewPastEmployee').val('Present');
            $('#viewPastEmployee').html("View Current Employees");

            // uncheck all present staff
           $('#currentStaff').find(':checkbox').each(function (){
                jQuery(this).attr('checked', $('#currentStaff').is(':checked'));
            });
            // uncheck all past staff
          /*  $('#pastStaff').find(':checkbox').each(function (){
                jQuery(this).prop('checked', !($('#pastStaff').is(':checked')));
            }); */

          }
          else {
            // Else if it's present staff
            // change button options
            $('#currentStaff').show();
            $('#pastStaff').hide();
            $('#viewPastEmployee').val('Past');
            $('#viewPastEmployee').html("View Past Employees");
            // uncheck all present staff
            $('#pastStaff').find(':checkbox').each(function (){
                jQuery(this).attr('checked', $('#pastStaff').is(':checked'));
            });
            /*
            $('#currentStaff').find(':checkbox').each(function (){
                jQuery(this).prop('checked', !($('#currentStaff').is(':checked')));
            });*/
          }
        });
    });


</script>
<style>

    input.datepicker{
        width: unset !important;
    }

    input#displayTable {
        margin-top: 5.5rem;
        height: 4rem !important;
    }

    #include, #timeFrame {
        height: 4rem;
        margin: 10px 0 20px 0 !important;
    }

    div#empty {
        margin-top: 3rem !important;
    }

    .staffMembers{
      display: block;
      position: relative;
      cursor: pointer;
      font-size: 17px !important;
    }

    .staffTable {
        background-color: transparent !important;
    }

    .checkmark {
      position: relative;
      height: 25px;
      width: 25px !important;
    }

    #mini-headers{
      margin-left: 15px;
      font-size: 20px !important;
    }

    .dateTable{
      text-align: left;
      /* width: 75%; */
      border:none !important;
    }

    .tablepress-class-.dateTable table, th, td {
      	border:none !important;
    }

    .staffTable {
      text-align: left;
      /* width: 75%; */
      border:none !important;
    }
    #viewTimeClock, #viewSummary{
      margin:auto;
      display:inline-block !important;
    }

    #centralButtons{
      text-align: left;
      margin-left: 20px;
    }

    #totalTime{
      font-size: 15px !important;
    }

    #defaultHeader {
    /*  font-weight: 600; */
      margin-top: 10px;
    /*  text-align:center; */
      margin-left:20px !important;
    }

    .staffMembers{
      all: unset;
   }
/*
    #clockInTable td, #clockInTable th{
      font-size: 15px;
    }

    #names{
      font-size: 18px !important;
    } */




</style>

<div id="content" class="site-content">


  <div>
      <h1 id="defaultHeader">Sign In Report</h1>
      <div>
          <table class="dateTable">
              <tr>
                  <td>
                      <label><strong>From : </strong></label>
                  </td>
                  <td>
                      <input type="text" class="datepicker" name="startDateTimeClock" id="startDateTimeClock"
                             value="<?php echo $date; ?>" tabindex="3">
                  </td>
                  <td>
                      <label><strong>To : </strong></label>
                  </td>
                  <td>
                      <input type="text" class="datepicker" name="endDateTimeClock" id="endDateTimeClock"
                             value="<?php echo $date; ?>" tabindex=4>
                  </td>
              </tr>
            </table>
          <div id="currentStaff">
            <table class="staffTable">

                <tr>
                  <h1 id="defaultHeader"> Staff Members </h1>
                  <!-- Checkbox label -->
                  <?php
                    // Get all the active staff members that have a pin
                    $sql = "SELECT
                              CONCAT(i.first_name,' ',i.last_name) AS NAME,
                              s.staff_pin AS pin
                            FROM
                              staff s
                            INNER JOIN
                              individual i ON(s.idINDIVIDUAL_STAFF = i.idINDIVIDUAL)
                            WHERE
                              i.is_staff = 1
                              AND
                              s.is_active = 1
                              AND staff_pin IS NOT NULL
                            ORDER BY `NAME` ASC";
                    $stmt = $connection->prepare($sql);
                    $stmt->execute();

                    // Create an array of such information
                    $array = $stmt->fetchAll();
                    $maxTdInARowCounter = 1;
                    //echo(sizeof($array));
                    for ($i = 0; $i < sizeof($array); $i = $i + 1){
                      // Remember the staff pin
                      $theStaffPin = $array[$i]['pin'];
                      //echo $theStaffPin;

                      $theEmployeeName = $array[$i]['NAME'];
                      // Maximum number of TD elements in a row
                      if ($maxTdInARowCounter == 5){
                        echo "</tr>";
                        $maxTdInARowCounter = 1;
                        echo "<tr>";
                      }

                      echo "<td> <label class='staffMembers'> <input type='checkbox' class='checkmark' value='$theEmployeeName'>" . $theEmployeeName . " </label> </td>";
                      $maxTdInARowCounter++;

                      // At the end of the array, add  </tr>
                      if ($i == sizeof($array) - 1){
                        echo "</tr>";
                      }
                    }
                  ?>
              </table>
          </div>
            <!-- hidden table -->
          <div id="pastStaff" style="display:none">
            <table class="staffTable">

                <tr>
                  <h1 id="defaultHeader"> Staff Members </h1>
                  <!-- Checkbox label -->
                  <?php
                    // Get all the active staff members that have a pin
                    $sql = "SELECT
                              CONCAT(i.first_name,' ',i.last_name) AS NAME,
                              s.staff_pin AS pin
                            FROM
                              staff s
                            INNER JOIN
                              individual i ON(s.idINDIVIDUAL_STAFF = i.idINDIVIDUAL)
                            WHERE
                              i.is_staff = 1
                              AND
                              s.is_active = 0
                              AND staff_pin IS NOT NULL
                            ORDER BY `NAME` ASC";
                    $stmt = $connection->prepare($sql);
                    $stmt->execute();

                    // Create an array of such information
                    $array = $stmt->fetchAll();
                    $maxTdInARowCounter = 1;
                    //echo(sizeof($array));
                    for ($i = 0; $i < sizeof($array); $i = $i + 1){
                      // Remember the staff pin
                      $theStaffPin = $array[$i]['pin'];
                      //echo $theStaffPin;

                      $theEmployeeName = $array[$i]['NAME'];
                      // Maximum number of TD elements in a row
                      if ($maxTdInARowCounter == 5){
                        echo "</tr>";
                        $maxTdInARowCounter = 1;
                        echo "<tr>";
                      }

                      echo "<td> <label class='staffMembers'> <input type='checkbox' class='checkmark' value='$theEmployeeName'>" . $theEmployeeName . " </label> </td>";
                      $maxTdInARowCounter++;

                      // At the end of the array, add  </tr>
                      if ($i == sizeof($array) - 1){
                        echo "</tr>";
                      }
                    }
                  ?>
              </table>

          </div>


            <div id="centralButtons">
              <button id='viewTimeClock' value="Details">Details</button>
              <button id='viewSummary' value="Summary">Summary</button>
              <button id='viewPastEmployee' value="Past">View Past Employees</button>
            </div>
      </div>
      <div id="empty">
      </div>
  </div>

</div>




<?php
$connectdb->close();
?>
