<?php
session_start();
if (isset($_SESSION['RC_EDITOR']['user_id'])) {
  header('location:index.php');
  die("Already logged in, redirecting to home page...");
}
if ($_SERVER['SERVER_PORT'] != 443) {
  $url = "https://" . $_SERVER['SERVER_NAME'] . ":443" . $_SERVER['REQUEST_URI'];
  header("Location: $url");
  die("Not SSL, redirecting to https...");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catalog-on-Demand&reg; Data-as-a-Service demo</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <!--[if lt IE 9]>
      <script src="http://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->        
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>        
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $("#login-form").submit(function () {
          $("#login-form button").prop("disabled", true);
          $.ajax({
            url: "processLogin.php",
            type: "POST",
            data: {
              operation: 'login',
              username: $("[name='username']").val(),
              password: $("[name='password']").val()
            },
            dataType: "json",
            success: function (json) {
              if (json.status == "OK") {
                create_alert("Login successful.", "alert-success");
                window.setTimeout(function () {
                  location.href = "./selectConfiguration.php";
                }, 500);
              } else {
                create_alert(json.error_description, "alert-danger");
                $("#login-form button").prop("disabled", false);
              }
            },
            error: function () {
              create_alert("An unknown error occured.", "alert-danger");
              $("#login-form button").prop("disabled", false);
            }
          });
          return false;
        })
      });
      function create_alert(msg, type) {
        $(".alert").remove();
        var alert = $("<div class='alert' />");
        alert.addClass(type);
        alert.append("<button type='button' class='close' data-dismiss='alert'>&times;</button>");
        alert.append("<p style='text-align: center;margin:0'>" + msg + "</p>");
        alert.hide();
        var $main = $("#main");
        $main.before(alert);
        if ("alert-success" == type)
          $main.hide();
        alert.fadeIn("fast");
      }
    </script>
  </head>
  <body>
    <div id="wrap">
      <div class="navbar navbar-inverse navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="container">
          <div class="navbar-header">
            <div class="logo-container">
              <h5>
                <img src="images/logo.png" alt="Catalog-on-Demand&reg; Logo" style="vertical-align: text-bottom" />
              </h5>
              <h4>Data-as-a-Service demo</h4>
            </div>
          </div>
        </div>
      </div>
      <div class="container" id="main">
        <div class="row" >
          <div class="modal-dialog" id="login-window">
            <form id="login-form">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Login</h3>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Username">
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-primary">Login</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div id="footer" class="navbar-inverse">
      <div class="container">
        <p class="text-muted credit">&copy; <?php echo date("Y") ?> Catalog-on-Demand&reg;</p>
      </div>
    </div>


  </body>
</html>