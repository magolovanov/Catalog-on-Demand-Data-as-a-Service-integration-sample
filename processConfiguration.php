<?php

session_start();
$request = $_REQUEST;
$process_error = false;
$index = isset($request['index']) ? 0 + $request['index'] : 0;
$_SESSION['DAASDEMO']['index'] = $index;
$output = array();
$output['status'] = "OK";
$output['index'] = $index;
header("Record-type: application/json");
echo json_encode($output);
exit();
