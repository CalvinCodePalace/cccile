<?php
/*
Template Name: Lobby TV
*/
// This is the lobby tv screen for 85 (main lobby)
//get_header();
require_once(dirname(dirname(__FILE__)) . "/connect.php");

//try to retrieve super user from database, for now hardcode it
$superuser = "lobbytvuser";
$current_user = wp_get_current_user()->user_login;
?>
<link href="<?php bloginfo('template_directory'); ?>/styles/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php bloginfo('template_directory'); ?>/styles/view.css" rel="stylesheet" type="text/css">
<link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous">

<style>

  h1, h2{
    margin-left: 5px !important;
  }

  h3{
    margin-left: 10px !important;
  }

  .date_time_div{
      text-align: left;
      width:100%;
      border-right:2px solid white;
      border-bottom: 2px solid white;
      background-color: #330080;
  }

  #timestamp{
    font-size: 75px;
    margin-left:15px;
    /*color: #330080;*/
    font-weight: bold;
    color: #F5C501;
  }

  #date{
    font-size: 30px;
    margin-left:15px;
    color: #F5C501;
    font-weight: bold;
  }

  #facts{
    font-size: 22px;
    color: white;
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


  div#logo{
    width: 100%;
    height: 95px;
    border-right: 2px solid white;
    background-image: url(<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/LobbyTvHeaderLogo.jpg"; ?>);
    background-position: left;
    text-align:left;
    background-repeat: no-repeat;
    background-color: white;
    background-color: #330080;
    /* left: -3px; */
  }

  #content{
    margin-top:1px !important;
    width:auto;
    height:98%;
    border-color: white;
    background-color:#330080;
  }

  .invisibleTableHeader{
    background-color: unset;
    border: none;
    width: auto;
  }

  .invisibleTable{
    background-color: unset;
    border: none;
    width: auto;
  }


  .tablepress-class-.invisibleTable table, th, td {
    border:none !important;
    background-color: unset;
  }

  .sectionHeader{
    color: #F5C501;
    font-size:30px;
  }

  #bottom{
    position:fixed;
    bottom:0;
    background-color:#330080;
    border-top: 2px solid white;
  }

  #weatherBox{
    width:100%;
    height:280px;
    color: #F5C501;
    border-right: 2px solid white;
    border-bottom: 2px solid white;
    background-color:#330080;
    background-image: linear-gradient(to bottom, #330080 45%, #3399ff 119%);
  }

  #weatherTable{
    font-size: 20px;
    text-align:center;
    color: #F5C501;
    border: none;
    width:auto;
    background-color:unset !important;

  }

  #weatherTable td{
    width:174px;
    padding-right:5px;
    padding-left:5px;
  }

  #weatherTable th {
    font-size:22px;
  }

  #factDiv{
     width:100%;
     border-right:2px solid white;
     border-bottom: 2px solid white;
     height:150px;
     background-color: #3366D5;
  }

  #maintenanceDiv{
    width:100%;
    height:465px;
    border-right:2px solid white;
    border-bottom: 2px solid white;
    background-image: linear-gradient(to top, #330080 18%, #3399ff 119%);
    color:white;

  }

  #events{
    text-align: center;
    font-size: 22px;
  }

  #eventDiv{
    transform: translateX(-4px);
    transform: translateY(-8px);
    width:1416px;
    border-top: 2px solid white;
    border-bottom: 2px solid white;
    height: 577px;
    background-image: linear-gradient(to top, #330080 12%, #3399ff 119%);
    color:white;
  }

  #newsDiv{
    transform: translateY(-45px);
    border-right:2px solid white;
    border-bottom: 2px solid white;
    width:100%;
    background-image: linear-gradient(to bottom, #330080 45%, #3399ff 139%);
  }

  #firstNewsDiv{
    margin-left:4px;
    width:471px;
    height:394px;
    background-image: linear-gradient(to bottom, #330080 45%, #3399ff 139%);
    color: white;
  }

  #secondNewsDiv{
    margin-left:4px;
    width:471px;
    height:394px;
    background-image: linear-gradient(to bottom, #330080 45%, #3399ff 139%);
    color: white;
  }

  #thirdNewsDiv{
    margin-left:4px;
    width:470px;
    height:394px;
    background-image: linear-gradient(to bottom, #330080 45%, #3399ff 139%);
    color: white;
  }

  #nextEventDiv{
  /*  border: 2px dashed white; */
    font-size:20px;
    text-align:center;
    margin-bottom:15px;
    font-weight:bold;
    height:15px;
    border-top: 2px solid white;

  }

  #nextEvent{
  /*  border: 2px dashed white; */
    width:1408px;
    height:65px;
    color:white;
  }

  .row-flex {
    height: auto;
    display: flex;
    flex-flow: row column;
  }

  .col-flex-1 {
    min-width: 500px;
    border-left: 1px #ccc solid;
    flex: 0 0;
  }

  .col-flex-3 {
    min-width: 500px;
    border-left: 1px #ccc solid;
    flex: 0 0;
  }

  .pos-top {
    display: -webkit-flex;
    display: flex;
    -webkit-align-items: flex-start;
    align-items: flex-start;
  }

  .pos-bottom {
    display: -webkit-flex;
    display: flex;
    -webkit-align-items: flex-end;
    align-items: flex-end;
  }

  #hidden{
    font-size:25px;
    color: white;
  }


</style>

