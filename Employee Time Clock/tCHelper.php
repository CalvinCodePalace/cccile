<?php

    require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
    $connection = connectPDO();

    // This is a helper php file for homepage

    function getEmployeeName($connection, $staffPin){
        $stmt = $connection->prepare("SELECT CONCAT(i.first_name,' ', i.last_name) as name
            FROM individual i
            INNER JOIN staff s
            ON(s.idINDIVIDUAL_STAFF = i.idINDIVIDUAL)
            WHERE is_staff = 1
            AND staff_pin = ?");
        $stmt->execute([$staffPin]);
        $employee_name = $stmt->fetch();

        return $employee_name;
    }
    //this function is used to get the shift(s) of an employee for current date
    function getEmployeeShift($connection, $staffPin, $current_date, $exit_time)
    {
        //retrieves name, time and type of entry
        $stmt = $connection->prepare("SELECT t.entry_time, t.entry_date, t.note, t.actiontype
            FROM timeclock t
            INNER JOIN staff s
            ON (t.staff_pin = s.staff_pin)
            WHERE t.entry_date = ?
            AND t.staff_pin = ?
            AND t.exit_time = ?");
        $stmt->execute([$current_date, $staffPin, $exit_time]);
        $employee_shift = $stmt->fetchAll();

        return $employee_shift;
    }
    // Inserts the employee clock in information into the timeclock database
    function insertEmployeeShift($connection, $staffPin, $etime, $edate, $note, $actiontype, $autologoff, $exit_time, $note2, $totalTime)
    {
      $sql = "INSERT INTO timeclock (staff_pin, entry_time, entry_date, note, actiontype, auto_logoff, exit_time, note2, totalTime) VALUES (?,?,?,?,?,?,?,?,?)";
      $stmt= $connection->prepare($sql);
      $stmt->execute([$staffPin,$etime,$edate,$note,$actiontype,$autologoff, $exit_time, $note2, $totalTime]);
    }


  if(isset($_POST['pin']) && !empty($_POST['pin'])){

    $current_date = date("Y-m-d");
    $current_time = date("h:i:s a");
    $staffPin = $_POST['pin'];

      if(isset($_POST['actiontype']))
      {
        $note = $_POST['note'];
        $actiontype = $_POST['actiontype'];
        $autologoff = 0;
        // $autologoff = $_POST['autologoff'];\
        // If the actiontype was in, then only put current_time on entry_time
        if (strcmp($actiontype, "in") == 0){
            insertEmployeeShift($connection, $staffPin, $current_time, $current_date, $note, $actiontype, $autologoff, 'TBA', 'TBA', 'TBA');
        }
        // Else if actiontype was out, then put it on exit_time
        else if (strcmp($actiontype, "out") == 0) {
            // Update the exit time's time on the TBA exit_time data
            $sql = "UPDATE timeclock SET exit_time=?, actiontype=? where staff_pin=$staffPin and exit_time='TBA'";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$current_time, $actiontype]);

            // Add the endnote
            $sql = "UPDATE timeclock SET note2=?, actiontype=? where staff_pin=$staffPin and note2='TBA'";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$note, $actiontype]);

            // Update the total time
            $sql = "SELECT entry_time, exit_time FROM timeclock where staff_pin=$staffPin and totalTime='TBA'";
            $stmt = $connection->prepare($sql);
            $stmt->execute();

            $timeArray = $stmt->fetchAll();
            $signInTime = $timeArray[0]['entry_time'];
            $signOutTime = $timeArray[0]['exit_time'];

            $start_time = new DateTime($signInTime);
            $end_time = new DateTime($signOutTime);

            $time_difference = $end_time->diff($start_time);
            $hour_difference = $time_difference->h;
            $minute_difference = $time_difference->i;

            $hour_value = (int)$hour_difference;
            $minute_value = (int)$minute_difference;
            // Do extensive rounding on the minutes
            // If between 0 to 7 minutes, 0 minutes
            if ($minute_value >= 0 && $minute_value <= 7){
              $minute_value = 0;
            }
            // Else if between 8 to 22 minutes, 0.25
            else if ($minute_value >= 8 && $minute_value <= 22){
              $minute_value = 25;
            }
            // Else if between 23 to 37 minutes, 0.5
            else if ($minute_value >= 23 && $minute_value <= 37){
              $minute_value = 50;
            }
            // Else if between 38 to 52 minutes, 0.75
            else if ($minute_value >= 38 && $minute_value <= 52){
              $minute_value = 75;
            }
            // Else if between 53 to 59 minutes, add 1 to the hour
            else if ($minute_value >= 53 && $minute_value <= 59){
              $minute_value = 0;
              $hour_value += 1;
            }
            $total_clock_time = $hour_value . "." . $minute_value;

            // Update
            $sql = "UPDATE timeclock SET totalTime=? where staff_pin=$staffPin and totalTime='TBA'";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$total_clock_time]);

       }
      //  $autologoff = 0;
      //  insertEmployeeShift($connection, $staffPin, $current_time, $current_date, $note, $actiontype, $autologoff);
        echo("Success");
      }
      else
      {
        $employee_name = getEmployeeName($connection, $staffPin, $current_date)['name'];
        $shift_log = getEmployeeShift($connection, $staffPin, $current_date, 'TBA');
        $decided_entry = "";
        $shift_log_size = sizeof($shift_log);
        $timeclock = array();

        // echo ("this is your log size:" .$shift_log_size);
        // echo ("<br>previous type of submission: " . $shift_log[0]['type']);
        // echo ("<br>current modulo 2 calc: " . $shift_log_size%2);
        // echo ("<br>comparison atm".strcmp($shift_log[0]['type'], 'in'));

        // echo ($shift_log_size%2!=0 && strcmp($shift_log[0]['type'], 'in')==0);

        //condition for no name associated with pin needed
        // send information back to AJAX
        if ($shift_log_size%2==0){
            $decided_entry = "clock in";
        }
        else if ($shift_log_size%2!=0 && strcmp($shift_log[$shift_log_size-1]['actiontype'], 'in')==0)
        {
            $decided_entry = "clock out";
        }
        $timeclock = [
            'name' => $employee_name,
            'action' => $decided_entry
        ];
        echo json_encode ($timeclock);
      }

  }

?>
