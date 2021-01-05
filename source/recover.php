<?php
require "vendor/autoload.php";
require "config/_database.php";

session_start();
// validate auth
if(isset($_SESSION['username'])){
  header('Location: index.php');
}
// database connection
$obj_db = new Database;
$conn = $obj_db->getConnection();

// action : login
if(isset($_POST['recover'])){
  if(isset($_POST['email']) && strlen($_POST['email']) > 0){
    $req_email = trim($_POST['email']);

    // email varification
    //try{
      //$result = $conn->users->findOne();
    //}
    //catch(exception $e) {
    //  echo $e->getMessage();
    //  $result = false;
    //}
    $result = true;
    if($result){
      $_SESSION['message'] = array('type'=> 'success', 'text' => 'password reset link sent');
    }
    else{
      $_SESSION['message'] = array('type'=> 'error', 'text' => 'something went wrong, try again');
    }
  }
  else{
    $_SESSION['message'] = array('type'=> 'error', 'text' => 'enter a valid email');
  }
}
?>
<!DOCTYPE html>
<html>
<title>Recover</title>
<head>
  <meta name="viewport" content="width=device-width, minimal-ui, user-scalable=no">
  <link rel="stylesheet" href="assets/css/icon.css">
  <link rel="stylesheet" href="assets/css/material.indigo-pink.min.css">
  <script defer src="assets/js/material.min.js"></script>
  <style type="text/css">
  body{
    background-color:#e8e8e8;
  }
  </style>
</head>
<body>

<div class="mdl-layout__container">
  <div class="mdl-layout mdl-js-layout is-upgraded" data-upgraded=",MaterialLayout">
    <div class="mdl-card mdl-shadow--16dp" style="margin:auto;">
      <div class="mdl-card__title mdl-card--expand" style="background-color:#3f51b5;">
        <i class="material-icons" style="color:#fff;padding-right:6px;">vpn_key</i>
        <h5 style="color:#fff;margin:12px 0px;">Recover</h5>
      </div>
      <div class="mdl-card__supporting-text">
        <form action="" method="post">
          <?php
          // response message will appair here
          if(isset($_SESSION['message'])){
            if($_SESSION['message']['type'] == 'success'){
              echo "<span style=\"display:flex;align-items:center;color:#3bca00;padding:6px;border-bottom:1px solid #3bca00;border-left:5px solid #3bca00;margin-bottom:12px;\"><i class=\"material-icons\" style=\"margin-right:4px;\">done</i> ".$_SESSION['message']['text']."</span>";
            }
            elseif($_SESSION['message']['type'] == 'error') {
              echo "<span style=\"display:flex;align-items:center;color:#ef1414;padding:6px;border-bottom:1px solid #ef1414;border-left:5px solid #ef1414;margin-bottom:12px;\"><i class=\"material-icons\" style=\"margin-right:4px;\">error_outline</i> ".$_SESSION['message']['text']."</span>";
            }
            unset($_SESSION['message']);
          }
          ?>
          <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" >
            <input class="mdl-textfield__input" type="email" id="email" name="email">
            <label class="mdl-textfield__label" for="email">Registered Email</label>
          </div>
          <div>
            <button id="recover-button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored mdl-color-text--white" data-upgraded=",MaterialButton,MaterialRipple" name="recover">
              Submit
            </button>
            <a href="login.php" class="mdl-button mdl-js-button mdl-button--primary">Login</a>
          </div>
        </form>
      </div>
      <div class="mdl-card__actions"> 
      </div>
    </div>
  </div>
</div>


</body>
</html>