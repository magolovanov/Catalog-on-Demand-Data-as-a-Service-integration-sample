<?php

session_start();
$data = array();
if (isset($_GET['files'])) {
  $error = false;
  $files = array();
  $uploaddir = './uploads/';
  foreach ($_FILES as $file) {
    if (move_uploaded_file($file['tmp_name'], $uploaddir . basename($file['name']))) {
      $_SESSION['DAASDEMO']['SKUList'] = $uploaddir . $file['name'];
    } else {
      $error = true;
    }
    // 1 file expected
    break;
  }
  $data = $error ? array('status' => 'Error', 'error_description' => 'There was an error uploading your files') :
    array('status' => 'OK', 'files' => $files);
} else {
  $data = array('status' => 'OK', 'formData' => $_POST);
  $_SESSION['DAASDEMO']['items'] = 0;
  $_SESSION['DAASDEMO']['itemtext'] = "";
  $text = "";
  
  // call demo stub
  $urltopost = "https://webservices.catalog-on-demand.com/dataAsAServiceDemoStub/processSKUs.php";
  $datatopost = array(
    "Operation" => "ProcessSKUList",
    "SKUList" => file_get_contents( $_SESSION['DAASDEMO']['SKUList'] )
  );
  $ch = curl_init($urltopost);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $datatopost);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $text = curl_exec($ch);
  $_SESSION['DAASDEMO']['itemtext'] = $text;
}
echo json_encode($data);
?>