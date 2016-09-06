<?php

session_start();
$operations = array(
  "login",
  "logout"
);
$output = array();
$request = $_REQUEST;
$process_error = false;
if (in_array($request['operation'], $operations)) {
  switch ($request['operation']) {
    case 'login':
      $username = isset($request['username']) ? urlencode(trim($request['username'])) : null;
      $password = isset($request['password']) ? urlencode($request['password']) : null;
      if (!$username || !$password) {
        $output['status'] = "ERROR";
        if (!$username && !$password)
          $output['error_description'] = "User ID and password are requred.";
        else if (!$username)
          $output['error_description'] = "User ID is requred.";
        else if (!$password)
          $output['error_description'] = "Password is requred.";
        leave($output);
      }
      $r = @simplexml_load_file("http://webservices.catalog-on-demand.com/aservices/api.do?Operation=ValidateUser&UserID=$username&Password=$password");
      if (!$r) {
        $output['status'] = "ERROR";
        $output['error_description'] = "Could not connect to CoD Web Services.";
        leave($output);
      }
      if ($r->Message == 'OK') {
        $_SESSION['DAASDEMO']['user_id'] = (string) $r->ValidateUserResponse->User->UserID;
        $_SESSION['DAASDEMO']['password'] = $password;
        $_SESSION['DAASDEMO']['full_name'] = "" . $r->ValidateUserResponse->User->FirstName . " " . $r->ValidateUserResponse->User->LastName;
        $_SESSION['DAASDEMO']['customer_id'] = (string) $r->ValidateUserResponse->User->CustomerID;
        $_SESSION['DAASDEMO']['IsAdmin'] = (string) $r->ValidateUserResponse->User->IsAdmin;
        $customerConfigs = array();
        foreach ($r->ValidateUserResponse->CatalogOnDemand->MobileApplicationConfiguration as $config) {
          $customerConfigs[] = strtolower($config["Type"]) . "|" . $config["Name"];
        }
        if (count($customerConfigs) == 0) {
          $fh = @fopen("http://app.catalog-on-demand.com/util/listCustomerConfigurations.php?customerID=" . $_SESSION['DAASDEMO']['customer_id'], "r");
          if ($fh) {
            while (( $name = fgets($fh))) {
              $customerConfigs[] = trim($name);
            }
            fclose($fh);
          } else {
            $customerConfigs[] = "Could not open http://app.catalog-on-demand.com/util/listCustomerConfigurations.php?customerID=" . $_SESSION['DAASDEMO']['customer_id'];
          }
        }
        $_SESSION["DAASDEMO_COD_MobileApplicationConfiguration"] = $customerConfigs;
      } else if ($r->Message == 'ERROR') {
        $output['status'] = "ERROR";
        $output['error_description'] = str_replace("Login error: ", "", $r->Errors->Error->Description);
        leave($output);
      } else {
        $output['status'] = "ERROR";
        $output['error_description'] = "Could not connect to CoD Web Services";
        leave($output);
      }
      $output['status'] = "OK";
      leave($output);
      break;
    case "logout":
      unset($_SESSION['DAASDEMO']);
      $output['status'] = "OK";
      leave($output);
  }
} else {
  $output['status'] = 'ERROR';
  $output['error_description'] = 'Invalid operation.';
  leave($output);
}

function leave($output = array('status' => 'ERROR', 'error_description' => 'An unknown error occured (leave by default)')) {
  header("Record-type: application/json");
  echo json_encode($output);
  exit();
}
