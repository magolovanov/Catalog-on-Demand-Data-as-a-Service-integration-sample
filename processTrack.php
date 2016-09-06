<?php

session_start();
$SessionID = $_SESSION['DAASDEMO']['SessionID'];
$ProcessID = $_SESSION['DAASDEMO']['ProcessID'];
$OK = TRUE;
$data = array();
$data["SessionID"] = $SessionID;
$data["ProcessID"] = $ProcessID;
try {
  $response = simplexml_load_file("http://webservices.catalog-on-demand.com:8080/onDemandPublishingProcessor.do?Operation=GetBackgroundJobProcessingState&SessionID=" . $SessionID . "&ProcessID=" . $ProcessID);
  if ($response->Message == "OK") {
    $data["status"] = "OK";
    $data["StateValue"] = (string) $response->GetBackgroundJobProcessingStateResponse->StateValue;
    if (isset($response->GetBackgroundJobProcessingStateResponse->OPSJobID))
      $data["OPSJobID"] = (string) $response->GetBackgroundJobProcessingStateResponse->OPSJobID;
    $state = intval($response->GetBackgroundJobProcessingStateResponse->StateValue);
    if ($state == 67) {
      $data["OutputURL"] = (string) $response->GetBackgroundJobProcessingStateResponse->OutputURL;
      $data["StateDescription"] = (string) $response->GetBackgroundJobProcessingStateResponse->StateDescription;
    } else {
      $data["StateDescription"] = (string) $response->GetBackgroundJobProcessingStateResponse->StateDescription;
      if (isset($response->Errors))
        $data["ErrorMessage"] = (string) $response->Errors->Error->Description;
    }
  } else if ($response->Message == "ERROR") {
    $data["status"] = "Error";
    $data["ErrorMessage"] = (string) $response->Errors->Error->Description;
  } else {
    $data["status"] = "Error";
    $data["ErrorMessage"] = "Runtime error.";
  }
} catch (Exception $e) {
  $data["status"] = "Error";
  $data["ErrorMessage"] = $e->getMessage();
}
echo json_encode($data);
?>