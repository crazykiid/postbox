<?php
require "vendor/autoload.php";
require "config/_database.php";
require "objects/_all.php";

session_start();
// validate auth
if(!isset($_SESSION['username'])){
  header('Location: login.php');
}
// database connection
$obj_db = new Database;
$conn = $obj_db->getConnection();

// details
$user_data = $conn->users->findOne(array('_id' => $_SESSION['id']));

// action : update
if(isset($_POST['update'])){
  if(isset($_POST['password']) && strlen($_POST['password']) > 0){

    $new_pass = trim($_POST['password']);
    // do signup
    try{
      $result = $conn->users->updateOne(
        array('_id' => $_SESSION['id']),
        array('$set' => array('password' => hash('sha256', $new_pass)))
      );
    }
    catch(exception $e) {
      echo $e->getMessage();
      $result = false;
    }
    if($result){
      $_SESSION['message'] = array('type'=> 'success', 'text' => 'password updated successfully');
      unset($_POST['username'], $_POST['email']);
    }
    else{
      $_SESSION['message'] = array('type'=> 'error', 'text' => 'something went wrong, try again');
    }    
  }
  else{
    $_SESSION['message'] = array('type'=> 'error', 'text' => 'invalid password');
  }
  $_SESSION['tab'] = 2;
  exit(header('Location: setting.php'));
}
?>
<!DOCTYPE html>
<html>
<title>Account Setting</title>
<head>
  <meta name="viewport" content="width=device-width, minimal-ui, user-scalable=no">
  <link rel="stylesheet" href="assets/css/icon.css">
  <link rel="stylesheet" href="assets/css/material.indigo-pink.min.css">
  <script defer src="assets/js/material.min.js"></script>
  <style type="text/css">
  body{
    background-color: #e8e8e8;
  }
  </style>
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
  <!-- header -->
  <header class="mdl-layout__header">
    <div class="mdl-layout__header-row">
      <!-- Title -->
      <span class="mdl-layout-title">POSTBOX</span>
      <!-- Spacer -->
      <div class="mdl-layout-spacer"></div>
      <!-- Searchbar -->
      <form action="search.php" method="get">
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable mdl-textfield--floating-label mdl-textfield--align-right" style="margin-left:16px;margin-right:16px;">
          <label class="mdl-button mdl-js-button mdl-button--icon" for="search">
            <i class="material-icons">search</i>
          </label>
          <div class="mdl-textfield__expandable-holder">
            <input class="mdl-textfield__input" type="text" name="q" id="search">
          </div>
        </div>  
      </form>
      <button id="demo-menu-lower-right" class="mdl-button mdl-js-button mdl-button--icon">
        <i class="material-icons">more_vert</i>
      </button>
      <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="demo-menu-lower-right">
        <a href="setting.php" style="text-decoration:none;"><li class="mdl-menu__item">Account Setting</li></a>
        <a href="logout.php" style="text-decoration:none;"><li class="mdl-menu__item">Logout</li></a>
      </ul>
    </div>
    <!-- Tabs -->
    <div class="mdl-layout__tab-bar mdl-js-ripple-effect">
      <a href="#details" class="mdl-layout__tab <?php if(!isset($_SESSION['tab'])){ echo "is-active";}?>">Details</a>
      <a href="#ac-pass" class="mdl-layout__tab <?php if(isset($_SESSION['tab']) && $_SESSION['tab'] == 2){ echo "is-active";}?>">Account Password</a>
    </div>
  </header>

  <!-- navigation drawer -->
  <div class="mdl-layout__drawer">
    <?php get_draweritems($conn);?>
  </div>
  <!-- page contents -->
  <main class = "mdl-layout__content">
      <section class="mdl-layout__tab-panel <?php if(!isset($_SESSION['tab'])){ echo "is-active";}?>" id="details">
        <div class="page-content" style="padding:6px 8px;">
          <div style="background:#fff;overflow:hidden;margin:10px 0px;padding:10px 16px;color:#757575;border-radius:2px;">
            <h4 style="padding:30px 0px 12px;margin:0px;">Account Details</h4>
            <div>
              <p>
                Username : <?php echo "<a href=\"profile.php?u=".$user_data['username']."\">".$user_data['username']."</a>";?><br>
                Email : <?php echo "<a href=\"mailto:".$user_data['email']."\">".$user_data['email']."</a>";?>
              </p>
            </div>
          </div>
        </div>
      </section>
      <section class="mdl-layout__tab-panel <?php if(isset($_SESSION['tab']) && $_SESSION['tab'] == 2){ echo "is-active";}?>" id="ac-pass">
        <div class="page-content" style="padding:6px 8px;">
          <!-- response -->
          <?php
          if(isset($_SESSION['message'], $_SESSION['tab']) && $_SESSION['tab'] == 2){
            if($_SESSION['message']['type'] == 'success'){
              echo "<span style=\"display:flex;align-items:center;color:#3bca00;padding:9px 12px;border-radius:2px;background-color:#fff;\"><i class=\"material-icons\">done</i> ".$_SESSION['message']['text']."</span>";
            }
            if($_SESSION['message']['type'] == 'error'){
              echo "<span style=\"display:flex;align-items:center;color:#ef1414;padding:9px 12px;border-radius:2px;background-color:#fff;\"><i class=\"material-icons\">error_outline</i> ".$_SESSION['message']['text']."</span>";
            }
            unset($_SESSION['message'], $_SESSION['tab']);
          }
          ?>
          <div style="background:#fff;overflow:hidden;margin:10px 0px;padding:10px 16px;color:#757575;border-radius:2px;">
            <h4 style="padding:30px 0px 12px;margin:0px;">Change Password</h4>
            <div>
              <form action="" method="post">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                  <input class="mdl-textfield__input" type="password" id="password" name="password">
                  <label class="mdl-textfield__label" for="password">New Password</label>
                </div>
                <div>
                  <button id="login-button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored mdl-color-text--white" data-upgraded=",MaterialButton,MaterialRipple" name="update">
                    Update
                  </button>
                </div>
                
              </form>
            </div>
          </div>
        </div>
      </section>      
    </div>
  </main>
</body>
</html>