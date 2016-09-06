<?php
session_start();
if (!isset($_SESSION['DAASDEMO']['user_id'])) {
  header('location:login.php');
  die("Log in first...");
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
        $("#configuration-form").submit(function () {
          $("#configuration-form button").prop("disabled", true);
          var form = $("#configuration-form");
          var checkedValue = form.find("input[name=configuration]:checked").val();
          $.ajax({
            url: "processConfiguration.php",
            type: "POST",
            data: {
              index: checkedValue
            },
            dataType: "json",
            success: function (json) {
              if (json.status == "OK") {
                location.href = "./uploadSKUs.php";
              } else {
                create_alert(json.error_description, "alert-danger");
                $("#configuration-form button").prop("disabled", false);
              }
            },
            error: function () {
              create_alert("An unknown error occured.", "alert-danger");
              $("#configuration-form button").prop("disabled", false);
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
      function logout() {
        $.ajax({
          url: 'processLogin.php',
          data: {operation: 'logout'},
          modal: true
        });
      }
      ;
    </script>
  </head>
  <body>
    <div id="wrap">
      <div class="navbar navbar-inverse navbar-static-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <div class="logo-container">
              <h5>
                <img src="images/logo.png" alt="Catalog-on-Demand&reg; Logo" style="vertical-align: text-bottom" />
              </h5>
              <h4>Data-as-a-Service demo</h4>
            </div>
            <div id="user-dropdown">
              <div class="btn-group">
                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                  <span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<?php echo $_SESSION['DAASDEMO']['full_name'] ?>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                  <li><p class="text-primary"><span class="text-muted small">organization</span><br /><strong class="h4"><?php echo $_SESSION['DAASDEMO']['customer_id'] ?></strong>
                    </p></li>
                  <li><p class="text-primary">
                      <span class="text-muted small">logged in as user</span><br /><?php echo $_SESSION['DAASDEMO']['user_id'] ?>
                    </p></li>
                  <li class="divider"></li>
                  <li><a href="login.php" id="logout-btn" onclick="logout();">Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container" id="main">
        <div class="row" >
          <div id="app-container" class="col-lg-8">
            <div class="panel panel-default" style="width: 640px;">
              <form id="configuration-form">
                <div class="panel-heading">
                  <h4>Step 1: Select a configuration</h4>
                </div>
                <div class="panel-body">
                  <div class="form-group">
                    <TABLE border="0" width="640">
                      <?php
                      $index = 0;
                      $selectedindex = 0;
                      if (isset($_SESSION['DAASDEMO']['index'])) {
                        $selectedindex = $selectedindex + $_SESSION['DAASDEMO']['index'];
                      }
                      foreach ($_SESSION["DAASDEMO_COD_MobileApplicationConfiguration"] as $c) {
                        $a = explode("|", $c);
                        ?>  
                        <TR valign=top>
                          <TD style="width:30px;padding:5px;">
                            <input type="radio" <?php if ($selectedindex == $index) echo "checked"; ?> name="configuration" value="<?php echo $index; ?>" />
                          </TD>
                          <TD style="width:600px;padding:5px;">
                            <?php echo $a[1] . " (Type: " . $a[0] . ")"; ?>
                          </TD>
                        </TR>
                        <?php
                        $index++;
                      }
                      ?>  
                    </TABLE>
                  </div>
                </div>
                <div class="panel-footer" style="text-align: right">
                  <button class="btn btn-primary">Next &raquo;</button>
                </div>
              </form>
            </div>
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