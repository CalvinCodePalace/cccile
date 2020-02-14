<?php
/*
Template Name: Employee Time Clock
*/
get_header();//if tken out, css overhaul required
require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
// require_once(dirname(dirname(__FILE__)) . "/authorization.php"); see how this works

//try to retrieve super user from database, for now hardcode it
$superuser = ""; // RETRACTED
$connection = connectPDO();
$current_user = wp_get_current_user()->user_login;

// $pin = $_POST['pin'];
// echo $pin;

?>
<link href="<?php bloginfo('template_directory'); ?>/styles/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php bloginfo('template_directory'); ?>/styles/view.css" rel="stylesheet" type="text/css">
<link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous">

<style>

#head{
    text-align: center;
    font-size: 6vw;
}
#head2{
  font-size: 2vw;
  text-align: center;
}

.punch-in-sect{
    padding-top: 10px;
    margin: auto;
    width: 50%;
}

.credentials{
    padding-top: 3em;
    margin: 0 auto;
    width: 100%;
}

/* .input_label{
    font-size: 23pt;
    margin-top: 1px;

} */

#empl_pin_field{
    margin: 0 auto;
    margin-top: 12px;
    font-size: 4vw;
    height: auto;
    width: 50%
}

#pin_description{
  margin-top: 12px;
  width: 25%
}

.date_time_div{
    margin:auto;
    width: 50%;
    height: 50%;
    text-align: center;
    display:block;
}

#timestamp{
    font-size: 5vw;
}

#date{
    font-size: 5vw;
}

.modal-header{
    justify-content: start;
}

@media only screen and (min-width: 1100px) and (min-height: 900px){
    .modal-dialog{
        max-width: 80vw;
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* overflow: auto; */
    }

    .modal-content{
        max-width: 80vw;
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* overflow: auto; */
    }

}

@media only screen and (min-width: 1100px) and (min-height: 700px){
    .modal-dialog{
        max-width: 60vw;
        min-height: 60vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* overflow: auto; */
    }

    .modal-content{
        max-width: 60vw;
        min-height: 60vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* overflow: auto; */
    }

}

/* @media only screen and (min-width: 1100px){
    .modal-content{
        width: 900px;
        min-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: auto;
    }

} */

#popup-content{
     text-align: center;
}

#employee_name{
    font-size: 6vw;
}

#modal-time-info{
    text-align: center;
    margin: 0 auto;
    /* font-size: 5vw;  */
}

#modal-time-info>h2{
    font-size: 3vw;
}

#shift-info {
    font-size: 5vw;
    margin: 0 auto;
}

#shift-info>h1{
    /* display: inline; */
    margin: 0 auto;
    font-size: 5vw;
}

#start {
    display: none;
    color: green;
}

#end {
    display: none;
    color: red;
}

#submit_pin{
    display: block;
    margin: 0.25em auto;
    font-size: 3vw;
}

#submit_button{
    display: block;
    margin: 1em auto;
    font-size: 3vw;
}

#cancel{
  display: block;
  margin: 1em auto;
  font-size: 3vw;
}

form {
    width: 100% !important;
}

#note_field {
    height: 60px;
    font-size: 3vw;
    margin: 0 auto;
    width: 100%;
}



</style>

<body>

