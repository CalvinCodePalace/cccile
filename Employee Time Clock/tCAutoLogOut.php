<?php

    require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
    $connection = connectPDO();
    // Date of Creation: September 29th, 2019


    // This is a php file for autologging out employees that did not sign in and also check for those that didn't sign in
    // if it's a shutdown check
    if (isset($_POST['shutDownCheck'])){
      //  echo "SHUT DOWN CHECK";
        // Collect all the non signed out employees from the timeclock table and the current_date is less than today
        $sql = 'SELECT * FROM timeclock WHERE actiontype="in" AND entry_date<CURDATE()';
    }
    // Else do the 11:59 check
    else {
      // Collect all the non signed out employees from the timeclock table
      $sql = 'SELECT * FROM timeclock WHERE actiontype="in"';
    }
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    // Create an array of unsigned out employees to sign them out
    $array = $stmt->fetchAll();
    //  print_r($array);

      for ($i = 0; $i < sizeof($array); $i = $i + 1){
        // Remember the staff pin
        $theStaffPin = $array[$i]['staff_pin'];
        // Get the new values for the SQL database
        $new_actiontype = "out";
        $new_autologoff = 1;
        $new_exit_time = "11:00:00 pm";
        // Update the information onto the SQL database
        $updatesql = "UPDATE timeclock SET actiontype=?, auto_logoff=?, exit_time=?, note2=? where staff_pin=$theStaffPin and exit_time='TBA'";
        $stmt = $connection->prepare($updatesql);
        $stmt->execute([$new_actiontype, $new_autologoff, $new_exit_time,"Auto Logged Out"]);

        // Update the total time
        $sql = "SELECT entry_time, exit_time FROM timeclock where staff_pin=$theStaffPin and totalTime='TBA'";
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
        $sql = "UPDATE timeclock SET totalTime=? where staff_pin=$theStaffPin and totalTime='TBA'";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$total_clock_time]);
      }

      // Now check if any employees that did not sign in
      // Step 1: Get all the staff pins from staff
      $staffsql = "SELECT staff_pin, idSTAFF, work_request from staff where staff_pin IS NOT NULL and is_active=1";
      $stmt = $connection->prepare($staffsql);
      $stmt->execute();

      $staffpins = $stmt->fetchAll();
      for ($i = 0; $i < sizeof($staffpins); $i++){

                  $pin = $staffpins[$i]['staff_pin'];
          $staffID = $staffpins[$i]['idSTAFF'];
          $workRequest = $staffpins[$i]['work_request']; // Are they maintenance?

          // If it's a shutdown check, check for 7 days ago
          if (isset($_POST['shutDownCheck'])){
              // Checck for past 7 days
              for ($j = 1; $j <= 7; $j++) {
                $checksql = "SELECT * FROM timeclock where entry_date=(CURDATE() - INTERVAL $j DAY) and staff_pin=$pin";
                $dateToSet = date("Y-m-d", strtotime("-$j days"));
                $stmt = $connection->prepare($checksql);
                $stmt->execute();
                $arrayOfSigns = $stmt->fetchAll();
                // If the size of this array is 0, then we create a N/A entry
                if (sizeof($arrayOfSigns) == 0){
                  $nasql = "INSERT INTO timeclock (staff_pin, entry_time, entry_date, note, actiontype, auto_logoff, exit_time, note2, totalTime) VALUES (?,?,?,?,?,?,?,?,?)";
                  $stmt2 = $connection->prepare($nasql);
                  $stmt2->execute([$pin,'N/A',$dateToSet, "Did not clock in", "N/A", "0", "N/A", "Did not clock in", "0"]);

                  // 2020-02-06: Added code for automating sick calendar and winter schedule
                  // Only check last 2 days
                  if ($j == 1 || $j == 2){
                    // Holiday Check
                    // Source: https://www.statutoryholidays.com/ + Our own holidays between Boxing Day and New Years
                    $currentYear = date("Y");
                    $arrayOfHolidays = ["$currentYear-01-01", "$currentYear-07-01", "$currentYear-11-11", "$currentYear-12-25", "$currentYear-12-26", "$currentYear-12-27", "$currentYear-12-28", "$currentYear-12-29", "$currentYear-12-30", "$currentYear-12-31"];
                    $arrayOfHolidays[] = date('Y-m-d', strtotime("third monday of february $currentYear")); // Family Day
                    $arrayOfHolidays[] = date('Y-m-d', strtotime("first monday of august $currentYear")); // Civic Holiday
                    $arrayOfHolidays[] = date('Y-m-d', strtotime("first monday of september $currentYear")); // Labour Day
                    $arrayOfHolidays[] = date('Y-m-d', strtotime("second monday of october $currentYear")); // Thanksgiving

                    $easter = date('Y-m-d', easter_date($currentYear));
                    $arrayOfHolidays[] = date('Y-m-d', strtotime($easter . " +1 day")); // Easter Monday
                    $arrayOfHolidays[] = date('Y-m-d', strtotime($easter . " -2 day")); // Good Friday
                    $arrayOfHolidays[] = date('Y-m-d', strtotime($currentYear."-05-25, last monday")); // Victoria Day
                    print_r($arrayOfHolidays);

                    // Check if it's already in sick, if it is. Skip all.
                    $sickCheckSQL = "SELECT * from sick WHERE idSTAFF_SICK='$staffID' AND '$dateToSet' BETWEEN start_date and end_date";
                    $sickCheckstmt = $connection->prepare($sickCheckSQL);
                    $sickCheckstmt->execute();
                    $sickCheckArray = $sickCheckstmt->fetchAll();

                    // Check if it's a vacation day, if it is. Skip all.
                    $vacationCheckSQL = "SELECT * from vacation WHERE idSTAFF_VACATION='$staffID' AND '$dateToSet' BETWEEN start_date and end_date";
                    $vacationCheckstmt = $connection->prepare($vacationCheckSQL);
                    $vacationCheckstmt->execute();
                    $vacationArray = $vacationCheckstmt->fetchAll();

                    // Weekend Check (Winter Schedule Ignores this)
                    $day = date("D", strtotime($dateToSet));
                    if ($day === "Sat" || $day === "Sun"){
                      $weekend = true;
                    }
                    else {
                      $weekend = false;
                    }

                    // if not maintenance, insert sick day
                    if ($workRequest == 0){
                      // Only insert if it's already not in the sick calendar, it's not in a vacation calendar, it's not a weekend, and not a holiday
                      if (sizeof($sickCheckArray) == 0 && sizeof($vacationArray) == 0 && $weekend == false && !in_array($dateToSet, $arrayOfHolidays)){
                        $sqlSick = "INSERT INTO sick (idSTAFF_SICK, start_date, end_date, quarters, comment, day_count, auto_timeclock) VALUES (?,?,?,?,?,?,?)";
                        $stmtSick = $connection->prepare($sqlSick);
                        $stmtSick->execute(["$staffID", "$dateToSet", "$dateToSet", 1, "Time Clock Auto Sick", 1, 1]);
                      }
                    }
                    else {
                      // Only check if it's already not in the sick calendar, it's not in a vacation calendar and not a holiday
                      if (sizeof($sickCheckArray) == 0 && sizeof($vacationArray) == 0 && !in_array($dateToSet, $arrayOfHolidays)){
                        // CONSIDER WINTER SCHEDULE CASES
                        // Get max date
                        $maxsql = "SELECT max(date) as DATE from winter_schedule";
                        $stmtMax = $connection->prepare($maxsql);
                        $stmtMax->execute();
                        $maxDateArray = $stmtMax->fetchAll();
                        for ($p = 0; $p < sizeof($maxDateArray); $p++){
                          $maxdate = $maxDateArray[$p]['DATE'];
                        }
                        echo "<script> console.log('$maxdate'); </script>";
                        // If winter schedule days,check.
                        if($dateToSet <= $maxdate){
                            // Check if this staff is in winter schedule
                            $wintersql = "SELECT * from winter_shifts where staff_id='$staffID'";
                            $stmtWinter = $connection->prepare($wintersql);
                            $stmtWinter->execute();

                            $winterShiftArray = $stmtWinter->fetchAll();

                            // If rows  is not empty, we have to check
                            if (sizeof($winterShiftArray) != 0){
                              for ($w = 0; $w < sizeof($winterShiftArray); $w++){
                                $is_active = $winterShiftArray[$w]['is_active'];
                                // if active, consider
                                if ($is_active == 1){
                                  $shiftA =  $winterShiftArray[$w]['shift_a'];
                                  $shiftB =  $winterShiftArray[$w]['shift_b'];
                                  $shiftC =  $winterShiftArray[$w]['shift_c'];

                                  // Find all dates where shifts are 1
                                  $findWinterDateSQL = "SELECT * from winter_schedule WHERE date = '$dateToSet' AND " . "( ";

                                  // Based on the shift data, it will grab the appropriate SQL queries
                                  if ($shiftA == 1){
                                    $findWinterDateSQL .= "shift='A' ";
                                  }
                                  else {
                                    $findWinterDateSQL .= "(shift='A' AND light_shade=1)";
                                  }

                                  if ($shiftB == 1){
                                    $findWinterDateSQL .= "OR shift='B' ";
                                  }
                                  else {
                                    $findWinterDateSQL .= "OR (shift='B' AND light_shade=1) ";
                                  }

                                  if ($shiftC == 1){
                                    $findWinterDateSQL .= "OR shift='C' ";
                                  }
                                  else {
                                    $findWinterDateSQL .= "OR (shift='C' AND light_shade=1) ";
                                  }
                                  $findWinterDateSQL .= ")";
                                  echo $findWinterDateSQL;

                                  $stmtWinterDate = $connection->prepare($findWinterDateSQL);
                                  $stmtWinterDate->execute();
                                  // Fetch the data
                                  $winterDateArray = $stmtWinterDate->fetchAll();
                                  print_r($winterDateArray);
                                  // If rows is not empty, add sick
                                  if (sizeof($winterDateArray) != 0){
                                    $sqlSick = "INSERT INTO sick (idSTAFF_SICK, start_date, end_date, quarters, comment, day_count, auto_timeclock) VALUES (?,?,?,?,?,?,?)";
                                    $stmtSick = $connection->prepare($sqlSick);
                                    $stmtSick->execute(["$staffID", "$dateToSet", "$dateToSet", 1, "Time Clock Auto Sick", 1, 1]);
                                  }
                              }
                            }
                          }
                          // Else insert into sick if it's not a weekend
                          else {
                            if ($weekend == false){
                              $sqlSick = "INSERT INTO sick (idSTAFF_SICK, start_date, end_date, quarters, comment, day_count, auto_timeclock) VALUES (?,?,?,?,?,?,?)";
                              $stmtSick = $connection->prepare($sqlSick);
                              $stmtSick->execute(["$staffID", "$dateToSet", "$dateToSet", 1, "Time Clock Auto Sick", 1, 1]);
                            }
                          }
                        }
                        // Else if no longer winter schedule days, just add to sick
                        else {
                          $sqlSick = "INSERT INTO sick (idSTAFF_SICK, start_date, end_date, quarters, comment, day_count, auto_timeclock) VALUES (?,?,?,?,?,?,?)";
                          $stmtSick = $connection->prepare($sqlSick);
                          $stmtSick->execute(["$staffID", "$dateToSet", "$dateToSet", 1, "Time Clock Auto Sick", 1, 1]);
                        }

                      }
                    }
                  }
                }
              }
          }
          // Check for all entries that are today and have the pin.
          else {
            $checksql = "SELECT * FROM timeclock where entry_date=CURDATE() and staff_pin=$pin";
            $dateToSet = date("Y-m-d");
            $stmt = $connection->prepare($checksql);
            $stmt->execute();
            $arrayOfSigns = $stmt->fetchAll();
            // If the size of this array is 0, then we create a N/A entry
            if (sizeof($arrayOfSigns) == 0){
              $nasql = "INSERT INTO timeclock (staff_pin, entry_time, entry_date, note, actiontype, auto_logoff, exit_time, note2, totalTime) VALUES (?,?,?,?,?,?,?,?,?)";
              $stmt2 = $connection->prepare($nasql);
              $stmt2->execute([$pin,'N/A',$dateToSet, "Did not clock in", "N/A", "0", "N/A", "Did not clock in", "0"]);

              // Holiday Check
              // Source: https://www.statutoryholidays.com/ + Our own holidays between Boxing Day and New Years
              $currentYear = date("Y");
              $arrayOfHolidays = ["$currentYear-01-01", "$currentYear-07-01", "$currentYear-11-11", "$currentYear-12-25", "$currentYear-12-26", "$currentYear-12-27", "$currentYear-12-28", "$currentYear-12-29", "$currentYear-12-30", "$currentYear-12-31"];
              $arrayOfHolidays[] = date('Y-m-d', strtotime("third monday of february $currentYear")); // Family Day
              $arrayOfHolidays[] = date('Y-m-d', strtotime("first monday of august $currentYear")); // Civic Holiday
              $arrayOfHolidays[] = date('Y-m-d', strtotime("first monday of september $currentYear")); // Labour Day
              $arrayOfHolidays[] = date('Y-m-d', strtotime("second monday of october $currentYear")); // Thanksgiving

              $easter = date('Y-m-d', easter_date($currentYear));
              $arrayOfHolidays[] = date('Y-m-d', strtotime($easter . " +1 day")); // Easter Monday
              $arrayOfHolidays[] = date('Y-m-d', strtotime($easter . " -2 day")); // Good Friday
              $arrayOfHolidays[] = date('Y-m-d', strtotime($currentYear."-05-25, last monday")); // Victoria Day
              print_r($arrayOfHolidays);

              // Check if it's already in sick, if it is. Skip all.
              $sickCheckSQL = "SELECT * from sick WHERE idSTAFF_SICK='$staffID' AND '$dateToSet' BETWEEN start_date and end_date";
              $sickCheckstmt = $connection->prepare($sickCheckSQL);
              $sickCheckstmt->execute();
              $sickCheckArray = $sickCheckstmt->fetchAll();

              // Check if it's a vacation day, if it is. Skip all.
              $vacationCheckSQL = "SELECT * from vacation WHERE idSTAFF_VACATION='$staffID' AND '$dateToSet' BETWEEN start_date and end_date";
              $vacationCheckstmt = $connection->prepare($vacationCheckSQL);
              $vacationCheckstmt->execute();
              $vacationArray = $vacationCheckstmt->fetchAll();

              // Weekend Check (Winter Schedule Ignores this)
              $day = date("D", strtotime($dateToSet));
              if ($day === "Sat" || $day === "Sun"){
                $weekend = true;
              }
              else {
                $weekend = false;
              }


              // 2020-02-06: Added code for automating sick calendar and winter schedule
              // if not maintenance, insert sick day
              if ($workRequest == 0){
                // Only insert if it's already not in the sick calendar, it's not in a vacation calendar, it's not a weekend, and not a holiday
                if (sizeof($sickCheckArray) == 0 && sizeof($vacationArray) == 0 && $weekend == false && !in_array($dateToSet, $arrayOfHolidays)){
                  $sqlSick = "INSERT INTO sick (idSTAFF_SICK, start_date, end_date, quarters, comment, day_count, auto_timeclock) VALUES (?,?,?,?,?,?,?)";
                  $stmtSick = $connection->prepare($sqlSick);
                  $stmtSick->execute(["$staffID", "$dateToSet", "$dateToSet", 1, "Time Clock Auto Sick", 1, 1]);
                }
              }
              else {
                // Only check if it's already not in the sick calendar, it's not in a vacation calendar and not a holiday
                if (sizeof($sickCheckArray) == 0 && sizeof($vacationArray) == 0 && !in_array($dateToSet, $arrayOfHolidays)){
                  // CONSIDER WINTER SCHEDULE CASES
                  // Get max date
                  $maxsql = "SELECT max(date) as DATE from winter_schedule";
                  $stmtMax = $connection->prepare($maxsql);
                  $stmtMax->execute();
                  $maxDateArray = $stmtMax->fetchAll();
                  for ($p = 0; $p < sizeof($maxDateArray); $p++){
                    $maxdate = $maxDateArray[$p]['DATE'];
                  }
                  echo "<script> console.log('$maxdate'); </script>";
                  // If winter schedule days,check.
                  if($dateToSet <= $maxdate){
                      // Check if this staff is in winter schedule
                      $wintersql = "SELECT * from winter_shifts where staff_id='$staffID'";
                      $stmtWinter = $connection->prepare($wintersql);
                      $stmtWinter->execute();

                      $winterShiftArray = $stmtWinter->fetchAll();

                      // If rows  is not empty, we have to check
                      if (sizeof($winterShiftArray) != 0){
                        for ($w = 0; $w < sizeof($winterShiftArray); $w++){
                          $is_active = $winterShiftArray[$w]['is_active'];
                          // if active, consider
                          if ($is_active == 1){
                            $shiftA =  $winterShiftArray[$w]['shift_a'];
                            $shiftB =  $winterShiftArray[$w]['shift_b'];
                            $shiftC =  $winterShiftArray[$w]['shift_c'];

                            // Find all dates where shifts are 1
                            $findWinterDateSQL = "SELECT * from winter_schedule WHERE date = '$dateToSet' AND " . "( ";

                            // Based on the shift data, it will grab the appropriate SQL queries
                            if ($shiftA == 1){
                              $findWinterDateSQL .= "shift='A' ";
                            }
                            else {
                              $findWinterDateSQL .= "(shift='A' AND light_shade=1)";
                            }

                            if ($shiftB == 1){
                              $findWinterDateSQL .= "OR shift='B' ";
                            }
                            else {
                              $findWinterDateSQL .= "OR (shift='B' AND light_shade=1) ";
                            }

                            if ($shiftC == 1){
                              $findWinterDateSQL .= "OR shift='C' ";
                            }
                            else {
                              $findWinterDateSQL .= "OR (shift='C' AND light_shade=1) ";
                            }
                            $findWinterDateSQL .= ")";
                            echo $findWinterDateSQL;

                            $stmtWinterDate = $connection->prepare($findWinterDateSQL);
                            $stmtWinterDate->execute();
                            // Fetch the data
                            $winterDateArray = $stmtWinterDate->fetchAll();
                            print_r($winterDateArray);
                            // If rows is not empty, add sick
                            if (sizeof($winterDateArray) != 0){
                              $sqlSick = "INSERT INTO sick (idSTAFF_SICK, start_date, end_date, quarters, comment, day_count, auto_timeclock) VALUES (?,?,?,?,?,?,?)";
                              $stmtSick = $connection->prepare($sqlSick);
                              $stmtSick->execute(["$staffID", "$dateToSet", "$dateToSet", 1, "Time Clock Auto Sick", 1, 1]);
                            }
                        }
                      }
                    }
                    // Else insert into sick if it's not a weekend
                    else {
                      if ($weekend == false){
                        $sqlSick = "INSERT INTO sick (idSTAFF_SICK, start_date, end_date, quarters, comment, day_count, auto_timeclock) VALUES (?,?,?,?,?,?,?)";
                        $stmtSick = $connection->prepare($sqlSick);
                        $stmtSick->execute(["$staffID", "$dateToSet", "$dateToSet", 1, "Time Clock Auto Sick", 1, 1]);
                      }
                    }
                  }
                  // Else if no longer winter schedule days, just add to sick
                  else {
                    $sqlSick = "INSERT INTO sick (idSTAFF_SICK, start_date, end_date, quarters, comment, day_count, auto_timeclock) VALUES (?,?,?,?,?,?,?)";
                    $stmtSick = $connection->prepare($sqlSick);
                    $stmtSick->execute(["$staffID", "$dateToSet", "$dateToSet", 1, "Time Clock Auto Sick", 1, 1]);
                  }

                }
              }
            }
          }


      }
  ?>
