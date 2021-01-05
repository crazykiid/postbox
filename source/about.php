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
?>
<!DOCTYPE html>
<html>
<title>This Project</title>
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
  </header>
  <!-- navigation drawer -->
  <div class="mdl-layout__drawer">
    <?php get_draweritems($conn);?>
  </div>
  <!-- page contents -->
  <main class = "mdl-layout__content">
    <div class="page-content" style="padding:6px 8px">
      <div style="background:#fff;overflow:hidden;margin:10px 0px;padding:10px 16px;color:#757575;border-radius:2px;">
        <h4 style="padding:30px 0px 12px;margin:0px;">This Project</h4>
        <div>
          <p>
            This project is not for production use. I was just playing with mongodb so I got this idea to create a project in php where it will save it's data in mongodb. So i picked this simple idea to create a project similer to twitter and now here we are.</br>
            In this project front-end is created with Material Design Lite and back-end is powered by PHP and MongoDB.</br>
            <b>Concept:</b><br>
            >> You can create an account with your email.<br>
            >> You can follow people so you will start receiving their posts in your public feed tab.<br>
            >> You can write your post and can add reference to someone by using @ with their username. Eg: @admin<br>
            >> You can easily search users and follow them.
          </p>
        </div>
      </div>




      
    </div>
  </main>
</div>
</body>
</html>