<?php
if (strcmp($current_user, $superuser)>=0){
  $building = 0;
  // if building 85 or 101 selected, choose 85 or 101
  if (isset($_GET['building'])){
      $building = $_GET['building'];
  }
  // Run the screen if building is 85 or 101
  if ($building == 85 || $building == 101){


?>
<!-- <h1> you are authorized </h1> -->
<div id="content" class="site-content" role="main">
  <div class="row-flex">
  <div class="col-flex-3">
    <div class="pos-top">
      <div class="date_time_div">
        <div id='hidden'><strong> Attempting to connect to CTV news..</strong></div>
        <div id='date'></div>
        <div id='timestamp' class='clock'></div>
      </div>

    </div>

    <div class='pos-bottom'>
      <div id='weatherBox'>
        <h2 style='font-size: 25px; font-family: "Britannic"; text-align:left !important;'> Toronto, Ontario </h2>
      <table id='weatherTable'>
        <tbody>
          <?php
          //LOADS WEATHER
          /*
            1-10-2020: READ ME
            The rss feed was obtained from this site: https://weather.gc.ca/city/pages/on-143_metric_e.html
            Should the website change, it might affect the hard code
          */
           $domOBJ = new DOMDocument();
           ob_start();
           $domOBJ->load("https://weather.gc.ca/rss/city/on-143_e.xml");//XML page URL
           ob_end_clean();
           $content = $domOBJ->getElementsByTagName("entry");
           $weatherArray = array();

           foreach($content as $data)
           {
             $title = $data->getElementsByTagName("title")->item(0)->nodeValue;
             $link = $data->getElementsByTagName("link")->item(0)->nodeValue;
             $weatherArray[] = $title . ' ' . $link;
           }

           // HARD CODED, MAY CHANGE BASED ON RSS CHANGES
           $conditionStatement = $weatherArray[1];
           //$arrayOfConditions = explode(',', $conditionStatement);
          // The weather condition status array
           $statusArray = array();
           $status = explode(':', $weatherArray[1]);
           $status2 = explode(':', $weatherArray[4]);
           $status3 = explode(':', $weatherArray[6]);
           $status4 = explode(':', $weatherArray[8]);
           $statusArray[] = $status;
           $statusArray[] = $status2;
           $statusArray[] = $status3;
           $statusArray[] = $status4;

           // The table header for the next 3 days
           $days = array();
           $day1Pos = strpos($weatherArray[2], ':');
           /*$day2Pos = strpos($weatherArray[4], ':');
           $day3Pos = strpos($weatherArray[6], ':');*/
           $days[] = substr($weatherArray[2], 0, $day1Pos);
           $days[] = $status2[0];//substr($weatherArray[4], 0, $day2Pos);
           $days[] = $status3[0];//substr($weatherArray[6], 0, $day3Pos);
           $days[] = $status4[0];//substr($weatherArray[6], 0, $day3Pos);

           // Check if it is night time
           $isNight = false; // Assume false
           $night = array();
           $night[] = explode(' ', $days[0]);
           $night[] = explode(' ', $days[1]);
           $night[] = explode(' ', $days[2]);
           $night[] = explode(' ', $days[3]);

           // If it is night time
           if ($night[0][1] === 'night'){
              $isNight = true;?>
              <th style='border-right: 2px solid white !important; color:white !important;'> <?php echo $night[0][0]; ?></th>
              <th style='border-right: 2px solid white !important; color:white !important;'> <?php echo $night[1][0]; ?> </th>
              <th style='border-right: 2px solid white !important; color:white !important;'> <?php echo $night[2][0]; ?> </th>
              <th style='color:white !important;'> <?php echo $night[3][0]; ?> </th> <!-- last TH does not need right border -->
            <?php
           }
           else {
             ?>
             <th style='border-right: 2px solid white !important; color:white !important;'> <?php echo $days[0]; ?></th>
             <th style='border-right: 2px solid white !important; color:white !important;'> <?php echo $days[1]; ?></th>
             <th style='border-right: 2px solid white !important; color:white !important;'> <?php echo $days[2]; ?></th>
             <th style='color:white !important;'> <?php echo $days[3]; ?></th> <!-- last TH does not need right border -->
             <?php
           }
            ?>

          <tr style='border-top: 2px solid white !important;'>

         <?php
         // Print the current condition text
         for ($i = 0; $i < 4; $i++){
           // If not last td
           if ($i != 3){
             echo "<td style='vertical-align:top; border-right: 2px solid white !important;'>";
           }
           // Otherwise last td doesn't need white right border
           else {
             echo "<td style='vertical-align:top;'>";
           }

           // If it's not the first case, then it uses a peroid instead of a
           if ($i > 0){
               $degreeArray = explode('.', $statusArray[$i][1]);
           }
           // else explode the first case
           else {
             $degreeArray = explode(',', $statusArray[$i][1]);
           }
           echo "<span style='font-size:17px;'><strong>" . $degreeArray[0] . "</strong></span><br></td>";
        }
        ?>
        </tr>
        <tr>
            <?php
              // Print the current condition image and degrees
              for ($i = 0; $i < 4; $i++){
                // If not last td
                if ($i != 3){
                  echo "<td  style='vertical-align:top;border-right: 2px solid white !important;'>";
                }
                // Otherwise last td doesn't need white right border
                else {
                  echo "<td  style='vertical-align:top;'>";
                }
                // If it's not the first case, then it uses a peroid instead of a ,
                if ($i > 0){
                    $degreeArray = explode('.', $statusArray[$i][1]);
                }
                // else explode the first case
                else {
                  $degreeArray = explode(',', $statusArray[$i][1]);
                }

                 // Displaying the picture for the first day
                 if (stripos($statusArray[$i][1], 'thunder') !== false){
                    // Display thunder picture
                  $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/thunder.svg";
                  echo '<img src=' . $url . ' alt="thunder" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'mostly cloudy') !== false){
                   // Display cloud picture
                   if ($isNight == false){
                     $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/MostlyCloudy.svg";
                   }
                   else {
                     $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/MostlyCloudyNight.svg";
                   }
                   echo '<img src=' . $url . ' alt="mostly cloudy" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'partly cloudy') !== false){
                   // Display cloud picture
                   if ($isNight == false){
                     $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/PartlyCloudy.svg";
                   }
                   else {
                     $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/PartlyCloudyNight.svg";
                   }
                   echo '<img src=' . $url . ' alt="partly cloudy" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'cloudy') !== false){
                   // Display cloud picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/Cloudy.svg";
                   echo '<img src=' . $url . ' alt="cloudy" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'mix of sun and cloud') !== false){
                   // Display snowing picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/PartlyCloudy.svg";
                   echo '<img src=' . $url . ' alt="mix cloudy" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'mist') !== false){
                   // Display mist  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/fog.svg";
                   echo '<img src=' . $url . ' alt="cloudy" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'fog') !== false){
                   // Display mist  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/fog.svg";
                   echo '<img src=' . $url . ' alt="cloudy" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'snow') !== false){
                   // Display snowing picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/snowy.svg";
                   echo '<img src=' . $url . ' alt="snow"/ width="100" height="100">';
                 }
                 else if (stripos($statusArray[$i][1], 'flurries') !== false){
                   // Display snowing picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/snowy.svg";
                   echo '<img src=' . $url . ' alt="flurries" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'freezing rain') !== false){
                   // Display rain  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/FreezingRain.svg";
                   echo '<img src=' . $url . ' alt="rain" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'rain') !== false){
                   // Display rain  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/rain.svg";
                   echo '<img src=' . $url . ' alt="rain"/ width="100" height="100">';
                 }
                 else if (stripos($statusArray[$i][1], 'showers') !== false){
                   // Display rain  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/rain.svg";
                   echo '<img src=' . $url . ' alt="showers" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'drizzle') !== false){
                   // Display rain  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/rain.svg";
                   echo '<img src=' . $url . ' alt="drizzle"/ width="100" height="100">';
                 }
                 else if (stripos($statusArray[$i][1], 'clear') !== false){
                   // Display sun  picture
                   if ($isNight == false){
                     $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/PartlyCloudy.svg";
                   }
                   else {
                     $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/PartlyCloudyNight.svg";
                   }
                   echo '<img src=' . $url . ' alt="clear" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'sun') !== false){
                   // Display sun  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/sunny.svg";
                   echo '<img src=' . $url . ' alt="clear" width="100" height="100"/>';
          //    echo "<iframe src='$url'></iframe>";
                 }
                 else if (stripos($statusArray[$i][1], 'wind') !== false){
                   // Display mist  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/windy.svg";
                   echo '<img src=' . $url . ' alt="cloudy" width="100" height="100"/>';
                 }
                 else if (stripos($statusArray[$i][1], 'windy') !== false){
                   // Display mist  picture
                   $url = "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/WeatherIcon/windy.svg";
                   echo '<img src=' . $url . ' alt="cloudy" width="100" height="100"/>';
                 }
                 // The degrees
                 echo "<br>";

                 // If there's no degree celcius displaying, you may have to comment out the php code below (up until ? > ) and uncomment the code directly below this comment
                  //echo "<span style='color: white; font-size: 35px;'><strong>" . $degreeArray[1] . "</span>";};

               // If it's not first case, we have to modify it
               if ($i > 0){
                  $finalDegreeArray = explode(' ', $degreeArray[1]); // Create an array of strings based on degree information (it can say things like High 11)
                  //print_r($finalDegreeArray);
                  // For some reason, $finalDegreeArray[0] is blank, may change based on RSS
                  $positionCounter = 1;
                  $positionFound = false;
                  // Run until it finds its first number
                  while ($positionCounter != sizeof($finalDegreeArray) && $positionFound == false){
                    // If first occurence of number found, exit
                    if (is_numeric($finalDegreeArray[$positionCounter]) || $finalDegreeArray[$positionCounter] === "zero"){
                      $positionFound = true;
                    }
                    else {
                      $positionCounter++;
                    }
                  }
                  // Then check the position before the number
                  // If it says minus, make the number negative
                  if ($finalDegreeArray[$positionCounter - 1] === 'minus'){
                    echo "<span style='font-size: 35px;'><strong>-" . $finalDegreeArray[$positionCounter] . "°C</strong></span>";
                  }
                  // Else if it's positive
                  else if ($finalDegreeArray[$positionCounter - 1] === 'plus'){
                    echo "<span style='font-size: 35px;'><strong>" . $finalDegreeArray[$positionCounter] . "°C</strong></span>";
                  }
                  // Otherwise, just print it out
                  else {
                    // If it says zero, print 0
                    if ($finalDegreeArray[$positionCounter] === "zero"){
                        echo "<span style='font-size: 35px;'><strong> 0°C</strong></span>";
                    }
                    else {
                      echo "<span style='font-size: 35px;'><strong>" . $finalDegreeArray[$positionCounter] . "°C</strong></span>";
                    }

                  }
                 }
                 // Else if it's case 1: just print it
                 else {
                    echo "<span style='font-size: 35px;'><strong>" . $degreeArray[1] . "</span>";
                 }
                 echo "</td>";
               }

               ?>
              </tr>
            </tbody>
          </table>
      </div>

    </div>

    <div class="pos-bottom">
            <div id='factDiv'>
              <h1 class='sectionHeader' style='margin-left:0px !important;'><strong>DFC FACTS</strong></h1>
              <?php
              //LOADS XML DFC FACTS
               $domOBJ = new DOMDocument();
               ob_start();
               $domOBJ->load(bloginfo('template_directory') . "wp-content/themes/contango/Signage/facts.xml");//XML page URL
               ob_end_clean();
               $content = $domOBJ->getElementsByTagName("item");
               $arrayOfFacts = array();

               foreach($content as $data)
               {
                 $title = $data->getElementsByTagName("title")->item(0)->nodeValue;
                 $link = $data->getElementsByTagName("description")->item(0)->nodeValue;
                 $arrayOfFacts[] = $title . ' ' . $link;
               }
               $fact_js_array = json_encode($arrayOfFacts);
               echo "<section id='facts'>". $arrayOfFacts[0] . "</section>";
              ?>
            </div>
      </div>

    <div class="pos-bottom">
        <div id="maintenanceDiv">
          <h1 class='sectionHeader' style='margin-left:0px !important;text-align:left !important;'><strong>MAINTENANCE TASKS</strong></h1>
          <?php

          //LOADS MAINTENANCE TASK XML
           $domOBJ = new DOMDocument();
           ob_start();
           $domOBJ->load(bloginfo('template_directory') . "wp-content/themes/contango/Signage/maintenance" . $building . ".xml");//XML page URL
           ob_end_clean();

           $content = $domOBJ->getElementsByTagName("item");
        //   print_r($content);
           //$content = array_reverse($content, true);
           $numberOfElements = 0; // This is used to counter number of non empty elements

           // ASSUMPTION: According to DFC, it's assumed that only one video will be uploaded to all of the maintenance tasks.
           for($n = $content->length-1; $n >= 0; $n--)
           {
             $name =  $content->item($n)->getElementsByTagName("name")->item(0)->nodeValue;
             $brief = $content->item($n)->getElementsByTagName("brief")->item(0)->nodeValue;
             $line1 = $content->item($n)->getElementsByTagName("line1")->item(0)->nodeValue;
             $line2 = $content->item($n)->getElementsByTagName("line2")->item(0)->nodeValue;
             $attachImage = $content->item($n)->getElementsByTagName("attachImage")->item(0)->nodeValue;

             if ($name !== ''){
               $numberOfElements++;
               echo "<h2 style='font-size:22px;'><strong> $name </strong></h2>";
               echo "<h1 style='font-size:18px;'><strong> $brief ($line2) </strong></h1>";
               echo "<section style='font-size:16px; margin-left:15px;'> $line1 <br></section>";
               if ($attachImage != ""){
                 echo "<video id='maintenanceVideo' width='500' height='100' playsinline autoplay muted loop>";
                 echo "<source src='".$attachImage."' type='video/mp4'>";
                 echo "</video>";
               }
             }

           }
          // If there are no elements, display another message
           if ($numberOfElements == 0){
             echo "<div style='text-align:center; font-size:35px; margin-top:85px;'><strong> No Maintenance Tasks </strong></div>";
           }

           // If there is 1 element
            if ($numberOfElements == 1){
              ?>
              <script>
                var mainVid = document.getElementById("maintenanceVideo");
                mainVid.height = '200';
                mainVid.width = '500';
              </script>
              <?php
            }

            // If there is 1 element
             if ($numberOfElements == 2){
               ?>
               <script>
                 var mainVid = document.getElementById("maintenanceVideo");
                 mainVid.height = '150';
                 mainVid.width = '300';
               </script>
               <?php
             }

          ?>
          <img src='<?php echo "http://" . $_SERVER['SERVER_NAME'] . "/wordpress/images/LobbyTvHeaderLogo.png"; ?>' alt='logo' style='position: absolute; left: 5px; bottom: 28px;'>
        </div>
      </div>

  </div>

<div class="col-flex-3" id='mastercol' style='border:2px dashed transparent;'>

        <div style='border: 2px solid white; border-right: none !important; width:1418px; transform: translate(-4px, -4px); background-color:#330080'><h1 class='sectionHeader'><strong> CTV NEWS </strong></h1> </div>
        <div class='pos-bottom' style='transform: TranslateX(-4px);'>
              <?php
                //LOADS RSS from CTV NEWS
               $domOBJ = new DOMDocument();
               $errorFound = "false";

              // ob_start();
               $domOBJ->load("https://toronto.ctvnews.ca/rss/ctv-news-toronto-1.822319");//XML page URL

              // echo $domOBJ['textContent'];

               $content = $domOBJ->getElementsByTagName("item");
               $arrayOfNews = array();
              // echo $content->length;
               if ($content->length == 0){
                $errorFound = "true";
               }
               foreach($content as $data)
               {
                 $title = $data->getElementsByTagName("title")->item(0)->nodeValue;
                 $description = $data->getElementsByTagName("description")->item(0)->nodeValue;
                 // Put contents into an array of array of news
                 $arrayOfNews[] = array($title, $description);

                /* echo "<h2 style='margin-left:0px !important; font-size:30px;'><strong> $title </strong></h2>";
                 echo "<span style='font-size: 25px;'>$description </span><br>";
                 echo "<br>"; */
               }
               $js_news_array = json_encode($arrayOfNews);
               //print_r($arrayOfNews);
              ?>
              <div style='border-right: 2px solid white; border-left: 2px dashed transparent !important;border-bottom: 2px solid white; transform: Translate(-4px,-4px);'>
                <div id='firstNewsDiv'>
                  <?php
                  $title = $arrayOfNews[0][0];
                  $description = $arrayOfNews[0][1];
                  echo "<h2 id='firstNewsTitle' style='margin-left:2px !important; font-size:30px;'><strong> $title </strong></h2>";
                  echo "<span id='firstNewsDescription' style='margin-left:2px; font-size: 25px;'>$description </span><br>";
                  ?>
                </div>
              </div>
            <div style='border-right: 2px solid white;border-bottom: 2px solid white; transform: Translate(-8px,-4px);'>
                <div id='secondNewsDiv'>
                <?php
                $title = $arrayOfNews[1][0];
                $description = $arrayOfNews[1][1];
                echo "<h2 id='secondNewsTitle' style='margin-left:2px;!important; font-size:30px;'><strong> $title </strong></h2>";
                echo "<span id='secondNewsDescription' style='margin-left:2px; font-size: 25px;'>$description </span><br>";
                ?>
                </div>
            </div>
            <div style='border-bottom: 2px solid white; transform: Translate(-12px,-4px);'>
                <div id='thirdNewsDiv'>
                <?php
                  $title = $arrayOfNews[2][0];
                  $description = $arrayOfNews[2][1];
                  echo "<h2 id='thirdNewsTitle' style='margin-left:2px !important; font-size:30px;'><strong> $title </strong></h2>";
                  echo "<span id='thirdNewsDescription' style='margin-left:2px; font-size: 25px;'>$description </span><br>";
                  ?>
              </div>
          </div>
        </div>
  <div class="col-flex-1" id='newscol' style='border:2px dashed transparent; transform: translateX(-4px);'>
    <div id='eventDiv'>
    <?php
      // LOAD POSTS.XML FOR DFC EVENTS
       $domOBJ = new DOMDocument();
       ob_start();
       $domOBJ->load(bloginfo('template_directory') . "wp-content/themes/contango/Signage/posts.xml");//XML page URL
       ob_end_clean();
       $content = $domOBJ->getElementsByTagName("item");
       $arrayofTitlePosts= array();
       $arrayofSubheading1= array();
       $arrayofSubheading2= array();
       $arrayofStartDates= array();
       $arrayofEndDates= array();
       $arrayOfDescriptions= array();
       $arrayOfImages = array();


       foreach($content as $data)
       {
         // Get the two dates of the events. The start date would be when the event starts. The end date is when it ends
         // The event notices should display if the event has not started or ended.
         // If an event has ended, it should stop displaying. In this case, the end date is less than today
         $endDate = $data->getElementsByTagName("endDate")->item(0)->nodeValue;
         $startDate = $data->getElementsByTagName("startDate")->item(0)->nodeValue;

         // If the endDate is after or today or there is no end date, display
         if ($endDate >= date("Y-m-d") || $endDate == ''){
           $title = $data->getElementsByTagName("title")->item(0)->nodeValue;
           $subheading1 = $data->getElementsByTagName("subheading1")->item(0)->nodeValue;
           $subheading2 = $data->getElementsByTagName("subheading2")->item(0)->nodeValue;
           $description = $data->getElementsByTagName("description")->item(0)->nodeValue;
           $image = $data->getElementsByTagName("link")->item(0)->nodeValue;

           $arrayofTitlePosts[] = $title;
           $arrayofStartDates[] =$startDate;
           $arrayofEndDates[] = $endDate;
           $arrayofSubheading1[] = $subheading1;
           $arrayofSubheading2[] = $subheading2;
          // $arrayofEndDates[] = $endDate;
           $arrayOfDescriptions[] = $description;
           $arrayOfImages[] = $image;
         }

       }
       $js_array_titles = json_encode($arrayofTitlePosts);
       $js_array_subheading1 = json_encode($arrayofSubheading1);
       $js_array_subheading2 = json_encode($arrayofSubheading2);
       $js_array_descriptions = json_encode($arrayOfDescriptions);
       $js_array_images = json_encode($arrayOfImages);
       $js_array_startDate = json_encode($arrayofStartDates);
       $js_array_endDate = json_encode($arrayofEndDates);
       // TO CALCULATE REFRESH TIME:
       for ($i = 0; $i < sizeof($arrayOfImages); $i++){
         $theFile = explode('/', $arrayOfImages[0]);
         $theType = substr($theFile[sizeof($theFile)-1], 0, 3);
         if($theType === "mp4"){
         }
       }
       // For some reason, having transparent borders stops shifting the CTV news section
       echo "<div id='events' style='border:2px dotted transparent ;'>";
       echo "<h1 style='font-size:30px; margin-top:5px; color:gold;'><strong>" . $arrayofTitlePosts[0] . "</strong></h1>";
       // If no end date, use a different format
       if ($arrayofEndDates[0] === ''){
         $startDate = date('F d, Y',strtotime($arrayofStartDates[0]));
         echo "<h2 style='font-size:28px;'><strong>" . $startDate . " - N/A </strong></h2>";
       }
       // else use regular format
       else {
         $startDate = date('F d, Y',strtotime($arrayofStartDates[0]));
         $endDate = date('F d, Y',strtotime($arrayofEndDates[0]));
         echo "<h2 style='font-size:28px;'><strong>" . $startDate . " - " . $endDate . "</strong></h2>";
       }
       // Break the array and check the last element of array to see if mp4 or img
       $img = explode('/', $arrayOfImages[0]);
       $type = substr($img[sizeof($img)-1], 0, 3);
       if ($type === "mp4"){
         echo "<video id='video' width='700' height='250'>";
         echo "<source src='".$arrayOfImages[0]."' type='video/mp4'>";
         echo "</video>";
       }
       else {
         echo "<img src='" . $arrayOfImages[0] . "' alt='eventImage'  width='500' height='250' style='vertical-align:top; padding-top:0px; '/>";
       }
       echo "<h2 style='font-size:28px;'><strong>" . $arrayofSubheading1[0] . " <br> " .  $arrayofSubheading2[0] . "</strong></h2>";
       //echo "<h3>" . $arrayofSubheading2[0] . "</h3>";
    //   echo $arrayofEndDates[0];
       echo "<div style='margin-left:10px; font-size:23px;'>" . $arrayOfDescriptions[0] . "</div>";
       echo "</div>";
    ?>
    </div>

  </div>

      <div class='pos-bottom' style='height: 15px;'>
        <div id='nextEventDiv'>
            <div id='nextEvent'>
              <?php
                $eventPositionStart = -1; // Remember the position of the best start date
                // Loop through the entire array to select next up coming start date
                for ($i = 0; $i < sizeof($arrayofStartDates); $i++){
                  // if the start date is not  before today, we check, else we ignore
                  if ($arrayofStartDates[$i] > date("Y-m-d")){
                    // If position not chosen, choose it
                    if($eventPositionStart == -1){
                      $eventPositionStart = $i;
                    }
                    // Else,if the current start date is before chosen one, choose that
                    else if ($arrayofStartDates[$i] < $arrayofStartDates[$eventPositionStart]){
                      $eventPositionStart = $i;
                    }
                    // Else we move on
                  }
                }
                // If event is not chosen, choose a different description
                if ($eventPositionStart == -1){
                  echo "<span style='font-size:20px;'> Coming Up Next:</span> <span style='color:#999900'>None</span>";
                }
                else {
                  $chosenTitle = $arrayofTitlePosts[$eventPositionStart];
                  $chosenDate = date('F d, Y',strtotime($arrayofStartDates[$eventPositionStart]));
                  echo "<span style='font-size:20px;'>Coming Up Next:</span> <span style='color:#999900'> $chosenTitle </span><span style='font-size:20px;'>ON</span> <span style='color:#B235FF'>$chosenDate</span> ";
                }

              ?>
            </div>
        </div>

      </div>
  </div>
  <div id='hiddenVideos' hidden>

  </div>
  <div id='bottom' style='border-right:2px solid white; height:55px; transform: translateY(25px);'>

    <marquee behavior='scroll' direction='left' scrollamount="7" width='1916'>
      <span style='color:red; font-size:15px;'><strong>
    <?php
    // Load notices.xml
    $domOBJ = new DOMDocument();
    ob_start();
    $domOBJ->load(bloginfo('template_directory') . "wp-content/themes/contango/Signage/notices" . $building . ".xml");//XML page URL
    ob_end_clean();
    $content = $domOBJ->getElementsByTagName("item");

    foreach($content as $data)
    {
         $title = $data->getElementsByTagName("title")->item(0)->nodeValue;
         $description = $data->getElementsByTagName("description")->item(0)->nodeValue;
         $image = $data->getElementsByTagName("link")->item(0)->nodeValue;
         // get start date and end date
         $startDate = $data->getElementsByTagName("startDate")->item(0)->nodeValue;
         $endDate = $data->getElementsByTagName("endDate")->item(0)->nodeValue;

         // If the start date is less than today and today is less than end date(or no end date), display
         if ($startDate <= date('Y-m-d') && (date('Y-m-d') <= $endDate || $endDate == '')){
           echo "<img src='" . 'http://10.10.85.77/wordpress/images/notification_icons/alert.png' . "' alt='warning' width='25' height='25'> $title : $description";
         }
    }
    ?>
  </strong>
  </span>
</marquee>
</div>
</div>

</div>

<script>
  var monthNames = [
    "January", "February", "March",
    "April", "May", "June", "July",
    "August", "September", "October",
    "November", "December"
  ];
  var factCounter = 0;
  var eventCounter = 0;
  var newsCounter = 3;

  var totalEventTime = 0; // Get total video time of all events. Will trigger refresh if necessary.
  var eventInterval;

$(document).ready(function() {
    // If CTV news has issues, reload until it's okay
    var errorFound = "<?php echo $errorFound; ?>";
  //  alert(errorFound);
    if (errorFound === "true"){
      window.location.reload();
    }
    // Else hide the attempt to connect message
    else {
      $('#hidden').hide();
      displayClock();
      setInterval(displayClock, 1000); // Time is checked every second
      setInterval(changeFacts, 30000); // Facts changes every 30 seconds
      setInterval(changeNews, 60000); // News changes every minute

      // Intialize Refresh time
      changeEventVideo();
      setTimeout(function (){
        var maintenanceVideo = document.getElementById('maintenanceVideo');
        maintenanceVideo.muted = !maintenanceVideo.muted;
      }, 3000);

      var news_array = <?php echo $js_news_array ?>; // Get json array for it to get length
      var event_array = <?php echo $js_array_images ?>; //  // Get json array for it to get length

      // Get total duration of events
      for (let i = 0; i < event_array.length; i++){
        var event = event_array[i].split('/');
        var eventType = event[event.length-1].substring(0, 3);
        // If mp4, find duration
        if (eventType === "mp4"){
            var newVideoDiv = "<video id='hiddenVideo"+i+"'><source src='"+event_array[i]+"' type='video/mp4'></video>";
            $('#hiddenVideos').append(newVideoDiv);

            setTimeout(function (){
              var startingVid = document.getElementById('hiddenVideo'+i);
              var startingDuration = ((startingVid.duration * 1000) + 3000)*2;
              totalEventTime += startingDuration;
              console.log("VIDEO: " + totalEventTime);
              $('#hiddenVideo'+i).remove();
          },1000);

        }
        else{
          totalEventTime += 60000 * 2; // Add 1 minute
          console.log("IMG: " + totalEventTime);
        }
      }
      setTimeout(function (){
        console.log("TOTAL EVENT TIME: " + totalEventTime);
        console.log("TOTAL NEWS TIME: " + (news_array.length*2)*60000);
        // If length of news > event, use news
        if (((news_array.length*2)*60000) > totalEventTime){
          var refreshTime = (news_array.length*2)*60000;
          console.log('news time refresh');
        }
        // Else use time length
        else{
          var refreshTime = totalEventTime;
          console.log('event time refresh');
        }
        setInterval(function() {
           window.location.reload();
         }, refreshTime); // reload page every (number of CTV news * 2) minutes or EVENT TIME algorithm to refresh new data
     },3000);
    }
 });



// Change news every minute
 function changeNews(){
   var news_array = <?php echo $js_news_array; ?>; // Get JSON array of news
   // Used to select divs
   var arrayOfTitleDivs = ["firstNewsTitle", "secondNewsTitle", "thirdNewsTitle"];
   var arrayOfDescriptionDivs = ["firstNewsDescription", "secondNewsDescription", "thirdNewsDescription"];
   // Run 3 times
   for (i = 0; i < 3; i++){
     var title = news_array[newsCounter][0];
     var description = news_array[newsCounter][1];
     // Select div
     var selectedTitle = arrayOfTitleDivs[i];
     var selectedDescription = arrayOfDescriptionDivs[i];
     changeNewsHelper(selectedTitle, selectedDescription, title, description)

     newsCounter = newsCounter + 1;
     if(newsCounter > news_array.length-1){
       newsCounter = 0;
     }
   }

 }
 // Helper Function of changeNews() | Putting setTimeout in for loop does not do as intended, therefore a helper function is used
 function changeNewsHelper(selectedTitle, selectedDescription, title, description){
   $('#'+selectedTitle).fadeOut();
   $('#'+selectedDescription).fadeOut();
    setTimeout(function() {
         $('#'+selectedTitle).html("<strong>"+ title + "</strong>");
         $('#'+selectedDescription).html(description);
         $('#'+selectedTitle).fadeIn();
         $('#'+selectedDescription).fadeIn();
   }, 1000);
 }

 // Change facts every 30 seconds
 function changeFacts(){
  var js_array = <?php echo $fact_js_array; ?>; // Get json array
  var lengthOfArray = js_array.length; // Get length of json array
  factCounter++;
  // if reached the end, reset back to 0
  if (factCounter == lengthOfArray){
    factCounter = 0;
  }
  // Ensure that the height of the box doesn't go away due to change
  var current_height = $('#factDiv').outerHeight();
  $("#factDiv").css("min-height", current_height);
  $('#facts').fadeOut();
    setTimeout(function() {
       $('#facts').html(js_array[factCounter]);
       $('#facts').fadeIn();
  }, 1000);
 }
 // Change events every minute
 function changeEvents(){
   var js_array_images = <?php echo $js_array_images ?>; // Get json array
   var js_array_titles = <?php echo $js_array_titles ?>; // Get json array
   var js_array_subheading1 = <?php echo $js_array_subheading1 ?>; // Get json array
   var js_array_subheading2 = <?php echo $js_array_subheading2 ?>; // Get json array
   var js_array_descriptions = <?php echo $js_array_descriptions ?>; // Get json array
   var js_array_startDate = <?php echo $js_array_startDate; ?> // Get json array
   var js_array_endDate = <?php echo $js_array_endDate; ?> // Get json array

   var js_array_length = js_array_titles.length; // Get length of json array

   eventCounter++;
   // if reached the end, reset back to 0
   if (eventCounter == js_array_length){
     eventCounter = 0;
   }
   var stringOutput = "<h1 style='font-size:30px; margin-top:5px; color: gold;'><strong>" + js_array_titles[eventCounter] + "</strong></h1>";
   // If no end date, use a different format
   var startDate = new Date(js_array_startDate[eventCounter] + " EST"); // When transferring from PHP, it is off by 1 day, needs EST
   var monthStartDate = monthNames[startDate.getMonth()];
   var dayMonthStartDate = startDate.getDate();
   var yearMonthStartDate = startDate.getFullYear();
   // If no end date, use a different format
   if (js_array_endDate[eventCounter] === ''){
     stringOutput += "<h2 style='font-size:28px;'><strong>" + monthStartDate + " " + dayMonthStartDate + ", " + yearMonthStartDate +  " - N/A </strong></h2>";
   }
   // else use regular format
   else {
     var endDate = new Date(js_array_endDate[eventCounter] + " EST"); // When transferring from PHP, it is off by 1 day, needs EST
     var monthEndDate = monthNames[endDate.getMonth()];
     var dayMonthEndDate = endDate.getDate();
     var yearMonthEndDate = endDate.getFullYear();
     stringOutput += "<h2 style='font-size:28px;'><strong>" + monthStartDate + " " + dayMonthStartDate + ", " + yearMonthEndDate +  " - " + monthEndDate + " " + dayMonthEndDate + ", " + yearMonthEndDate +   "</strong></h2>";
   }
   // Break the array and check the last element of array to see if mp4 or img
   var img = js_array_images[eventCounter].split('/');
   var type = img[img.length-1].substring(0, 3);
   if (type === "mp4"){
     stringOutput += "<video id='video' width='700' height='250'>";
     stringOutput += "<source src='"+js_array_images[eventCounter]+"' type='video/mp4'>";
     stringOutput += "</video>";

   }
   else {
    stringOutput += "<img src='" + js_array_images[eventCounter] + "'width='500' height='250' alt='eventImage' style='vertical-align:top; padding-top:0px;'/>";
   }

   stringOutput +="<h2 style='font-size:28px;'><strong>" + js_array_subheading1[eventCounter] + " <br> " +  js_array_subheading2[eventCounter] + "</strong></h2>";
   stringOutput += "<div style='margin-left:10px;font-size:23px;'>" + js_array_descriptions[eventCounter] + "</div>";

   // Ensure that the height of the box doesn't go away due to change

   var current_height = $('#eventDiv').outerHeight();
   $("#eventDiv").css("min-height", current_height);

   var current_height = $('#mastercol').outerHeight();
   $("#mastercol").css("min-height", current_height);


   var current_height = $('#events').outerHeight();
   $("#events").css("min-height", current_height);

   $('#events').fadeOut();
   setTimeout(function() {
        $('#events').html(stringOutput);
        $('#events').fadeIn();
   }, 1000);
   clearInterval(eventInterval);
   // If video, play for video duration
   console.log(type);
   if (type === "mp4"){
     changeEventVideo();
   }
   // Else play for 1 minute
   else {
      console.log("IMG 1 minute");
      eventInterval = setInterval(changeEvents, 60000); // Events changes every 5 minutes
   }


 }

// Helper function to adjust interval of videos
 function changeEventVideo(){
   setTimeout(function() {
     var video = document.getElementById('video');
     video.play();
     videoTime = (video.duration * 1000) + 3000;
     eventInterval = setInterval(changeEvents, videoTime); // Events changes every 5 minutes
     console.log("TIME: " + videoTime);
   },3000)
 }

  // Display the current time by setting the timestamp and date to of current time
  function displayClock(){
    var date = new Date();
    // Is it Sunday-Saturday?
    switch(date.getDay()){
      case 0:
          var xday = "Sunday";
          break;
      case 1:
        var xday = "Monday";
        break;
      case 2:
        var xday = "Tuesday";
        break;
      case 3:
        var xday = "Wednesday";
        break;
      case 4:
        var xday = "Thursday";
        break;
      case 5:
        var xday = "Friday";
        break;
      case 6:
        var xday = "Saturday";
        break;
    }
    var day = date.getDate();
    var month = date.getMonth();
    var year = date.getFullYear();
    var dateString = monthNames[month] + " " + day + " " + year;
    var hours = date.getHours();
    var minutes = date.getMinutes();

    if (minutes < 10){
      minutes = '0' + minutes
    }
    var ampm = 'AM';
    if (hours >= 12){
      ampm = 'PM';
      hours = hours - 12;
      if (hours == 0){
        hours = 12;
      }
    }

   $('#date').html(xday + ', ' + dateString);
   $('#timestamp').html(hours + ":" + minutes + " " + ampm);
  }
</script>
<?php
  }
  else {
    invalidTVSelection();
    die();
  }
}
else {
  get_header();
  noAuth();
  die();
}

function noAuth(){
?>
    <div id="content" class="site-content" role="main" style="text-align: center">
    <section style="background: #ffffff; padding: 2rem; margin: 1rem 13%;
        border: 2px solid #cccccc;">
        <h1>Not Authorized</h1>
        <p>You are not authorized to view Lobby TV</p>
        <p>If you want access, see Ann or Hong about getting authorization
        for Lobby TV</p>
    </section>
    </div>
  <?php
}


function invalidTVSelection(){
?>
    <div id="content" class="site-content" role="main" style="text-align: center">
    <section style="background: #ffffff; padding: 2rem; margin: 1rem 13%;
        border: 2px solid #cccccc;">
        <h1>Invalid TV Selected</h1>
        <p>Please select the correct tv display in the dashboard if you are admin. </p>
        <p>If you are experiencing trouble, please refer to the Lobby TV Manual.</p>
    </section>
    </div>
  <?php
}
