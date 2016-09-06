<?php

session_start();
$data = array();
$_SESSION['DAASDEMO']['jobID'] = $_REQUEST['jobID'];
$text = "JobID\t" . $_SESSION['DAASDEMO']['jobID'] . "\n";
$selectedindex = 0;
$configuration = "";
if (isset($_SESSION['DAASDEMO']['index'])) {
  $selectedindex = $selectedindex + $_SESSION['DAASDEMO']['index'];
}
if (isset($_SESSION["DAASDEMO_COD_MobileApplicationConfiguration"][$selectedindex])) {
  $a = explode("|", $_SESSION["DAASDEMO_COD_MobileApplicationConfiguration"][$selectedindex]);
  $configuration = $a[1];
}
$text .= "Configuration\t" . $configuration . "\n";
$text .= "Variable Data\tTitle\tSelected  Products for ABC Inc.\n";
$text .= "Include Categories\n";
$text .= $_SESSION['DAASDEMO']['itemtext'] . "\n#$#\n";
$errors = "";
$OK = TRUE;
$fetched = FALSE;
try {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://app.catalog-on-demand.com/fjf/fjf_api.php?" . time());
  curl_setopt($ch, CURLOPT_POST, true);
  $fields_string = "action=submitScheduledJob&id=" . $_SESSION['DAASDEMO']['user_id'] . "&up=" . $_SESSION['DAASDEMO']['password'];
  $fields_string .= "&text=" . urlencode($text);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Linux; Catalog-on-Demand WebServices)");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FAILONERROR, true);
  $fetched = curl_exec($ch);
  if (!$fetched) {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = FALSE;
    if (isset($httpMessages[$httpCode]))
      $error = $httpMessages[$httpCode];
    if (!$error)
      $error = curl_error($ch);
    if (!$error)
      $error = $httpCode;
    $errors = "Error posting PDF job: " . $error;
    $OK = FALSE;
  }
  curl_close($ch);
} catch (Exception $e) {
  $errors = "Error posting PDF job: " . $e->getMessage();
  $OK = FALSE;
}
if ($OK) {
  $SessionID = "";
  $ProcessID = "";
  $JobID = "";
  $pairs = explode("\n", $fetched);
  foreach ($pairs as $pair) {
    $pair = explode("=", $pair);
    if (count($pair) > 1) {
      $name = $pair[0];
      $value = $pair[1];
      if ($name == "SessionID")
        $SessionID = $value;
      else if ($name == "ProcessID")
        $ProcessID = $value;
      else if ($name == "JobID")
        $JobID = $value;
    }
  }
  $data["status"] = "OK";
  $data["fetched"] = implode(";", $pairs);
  $data["SessionID"] = $SessionID;
  $data["JobID"] = $JobID;
  $data["ProcessID"] = $ProcessID;
  $_SESSION['DAASDEMO']['SessionID'] = $SessionID;
  $_SESSION['DAASDEMO']['JobID'] = $JobID;
  $_SESSION['DAASDEMO']['ProcessID'] = $ProcessID;
} else {
  $data["status"] = "Error";
  $data["error"] = $errors;
}
echo json_encode($data);
?>