<?php
/* The timeclock is only available to one user known as timeclockuser where only one user is allowed to access the database of sign ins so that no other staff
can sign in through the website and can only do it from one station */
if (strcmp($current_user, $superuser)>=0)
{ ?>

<!-- <h1> you are authorized </h1> -->
<div id = content class = site-content role = main>
    <section id = home-page-header>
        <h1 id= head> Time Clock </h1>
    </section>
    <hr>
    <section id = punch-in-sect>
    <!-- do post, and submit to database on post, choose to do data submission on php or jquery -->
        <form id=home-page-form>
            <div class = "row credentials">
                <!-- <label class = "input_label col-sm-4">Employee Pin: </label> -->
                <p id=pin_description> <h2 id=head2> Please enter your Employee Pin:  </h2> </p>
                <input id = "empl_pin_field" class = "col-sm-6 form-control" name = pin type = "text submit">
            </div>
            <button type="button submit" class="btn btn-success" id="submit_pin" name = pin>Submit</button>
        </form>
        <div class = date_time_div>
            <div id = date></div>
            <div id = timestamp class = clock></div>
        </div>
    </section>
</div>

<!-- Popup Modal, on close set focus back to employee pin field -->
<div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-labelledby="popupModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" id="popupModalLabel">Your Shift</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body" id = popup-content>
        <h1 id = employee_name> NO INTERNET CONNECTION. PLEASE WAIT OR CONTACT HONG FOR TROUBLESHOOTING. DO NOT SUBMIT. </h1>
        <span id = modal-time-info> <h2 id=date-info> DATE </h2> <h2 id = time-info> TIME </h2> </span>
        <!-- <br /> -->
        <span id = shift-info> <h1 id=start> IN </h1> <h1 id = end> OUT </h1> </span>
      </div>
      <div class="modal-footer">
        <form id =note-entry>
            <!-- when submit note or press enter, trigger this attribute in jquery data-dismiss=modal -->
            <!-- Add error checks for empty submissions -->
            <input placeholder = "Add Note Here" type = "text submit" id = note_field name = note_send>
            <button name="note_send" type="button submit" class="btn btn-success btn-block" id="submit_button" data-dismiss="modal">Submit</button>
            <button class="btn btn-warning btn-block" id="cancel" data-dismiss="modal">Cancel</button>

        </form>
    </div>
    </div>
  </div>
</div>

<?php
}
// Unauthorized, not logged into timeclockuser
else{
    echo "<div id = content class = site-content role = main>";
    echo "<p style='font-size: 20px'> Please clock in at the appropriate station. </p>" ;
    echo "</div>";
}?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js">
</script>

