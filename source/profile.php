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

// user
if(isset($_GET['u']) && strlen($_GET['u']) > 0){

  $req_username = trim($_GET['u']);
  $userdata = get_userdata($conn, $req_username);
  if($userdata){

    if($userdata['_id'] == $_SESSION['id']){
      $f_actions = false;
    }
    else{
      $f_actions = true;
      $f_status = am_ifollow($conn, $userdata['_id'], $_SESSION['id']);

      //actions
      if(isset($_GET['action']) && trim($_GET['action']) == 'follow' && $f_status == false){
        $status = $conn->following->insertOne(array('master' => $userdata['_id'], 'follower' => $_SESSION['id']));
        exit(header('Location: ?u='.$userdata['username']));
      }
      elseif(isset($_GET['action']) && trim($_GET['action']) == 'unfollow' && $f_status == true) {
        $status = $conn->following->deleteOne(array('master' => $userdata['_id'], 'follower' => $_SESSION['id']));
        exit(header('Location: ?u='.$userdata['username']));
      }
    }
    // get user's posts
    $get_userposts = $conn->posts->find(array('author_id' => $userdata['_id'], 'post_status' => 1), array('sort' => array('_id' => -1), 'limit' => 12));
    $user_posts = iterator_to_array($get_userposts);
  }
  else{
    exit(header('Location: index.php'));
  }
}
else{
  exit(header('Location: index.php'));
}
?>
<!DOCTYPE html>
<html>
<title><?php echo '@'.$userdata['username'];?></title>
<head>
  <meta name="viewport" content="width=device-width, minimal-ui, user-scalable=no">
  <link rel="stylesheet" href="assets/css/icon.css">
  <link rel="stylesheet" href="assets/css/material.indigo-pink.min.css">
  <script defer src="assets/js/material.min.js"></script>
  <style type="text/css">
  body{
    background-color: #e8e8e8;
  }
  .post{
    padding:14px 16px;color:#757575;overflow-wrap: break-word;word-wrap: break-word;-ms-word-break: break-all;word-break: break-all;word-break: break-word;-ms-hyphens: auto;-moz-hyphens: auto;-webkit-hyphens: auto;hyphens: auto;}
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
      <!-- profile info -->
      <div class="mdl-card mdl-shadow--2dp" style="width:100%;margin-top:10px;">
        <div class="mdl-card__title mdl-card--expand">
          <div style="color:#3f51b5;display:flex;">
            <div style="align-items: center;display: inherit;">
              <i class="material-icons" style="font-size:80px;background: #e6e6e6;">person</i>
              <div style="display:inherit;flex-direction:column;padding:27px 0px 0px 4px;">
                <span style="font-size:32px;font-weight:bold;"><?php echo '@'.$userdata['username'];?>
                </span>
                <span style="font-size:14px;">
                  (Followers: <?php echo get_followers($conn, $userdata['_id']);?> - 
                  Following: <?php echo get_following($conn, $userdata['_id']);?>)
                </span>
              </div>
            </div>
          </div>
        </div>
        <?php if(isset($f_actions) && $f_actions == true){ ?>
        <div class="mdl-card__actions mdl-card--border">
          <?php if($f_status){ ?>
          <a href="?u=<?php echo $userdata['username'];?>&action=unfollow" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
            Unfollow
          </a>
          <?php } else{ ?>
          <a href="?u=<?php echo $userdata['username'];?>&action=follow" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
            Follow
          </a>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <!-- user posts -->
      <?php
      foreach($user_posts as $post){
      ?>
        <div class="mdl-card mdl-shadow--2dp" style="width: unset;min-height: unset;margin:10px 0px;">
          <div class="mdl-card__supporting-text meta mdl-color-text--grey-600" style="display:flex;flex-direction: row;padding: 10px 15px;width: unset;    border-bottom: 1px solid #e2e2e2;align-items: center;">
              <i class="material-icons" style="font-size: 38px;margin-right:6px;">account_circle</i>
              <div style="display:flex;flex-direction:column;">
                <span style="font-weight:bold">@<?php echo get_username($conn, $post['author_id']);?></span>
                <span><?php echo time_ago($post['time']);?></span>
              </div>
          </div>
          <p class="post">
            <?php echo post_scanner($conn, $post['post']);?>
          </p>
        </div>
      <?php
      }
      ?>
    </div>
  </main>
</div>
</body>
</html>