<?php
/*
  Template Name: Edit EmployeeTimeClock
 */
get_header();
require_once(dirname(dirname(__FILE__)) . '/connect.php');
require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
require_once(dirname(dirname(__FILE__)) . "/authorization.php");
$current_user = wp_get_current_user();

$connection = connectPDO();
$auth = new Authorization\Authorization("Employee Time Clock Report", $roles, $connection, true);

// Based on the entry_id of the page, it will insert the necesssary information needed to edit
if (isset($_GET['id'])){
    $id = $_GET['id'];

    // Obtain the necessary information from SQL Database
    $sql = "SELECT * FROM timeclock WHERE entry_id=$id";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $entry_date = $rows[0]['entry_date'];
    $signInTime = $rows[0]['entry_time'];
    $signOutTime = $rows[0]['exit_time'];
    $noteSignIn = $rows[0]['note'];
    $noteSignOut = $rows[0]['note2'];

    $theStaffPin = $rows[0]['staff_pin'];

    // get the employee_name based on staff_pin
    $stmt2 = $connection->prepare("SELECT CONCAT(i.first_name,' ', i.last_name) as name
        FROM individual i
        INNER JOIN staff s
        ON(s.idINDIVIDUAL_STAFF = i.idINDIVIDUAL)
        WHERE is_staff = 1
        AND staff_pin = ?");
    $stmt2->execute([$theStaffPin]);
    $employee_name = $stmt2->fetch();

    foreach($employee_name as $fullname){
      $name = $fullname;
    }
}

?>

<link href="<?php bloginfo('template_directory'); ?>/styles/print.min.css" rel="stylesheet" type="text/css">
<link href="<?php bloginfo('template_directory'); ?>/styles/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php bloginfo('template_directory'); ?>/styles/view.css" rel="stylesheet" type="text/css"/>


<style>
        h1{
            font-family: 'Nixie One', cursive;
            color: #333366;
            font-weight: normal;
            clear: both;
            font-size: 36px;
            margin-top: 10px;
            margin-left: 20px;
        }

        input[type="text"], input[type="time"] {
            width: auto;
        }

        #buttons{
          text-align:center;
        }

        #sign_out_note{
          margin-left: 10px;
          height: 50px;
          width: 450px;
        }

        #sign_in_note{
          margin-left: 10px;
          height: 50px;
          width: 450px;
        }

        .tablepress-id-notetable table, th, td {
            border:none !important;
            background-color:transparent !important;
        }

        #notetable{
          margin-left: 5px !important;
          border:none !important;
          background-color:transparent !important;
        }

</style>


<?php
  // Change the string into a time value
  if ($signInTime != "N/A"){
    $signTime = strtotime($signInTime);
    $signInTime = date('H:i', $signTime);
  }
 // echo $signInTime;
  if ($signOutTime != "TBA" && $signOutTime != "N/A"){
    $signTime = strtotime($signOutTime);
    $signOutTime = date('H:i', $signTime);
  }
//  echo $signOutTime;

?>
<div id="content" class="site-content" role="main">

    <section id="editTimeClock">

        <h1>Edit Sign In: <?php echo $name;?></h1>

        <form id="editTimeClock" name="editTimeClock" action="" method="post" >

            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="entry_date" class="col-sm-4 col-form-label">DATE:<span style='color: red;'>*</span></label>
                    <div class="col-sm-8">
                        <input name="entry_date" type="text" id="entry_date" class="datepicker form-control " required value="<?php echo $entry_date;?>">
                    </div>
                </div>
                <div class="form-group col-sm-4">
                  <label for="start_time" class="col-sm-4 col-form-label">SIGN IN TIME:</label>
                  <div class="col-sm-8">
                      <input name="start_time" type="time" id="start_time" value="<?php echo $signInTime;?>">
                  </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="exit_time" class="col-sm-4 col-form-label">SIGN OUT TIME:</label>
                    <div class="col-sm-8">
                      <input name="exit_time" type="time" id="exit_time" value="<?php echo $signOutTime;?>">
                    </div>
                </div>
            </div>


            <h1>Notes</h1>

            <table id="notetable">

                <th> <label><font color="black">SIGN IN NOTE</font></label> </th>
                <th> <label><font color="black">SIGN OUT NOTE</font></label></th>
                  <tr>
                    <td> <textarea name="sign_in_note" id="sign_in_note"><?php echo $noteSignIn;?></textarea> </td>
                    <td> <textarea name="sign_out_note" id="sign_out_note"><?php echo $noteSignOut;?></textarea> </td>
                  </tr>

            </table>

            <div id="buttons">
                <input type="submit" name="submit" id="submit" value="Confirm">
                <button name="delete" id="delete"> Delete </button>
            </div>
        </form>



    </section>


    <script>

    // If you want to delete an entry
    document.getElementById('delete').onclick = function(){
      var confirmation = confirm("Are you sure you want to delete this time clock entry?");
      if (confirmation == true) {
          event.preventDefault();
          $.ajax({
              type: 'post',
              data: {
                id: <?php echo $id;?>,
                "currentuser": '<?php echo $current_user->user_login;?>' // For tracking who makes an edit to timeclock report
              },
              url:"<?php bloginfo('template_directory'); ?>/Employee Time Clock Report/deleteEmployeeTimeClock.php", //the page containing php script
              success: function(output){
                //alert("HOUND");
                console.log(output);
               document.location.href = 'http://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/employee-time-clock-report/';
            },
        });
      }
  }



    // When submit button is pressed, update the values on SQL
    // USE AJAX WITH NO DATA TO CALL A HELPER FUNCTION THAT JUST UPDATES TO REDRECT PAGE
    $('#editTimeClock').submit(function (event){
      // Get the necessary values for AJAX
      var entry_date = document.getElementById('entry_date').value;
      var start_time = document.getElementById('start_time').value;
      var exit_time = document.getElementById('exit_time').value;
      var sign_in_note = document.getElementById('sign_in_note').value
      var sign_out_note = document.getElementById('sign_out_note').value;
      event.preventDefault();
      // If for some reason, someone entered an exit_time but not a start_time, do not let this RUN
      if (start_time == "" && exit_time != "") {
        // do nothing
      }
      else {
        $.ajax({
            type: 'post',
            data: {
              id: <?php echo $id;?>,
              "entry_date": entry_date,
              "start_time": start_time,
              "exit_time": exit_time,
              "sign_in_note": sign_in_note,
              "sign_out_note": sign_out_note,
              "currentuser": '<?php echo $current_user->user_login;?>' // For tracking who makes an edit to timeclock report
            },
            url:"<?php bloginfo('template_directory'); ?>/Employee Time Clock Report/updateTimeClock.php", //the page containing php script
            success: function(output){
              //alert("HOUND");
            //  alert(output);
              console.log(output);
          //    document.location.href = 'http://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/employee-time-clock-report/';
          },
      });
    }
  });

    // THIS IS TO RESTRICT SETTING AN EXIT_TIME LESSER THAN start_time
    $(document).ready(function () {
      // This is to prevent setting from start_time to a time after exit_time
      $("#start_time").change(function(){
            if($("#start_time").val() > $("#exit_time").val()) {
                $("#exit_time").val($("#start_time").val());
            }
      });

      // This is to prevent setting from exit_time to a day before start_time
      $("#exit_time").change(function(){
        // If someone wants to clear an exit time cause they didn't want to sign out for whatever reason
        if($("#exit_time").val() != ""){
          if($("#start_time").val() > $("#exit_time").val()) {
              $("#exit_time").val($("#start_time").val());
            }
        }
      });

  });

</script>
