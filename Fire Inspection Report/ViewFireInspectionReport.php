<?php
/*
Template Name: View Fire Inspection Report
*/
get_header();
require_once(dirname(dirname(__FILE__)) . '/connect.php');
require_once(dirname(dirname(__FILE__)) . '/tables.php');
require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
require_once(dirname(dirname(__FILE__)) . "/authorization.php");
require_once(dirname(dirname(__FILE__)) . "/Unit/selectUnitsForFireInspection.php");

$connection = connectPDO();
$auth = new Authorization\Authorization("Fire Inspection Report", $roles, $connection);
?>
<link href="<?php bloginfo('template_directory'); ?>/styles/view.css" rel="stylesheet" type="text/css"/>

<style>

    input.datepicker{
        width: unset !important;
    }

    input#displayTable {
        margin-top: 5.5rem;
        height: 4rem !important;
    }

    div#empty {
        margin-top: 3rem !important;
    }

  .dateTable{
      text-align: left;
      /* width: 75%; */
      border:none !important;
    }

    .tablepress-class-.dateTable table, th, td {
      	border:none !important;
    }


    #centralButtons{
      text-align: left;
      margin-left: 20px;
    }


    #defaultHeader {
    /*  font-weight: 600; */
      margin-top: 10px;
    /*  text-align:center; */
      margin-left:20px !important;
    }

    .selectUnits{
      margin-left: 20px !important;
    }




</style>

<div id="content" class="site-content">

  <div>
      <h1 id="defaultHeader">Fire Inspection Report</h1>
      <div>
          <table class="dateTable">
              <tr>
                  <td>
                      <label><strong>From : </strong></label>
                  </td>
                  <td>
                      <input type="text" class="datepicker" name="startDate" id="startDate"
                             value="<?php echo $date; ?>" tabindex="3">
                  </td>
                  <td>
                      <label><strong>To : </strong></label>
                  </td>
                  <td>
                      <input type="text" class="datepicker" name="endDate" id="endDate"
                             value="<?php echo $date; ?>" tabindex=4>
                  </td>
              </tr>
            </table>

            <input id="selectUnits" class="selectUnits" type="button" value="Select Units">
            <div id='hiddenUnits' hidden> </div>
            <label><input type='checkbox' id='seeAllUnits'>See All Units</label>

            <div id="centralButtons">
              <button id='viewFireInspectionReport' value="View">View</button>
            </div>
      </div>
      <div id="empty">
      </div>
  </div>

</div>

<script>

    $(document).ready(function () {
        var selectedUnits = [];
        // This is to prevent setting from start_date to a date after end_date
        $("#startDate").change(function(){
            if($("#startDate").val() > $("#endDate").val()) {
                $("#endDate").val($("#startDate").val());
            }
        });

        // This is to prevent setting from end_date to a date after start_date
        $("#endDate").change(function(){
            if($("#startDate").val() > $("#endDate").val()) {
              $("#endDate").val($("#startDate").val());
            }
        });

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
            selectedUnits = [];
            $.each($("div.units input[type='checkbox']:checked"), function(){
              if ($(this).val() !== 'All'){
                $('#hiddenUnits').append($(this).val() + ", ");
                selectedUnits.push($(this).val());
              }
          //    $('#hiddenUnits').show();
            });
            $('#selection-popup').hide();
        });

        $("#viewFireInspectionReport").click(function () {
            // Only run if startDate and endDate is given
            if ($("#endDate").val() != '' && $("#startDate").val() != ''){
              // Check if see all units is selected
              var checkedAll = 0; // 0 if not checked, 1 if checked
              if ($('#seeAllUnits').is(":checked") == true){
                checkedAll = 1;
              }
              $.ajax({
                 type: "POST",
                 url: "http://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/genFireInspectionReport/",

                  data: {
                      startDate: $("#startDate").val(),
                      endDate: $("#endDate").val(),
                      "selectedUnits": selectedUnits,
                      "checkedAll": checkedAll
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
          // Otherwise, warn user they need to enter a date
          else {
            alert('Selecting a date is required');
          }
        });

    });


</script>

<?php
$connectdb->close();
?>