<script>
    var pin, date, time, actiontype = "";
    $(document).ready(function() {
        // Get the employee pin submitted
        $('#empl_pin_field').focus();
        // Blogin
        $("a").attr("href", "<?php bloginfo('url'); ?>/employee-time-clock/");
        displayClock();
        setInterval(displayClock, 1000);
     });

    // Display the current time by setting the timestamp and date to of current time
    function displayClock(){
    //  $('#timestamp').html("TROLLING");
        $('#date').html(moment().format("MMM DD YYYY"));
        $('#timestamp').html(moment().format("h:mm:ss a"));
    }

    // The pop up display information when submitted employee ID
    $("#home-page-form").submit(function (e){
        e.preventDefault();
        pin = $('#empl_pin_field').val();
        // If an empty pin is entered, consider it invalid.
        if (!pin){
          document.getElementById("employee_name").style.display = "none";
          document.getElementById("modal-time-info").style.display = "none";
          document.getElementById("note_field").style.display = "none";
          document.getElementById("note_field").disabled = true;
          $('#end').css("display", "block");
          $('#end').html("Invalid Pin");
          document.getElementById("submit_button").style.display = "none";
        }


        // console.log(pin);
        date =  moment().format("MMM DD YYYY");
        $('#date-info').html(date);
        // console.log(date);
        time = moment().format("h:mm:ss a");
        $('#time-info').html(time);
        // console.log(time);
        $.ajax({
            url:"<?php bloginfo('template_directory'); ?>/Employee Time Clock/tCHelper.php", //the page containing php script,
            data: {pin},
            type: 'post',
            success: function(output) {
              // Using JSON to obtain the necessary information to display
                var obj = (JSON.parse(output));
                // If the employee name does not exist, stop everything and redirect back to the page
                if (!obj.name) {
                  // Hide the options to submit anything and redirect them
                  document.getElementById("employee_name").style.display = "none";
                  document.getElementById("modal-time-info").style.display = "none";
                  document.getElementById("note_field").style.display = "none";
                  document.getElementById("note_field").disabled = true;
                  $('#end').css("display", "block");
                  $('#end').html("Invalid Pin");
                  document.getElementById("submit_button").style.display = "none";
                }
                else {
                //console.log(obj.name);
                  // Set the pop up employee name to the name on JSON
                  $('#employee_name').html(obj.name);
                  console.log(obj.action == "clock in");

                  // Action is based on whether clocking in or outs
                  if(obj.action === "clock in"){
                    actiontype = "in";
                    $('#start').css("display", "block");
                    $('#start').html("Clock in");
                 }
                 else if (obj.action === "clock out"){
                    actiontype = "out";
                      $('#end').css("display", "block");
                    $('#end').html("Clock out");
                 }
              }
            }
        });
        $('#popupModal').modal({
            backdrop: 'static',
            keyboard: true,
            show: true
        });
        // This is for if the employee enters a note
        $('#popupModal').on('shown.bs.modal', function () {
            $('#note_field').trigger('focus');
        });

    });
    // Submit the data if the submit button is pressed, cancel button will do nothing.
    document.getElementById("submit_button").onclick = function () {
      $('#popupModal').on('hidden.bs.modal', function () {
          $('#empl_pin_field').css("background-color",  "lightgreen");
          // If note was not hidden
          var note = $('#note_field').val();
          //If there's no note
          if (note == "") {
            note="none";
          }
          else{console.log("there is a note");}
          $.ajax({
              url:"<?php bloginfo('template_directory'); ?>/Employee Time Clock/tCHelper.php", //the page containing php script
              data: {pin, time, note, actiontype},
              type: 'post',
              // Not needed
            /*  success: function(output) {
                  console.log(output);
                  var obj = (JSON.parse(output));
                  // // console.log(obj.name);
                  $('#employee_name').html(obj.name);
                  // console.log(obj.action == "clock in");
                  if(obj.action === "clock in"){
                      actiontype = "in";
                  //    alert("in");
                      $('#start').css("display", "block");
                       $('#start').html("clock in"); //either display time or action --> ask Hong about this
                 }
                 else{
                      actiontype = "out";
                  //    alert("out");
                       $('#end').css("display", "block");
                       $('#end').html("clock out");  //either display time or action --> ask Hong about this
                }
              } */
            });
            // console.log("this is your note " +note);
            location.reload(true);

      });
  }

  // When clicking the submit pin button, redisplay previous data
  document.getElementById("submit_pin").onclick = function(){
    document.getElementById("employee_name").style.display = "block";
    document.getElementById("modal-time-info").style.display = "block";
    document.getElementById("note_field").style.display = "block";
    document.getElementById("note_field").disabled = false;
    document.getElementById("submit_button").style.display = "block";
    $('#end').html('');
    $('#start').html('');
  }

  // 11:59 feature, if it's 11:59
 $(document).ready(function () {

    // Prevent the user from entering a space in their pin
      $('input#empl_pin_field').keypress(function (e){
          if(e.which === 32){
              return false;
          }
      });

      // If the system let's say had a power outage for more than a day, this will go through any entries before today and change all clockins to outs at 11:59 PM
      var shutDownCheck = true;
      $.ajax({
          type: 'post',
          data: {
            "shutDownCheck": shutDownCheck
          },
          url:"<?php bloginfo('template_directory'); ?>/Employee Time Clock/tCAutoLogOut.php", //the page containing php script
          success: function(output){
      //      alert("SHUTDOWN CHECK");
            console.log(output);
          }
        });


      var today = new Date();
      // This sets the time to be 11:59 PM
      var millisTill1159 = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 23, 00, 0, 0) - today;
      // If the required time left from today to 11:59 PM is negative (meaning after 11:59 PM), put it on a 24 hour clock timer.
      if (millisTill1159 < 0){
         millisTill1159 += 86400000;
      }
      // Run the command to change all check ins to auto sign outs AND also check if any employee did not SIGN IN
      setTimeout(function() {
          //  alert("IT'S TIME");
            $.ajax({
                type: 'post',
                url:"<?php bloginfo('template_directory'); ?>/Employee Time Clock/tCAutoLogOut.php", //the page containing php script
                success: function(output){
                //  alert("11:59 CHECK");
                  console.log(output);
                }
          });
        }, millisTill1159);

});


</script>

</body>
