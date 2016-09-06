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
      var timerId;
      function setTimer() {
        timerId = setInterval('checkState()', 5000);
      }
      $(document).ready(function () {
        setTimer();
      });
      function checkState() {
        if (timerId) {
          clearInterval(timerId);
          timerId = null;
        }
        try {
          $.ajax({
            url: "processTrack.php",
            data: ({action: "processTrack"}),
            type: "POST",
            dataType: "json",
            success: function (json) {
              var status = json.status;
              var OPSJobID = json.OPSJobID;
              var StateValue = json.StateValue;
              var StateDescription = json.StateDescription;
              var ErrorMessage = json.ErrorMessage;
              var OutputURL = json.OutputURL;

              //alert("sessionID="+json.SessionID+" processID="+json.ProcessID);
              //alert("status="+status+" OPSJobID="+OPSJobID+" StateValue="+StateValue+" StateDescription="+StateDescription+" ErrorMessage="+ErrorMessage+" OutputURL="+OutputURL);

              var state = parseInt(StateValue);
              var error = false;
              if (state == 67 && OutputURL) {
              } else if (state == 67 && !OutputURL) {
                error = "PDF URL is not available. Please contact the Administrator.";
              } else if (state > 67) {
                if (OPSJobID) {
                  // switch to error
                  error = "PDF could not be prepared. Please contact the Administrator.<br/><br/>Publication job ID: " + OPSJobID + "<br/><br/>Error message: <font color='@AA0000'>" + (ErrorMessage ? ErrorMessage : StateDescription) + "</font>";
                } else {
                  error = "PDF could not be prepared.<br/><br/>Error message: <font color='@AA0000'>" + (ErrorMessage ? ErrorMessage : StateDescription + "<br/><br/>Please contact the Administrator.") + "</font>";
                }
              }
              // write state
              if (state > 67) {
                StateDescription = "<fint color='#AA0000'>" + StateDescription + "</font>";
              }
              $("#jobStatecell").text(StateDescription);
              // write url
              if (state == 67 && OutputURL) {
                $("#jobURL").prop("href", OutputURL);
              }
              if (state < 67) {
                setTimer();
              } else {
                if (error) {
                  create_alert(error, "alert-danger");
                } else {
                  // switch
                  $("#jobState").css("display", "none");
                  $("#jobResult").css("display", "");
                }
              }
            },
            error: function () {
              create_alert("Runtime submission call error occurred.\r\nPlease contact the Administrator.", "alert-danger");
              setTimer();
            }
          });
        } catch (e) {
          create_alert("Runtime error occurred: " + e.message + "\r\nPlease contact the Administrator.", "alert-danger");
          setTimer();
        }
      }
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
      function trim(str) {
        if (typeof str != "string")
          return str;
        var retVal = str;
        var ch = retVal.charCodeAt(0);
        while (ch <= 32) {
          retVal = retVal.substring(1, retVal.length);
          ch = retVal.charCodeAt(0);
        }
        ch = retVal.charCodeAt(retVal.length - 1);
        while (ch <= 32) {
          retVal = retVal.substring(0, retVal.length - 1);
          ch = retVal.charCodeAt(retVal.length - 1);
        }
        return retVal;
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
            <div id="jobState" class="panel panel-default" style="width: 640px;" >
              <div class="panel-heading">
                <h4>Step 4: obtain PDF link</h4>
              </div>
              <div class="panel-body">
                <div class="form-group">
                  Your PDF will be ready soon. Please do not close this page. It might take several minutes, depending on how many requests are in the queue ahead of you.
                </div>
                <div class="form-group">
                  <TABLE border="0" width="640">
                    <TR valign=top>
                      <TD noWrap style="padding:5px;text-align:right;">Job ID :</TD>
                      <TD id="jobIDcell" style="width:600px;padding:5px;font-weight:bold;"><? echo $_SESSION['DAASDEMO']['JobID']; ?></TD>
                    </TR>
                    <TR valign=top>
                      <TD noWrap style="padding:5px;text-align:right;">Job state :</TD>
                      <TD id="jobStatecell" style="width:600px;padding:5px;font-weight:bold;">Pending in the Catalog-on-Demand system queue</TD>
                    </TR>
                  </TABLE>
                </div>
              </div>
            </div>
            <div id="jobResult" class="panel panel-default" style="width: 640px;display:none;" >
              <div class="panel-heading">
                <h4>Step 5: download PDF</h4>
              </div>
              <div class="panel-body">
                <div class="form-group">
                  Your PDF is ready. <a id="jobURL" href="" target="_blank"><b>View / Download</b></a>
                </div>
              </div>
              <div class="panel-footer" style="text-align: right">
                <button class="btn btn-primary" onclick="location.href = './selectConfiguration.php';">New publication</button>
              </div>
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