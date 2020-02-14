<?php

    require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
    $connection = connectPDO();

// Date of creation: 2020-10-01
// Will update the time clock once edited changes are confirmed

// Based on the entry_id of the page, it will insert the necesssary information needed to edit
if (isset($_POST['id'])){
    $id = $_POST['id'];
    // Obtain the necessary information from SQL Database
    $sql = "SELECT * FROM timeclock WHERE entry_id=$id";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    // This is for tracking history where it remembers the old values
    $old_values = array();
    foreach($rows as $contents){
      $old_values[] = 'entry_date' . "::" . $contents['entry_date'];
      $old_values[] = 'entry_time' . "::" . $contents['entry_time'];
      $old_values[] = 'exit_time' . "::" . $contents['exit_time'];
      $old_values[] = 'note' . "::" . $contents['note'];
      $old_values[] = 'note2' . "::" . $contents['note2'];
      $old_values[] = 'actiontype' . "::" . $contents['actiontype'];
    }
    $old_value_string = "";
    // Add to old_values string
    foreach($old_values as $contents){
      $old_value_string.= $contents . "|||";
    }
    $current_user = $_POST['currentuser'];
  //  echo $old_value_string;

  //  print_r($old_values);


    if(isset($_POST['entry_date'])) {

        // Get the post data
        $new_entry_date = $_POST['entry_date'];

        // Convert from 24 hour clock to 12 hour clock for start and exit time
        if ($_POST['start_time'] != "TBA" && $_POST['start_time'] != "") {
          $date_start_time = new DateTime($_POST['start_time']);
          $new_entry_time = $date_start_time->format('h:i:s a');

          if ($_POST['exit_time'] != "TBA" && $_POST['exit_time'] != "") {
            $date_exit_time = new DateTime($_POST['exit_time']);
            $new_exit_time = $date_exit_time->format('h:i:s a');
          }
          else {
            $new_exit_time = "TBA";
          }
        }
        // Else, this is considered a non-signed in day
        else {
          $new_entry_time = "N/A";
          $new_exit_time = "N/A";
        }
      //  echo $new_exit_time;
        $new_sign_in_note = $_POST['sign_in_note'];
        $new_sign_out_note = $_POST['sign_out_note'];

        // If an exit time is entered, consider it out, else, still in
        if ($new_exit_time === 'TBA'){
          $new_action_type = 'in';
        }
        // Else if the entry time is N/A
        else if ($new_entry_time === 'N/A') {
          $new_action_type = 'N/A';
        }
        else {
          $new_action_type = 'out';
          // And if note for sign out is TBA, change it to none
          if ($new_sign_out_note === 'TBA'){
            $new_sign_out_note = "none";
          }
        }


        // Update the values based on given entry_id
        $sql = "UPDATE timeclock SET entry_date=?, entry_time=?, exit_time=?, note=?, note2=?, actiontype=?, edited=? WHERE entry_id=$id";
        $historysql = "UPDATE timeclock SET entry_date=$new_entry_date, entry_time=$new_entry_time, exit_time=$new_exit_time, note=$new_sign_in_note, note2=$new_sign_out_note, actiontype=$new_action_type, edited='*' WHERE entry_id=$id";

        // Track the data (trackedQuery not working) into the history
        $trackedsql = "INSERT INTO tracking (date, time, user, action, affected_ids, old_values, new_values, table_name, sql_string) VALUES(?,?,?,?,?,?,?,?,?)";
        $trackedstmt = $connection->prepare($trackedsql);
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');

        $new_value_string = 'entry_date::' . $new_entry_date . "|||entry_time::" . $new_entry_time . "|||exit_time::" .  $new_exit_time . "|||note::" .  $new_sign_in_note . "|||note2::" .  $new_sign_out_note . "|||actiontype::" .  $new_action_type;
    //    echo $new_value_string;


        $trackedstmt->execute([$currentDate,$currentTime, $current_user, 'update', $id,$old_value_string, $new_value_string, 'timeclock', $historysql]);
        $stmt = $connection->prepare($sql);
        $stmt->execute([$new_entry_date, $new_entry_time, $new_exit_time, $new_sign_in_note, $new_sign_out_note, $new_action_type, '*']);

        // If $new_action_type is N/A, update totalTime to 0
        // Update the values based on given entry_id
        if ($new_action_type === "N/A") {
          // This is for tracking history where it remembers the old total time
          $old_total = "";
          foreach($rows as $contents){
            $old_total = 'totalTime' . "::" . $contents['totalTime'];
          }
          $new_total = 'totalTime::0';

          $trackedsql = "INSERT INTO tracking (date, time, user, action, affected_ids, old_values, new_values, table_name, sql_string) VALUES(?,?,?,?,?,?,?,?,?)";
          $trackedstmt = $connection->prepare($trackedsql);
          $currentDate = date('Y-m-d');
          $currentTime = date('H:i:s');

          $totalHistorySql = "UPDATE timeclock SET totalTime='0' WHERE entry_id=$id";

          $trackedstmt->execute([$currentDate,$currentTime, $current_user, 'update', $id,$old_total, $new_total, 'timeclock', $totalHistorySql]);

          $sql = "UPDATE timeclock SET totalTime=? WHERE entry_id=$id";
          $stmt = $connection->prepare($sql);
          $stmt->execute(['0']);
        }
        else if ($new_action_type === "in") {
          // This is for tracking history where it remembers the old total time
          $old_total = "";
          foreach($rows as $contents){
            $old_total = 'totalTime' . "::" . $contents['totalTime'];
          }
          $new_total = 'totalTime::TBA';

          $trackedsql = "INSERT INTO tracking (date, time, user, action, affected_ids, old_values, new_values, table_name, sql_string) VALUES(?,?,?,?,?,?,?,?,?)";
          $trackedstmt = $connection->prepare($trackedsql);
          $currentDate = date('Y-m-d');
          $currentTime = date('H:i:s');

          $totalHistorySql = "UPDATE timeclock SET totalTime='TBA' WHERE entry_id=$id";

          $trackedstmt->execute([$currentDate,$currentTime, $current_user, 'update', $id,$old_total, $new_total, 'timeclock', $totalHistorySql]);

          $sql = "UPDATE timeclock SET totalTime=? WHERE entry_id=$id";
          $stmt = $connection->prepare($sql);
          $stmt->execute(['TBA']);

          // 2020-02-06: If edited as time in, remove sick day
          $staffsql = "SELECT idSTAFF as 'idSTAFF' from staff s INNER JOIN timeclock t on (t.entry_id = '$id') where t.staff_pin = s.staff_pin";
          $stmt = $connection->prepare($staffsql);
          $stmt->execute();

          $staffpins = $stmt->fetchAll();
          for ($i = 0; $i < sizeof($staffpins); $i++){
              $staffID = $staffpins[$i]['idSTAFF'];
          }

          $findSick = "SELECT * FROM sick WHERE start_date='$new_entry_date' AND idSTAFF_SICK='$staffID' and auto_timeclock=1";
          $stmt = $connection->prepare($findSick);
          $stmt->execute();

          $idSICKArray = $stmt->fetchAll();
          $old_data = "";

          for ($i = 0; $i < sizeof($idSICKArray); $i++){
              $idSICK = $idSICKArray[$i]['idSICK'];
              $old_data .= "idSICK::" . $idSICK . "|||idSTAFF_SICK::" .  $idSICKArray[$i]['idSTAFF_SICK'] . "|||start_date::" . $idSICKArray[$i]['start_date'] . "|||end_date::" . $idSICKArray[$i]['end_date'] . "|||quarters::". $idSICKArray[$i]['quarters'] . "|||comment::";
              $old_data .= $idSICKArray[$i]['comment'] . "|||day_count::".$idSICKArray[$i]['day_count'] . "|||";
          }

          $delSql = "DELETE FROM sick WHERE date='$new_entry_date' and idSTAFF_SICK='$staffID'";
          $stmt = $connection->prepare($delSql);
          $stmt->execute();

          $trackedsql = "INSERT INTO tracking (date, time, user, action, affected_ids, old_values, new_values, table_name, sql_string) VALUES(?,?,?,?,?,?,?,?,?)";
          $trackedstmt = $connection->prepare($trackedsql);
          $currentDate = date('Y-m-d');
          $currentTime = date('H:i:s');

          $totalHistorySql = "DELETE FROM sick WHERE start_date='$new_entry_date' and idSTAFF_SICK='$staffID'";

          $trackedstmt->execute([$currentDate,$currentTime, $current_user, 'delete', $idSICK,$old_data, "", 'sick', $totalHistorySql]);

        }
        // Out, just update the sick. It will update in reports
        else if ($new_action_type === "out") {
          // 2020-02-06: If edited as time in, remove sick day
          $staffsql = "SELECT idSTAFF as 'idSTAFF' from staff s INNER JOIN timeclock t on (t.entry_id = '$id') where t.staff_pin = s.staff_pin";
          $stmt = $connection->prepare($staffsql);
          $stmt->execute();

          $staffpins = $stmt->fetchAll();
          for ($i = 0; $i < sizeof($staffpins); $i++){
              $staffID = $staffpins[$i]['idSTAFF'];
          }

          // 2020-02-06: If edited as time in, remove sick day
          $findSick = "SELECT * FROM sick WHERE start_date='$new_entry_date' AND idSTAFF_SICK='$staffID' and auto_timeclock=1";
          $stmt = $connection->prepare($findSick);
          $stmt->execute();

          $idSICKArray = $stmt->fetchAll();
          $old_data = "";

          for ($i = 0; $i < sizeof($idSICKArray); $i++){
              $idSICK = $idSICKArray[$i]['idSICK'];
              $old_data .= "idSICK::" . $idSICK . "|||idSTAFF_SICK::" .  $idSICKArray[$i]['idSTAFF_SICK'] . "|||start_date::" . $idSICKArray[$i]['start_date'] . "|||end_date::" . $idSICKArray[$i]['end_date'] . "|||quarters::". $idSICKArray[$i]['quarters'] . "|||comment::";
              $old_data .= $idSICKArray[$i]['comment'] . "|||day_count::".$idSICKArray[$i]['day_count'] . "|||";
          }

          $delSql = "DELETE FROM sick WHERE start_date='$new_entry_date' and idSTAFF_SICK='$staffID'";
          $stmt = $connection->prepare($delSql);
          $stmt->execute();

          $trackedsql = "INSERT INTO tracking (date, time, user, action, affected_ids, old_values, new_values, table_name, sql_string) VALUES(?,?,?,?,?,?,?,?,?)";
          $trackedstmt = $connection->prepare($trackedsql);
          $currentDate = date('Y-m-d');
          $currentTime = date('H:i:s');

          $totalHistorySql = "DELETE FROM sick WHERE date='$new_entry_date' and idSTAFF_SICK='$staffID'";

          $trackedstmt->execute([$currentDate,$currentTime, $current_user, 'delete', $idSICK,$old_data, "", 'sick', $totalHistorySql]);
        }

      }
}
?>
