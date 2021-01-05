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

// search
if(isset($_GET['q']) && strlen(trim($_GET['q'])) > 0){

  $req_str = trim($_GET['q']);
	$str = preg_replace('/[^A-Za-z0-9\-\']/', ' ', $req_str);
	$str = trim(preg_replace('/\s\s+/', ' ', str_replace('\n', ' ', $str)));
	$keywords = explode(' ', $str);
	$query = [];
	foreach ($keywords as $key){
    $query[] = array('username' => $key);
	}
	$query = array('$or' => $query);
  $results = $conn->users->find($query);
  $results = iterator_to_array($results);
  $result_count = count($results);
}
else{
  exit(header('Location: index.php'));
}
?>
<!DOCTYPE html>
<html>
<title>Search</title>
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
        <h4 style="padding:30px 0px 12px;margin:0px;">Search</h4>
        <p><?php if($result_count > 1){echo "<b>".$result_count."</b> results";}else{echo "<b>".$result_count."</b> result";} ?> found for <b>"<?php echo $req_str;?>"</b></p>
      </div>
      <?php
      foreach($results as $result){
      ?>
      <div style="display:flex;background:#fff;overflow:hidden;margin:10px 0px;padding:10px 16px;color:#757575;border-radius:2px;align-items:center;">
        <i class="material-icons" style="font-size:80px;background:#e6e6e6;margin-right:12px;">person</i>
        <div style="display:inherit;flex-direction:column;">
          <a href="profile.php?u=<?php echo get_username($conn, $result['_id']);?>" style="display:contents;font-weight:bold">@<?php echo get_username($conn, $result['_id']);?></a>
          <span>Followers <?php echo get_followers($conn, $result['_id']);?></span>      
        </div> 
      </div>
      <?php
      }
      ?>
    </div>
  </main>
</div>
</body>
</html>