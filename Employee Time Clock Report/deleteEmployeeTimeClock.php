<?php
// Helper file to delete time clock report
    require_once(dirname(dirname(__FILE__)) . "/connectpdo.php");
    $connection = connectPDO();

    // Delete from SQL databse
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
            $old_values[] = 'totalTime' . "::" . $contents['totalTime'];
          }
          $old_value_string = "";
          // Add to old_values string
          foreach($old_values as $contents){
            $old_value_string.= $contents . "|||";
          }
          $current_user = $_POST['currentuser'];

          // Obtain the necessary information from SQL Database
          $sql = "DELETE FROM timeclock WHERE entry_id=$id";

          // Track the data (trackedQuery not working) into the history
          $trackedsql = "INSERT INTO tracking (date, time, user, action, affected_ids, old_values, new_values, table_name, sql_string) VALUES(?,?,?,?,?,?,?,?,?)";
          $trackedstmt = $connection->prepare($trackedsql);
          $currentDate = date('Y-m-d');
          $currentTime = date('H:i:s');

          $trackedstmt->execute([$currentDate,$currentTime, $current_user, 'delete', $id,$old_value_string, '', 'timeclock', $sql]);

          $stmt = $connection->prepare($sql);
          $stmt->execute();
    }
  ?>
