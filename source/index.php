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

// action : follow
if(isset($_GET['u'])){
  if(isset($_GET['u'], $_GET['action']) && strlen(trim($_GET['u'])) > 0 && strlen(trim($_GET['action'])) > 0){

    $req_act = trim($_GET['action']);
    if($req_act == 'follow'){

      $req_user = trim($_GET['u']);
      $req_userdata = get_userdata($conn, $req_user);
      if($req_userdata){
        // start following
        $result = $conn->following->insertOne(array('master' => $req_userdata['_id'], 'follower' => $_SESSION['id']));
        if($result->getInsertedCount() > 0){
          $_SESSION['message'] = array('type'=> 'success', 'text' => 'following added');
        }
        else{
          $_SESSION['message'] = array('type'=> 'error', 'text' => 'something went wrong, try again');
        }
      }
      else{
        $_SESSION['message'] = array('type'=> 'error', 'text' => 'invalid request');
      }
    }
  }
  else{
    $_SESSION['message'] = array('type'=> 'error', 'text' => 'invalid request');
  }
  $_SESSION['tab'] = 3;
  exit(header('Location: index.php'));
}

// action : delete
if(isset($_GET['post'])){
  if(isset($_GET['post'], $_GET['action']) && strlen(trim($_GET['post'])) > 0 && strlen(trim($_GET['action'])) > 0){

    $req_act = trim($_GET['action']);
    if($req_act == 'delete'){

      $req_post = trim($_GET['post']);
      $_SESSION['tab'] = 2;
      // delete post
      $result = $conn->posts->updateOne(
        array('_id' => new MongoDB\BSON\ObjectID($req_post), 'post_status' => 1),
        array('$set' => array('post_status' => 0))
      );
      if($result->getModifiedCount() > 0){
        $_SESSION['message'] = array('type'=> 'success', 'text' => 'post removed');
      }
      else{
        $_SESSION['message'] = array('type'=> 'error', 'text' => 'something went wrong, try again');
      }
      exit(header('Location: index.php'));
    }
  }
  else{
    $_SESSION['message'] = array('type'=> 'error', 'text' => 'invalid request');
  }
  $_SESSION['tab'] = 2;
  exit(header('Location: index.php'));
}

// action : post
if(isset($_POST['post'])){
  if(isset($_POST['text']) && strlen(trim($_POST['text'])) > 0){

    $post_text = trim($_POST['text']);
    $post_author = $_SESSION['id'];
    $post_time = date('Y-m-d H:i:s');

    // save post
    $result = $conn->posts->insertOne(array('author_id' => $post_author, 'post' => $post_text, 'time' => $post_time, 'post_status' => 1));

    if($result->getInsertedCount() > 0){
      $_SESSION['message'] = array('type'=> 'success', 'text' => 'posted successfully');
    }
    else{
      $_SESSION['message'] = array('type'=> 'error', 'text' => 'something went wrong, try again');
    }
  }
  else{
    $_SESSION['message'] = array('type'=> 'error', 'text' => 'invalid request');
  }
  $_SESSION['tab'] = 2;
  exit(header('Location: index.php'));
}

// get all user's posts
$get_following = $conn->following->find(array('follower' => $_SESSION['id']));
$following_data = iterator_to_array($get_following);
$following = [];
foreach($following_data as $fd){
  $following[] = $fd['master'];
}
$get_allposts = $conn->posts->find(array('author_id' => array('$in' => $following), 'post_status' => 1), array('sort' => array('_id' => -1), 'limit' => 12));
$all_posts = iterator_to_array($get_allposts);

// get user's posts
$get_userposts = $conn->posts->find(array('author_id' => $_SESSION['id'], 'post_status' => 1), array('sort' => array('_id' => -1), 'limit' => 12));
$user_posts = iterator_to_array($get_userposts);

//users list
$get_allusers = $conn->users->find();
$all_users = iterator_to_array($get_allusers);
?>
<!DOCTYPE html>
<html>
<title>Home</title>
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
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header mdl-layout--fixed-tabs">
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
    <div class="mdl-layout__tab-bar mdl-js-ripple-effect" style="height: unset;">
      <a href="#posts" class="mdl-layout__tab <?php if(!isset($_SESSION['tab'])){ echo "is-active";}?>">
        <i class="material-icons" style="padding: 12px;">public</i>
      </a>
      <a href="#publish" class="mdl-layout__tab <?php if(isset($_SESSION['tab']) && $_SESSION['tab'] == 2){ echo "is-active";}?>">
        <i class="material-icons" style="padding: 12px;">edit</i>
      </a>
      <a href="#people" class="mdl-layout__tab <?php if(isset($_SESSION['tab']) && $_SESSION['tab'] == 3){ echo "is-active";}?>">
        <i class="material-icons" id="to-3" style="padding: 12px;">people_outline</i>
      </a>
    </div>
  </header>
  <!-- navigation drawer -->
  <div class="mdl-layout__drawer">
    <?php get_draweritems($conn);?>
  </div>
  <!-- page contents -->
  <main class = "mdl-layout__content">
    <!-- posts area -->
    <section class="mdl-layout__tab-panel <?php if(!isset($_SESSION['tab'])){ echo "is-active";}?>" id="posts">
      <div class="page-content" style="padding:6px 8px">
      <?php
      foreach($all_posts as $post){
      ?>
        <div class="mdl-card mdl-shadow--2dp" style="width: unset;min-height: unset;margin:10px 0px;">
          <div class="mdl-card__supporting-text meta mdl-color-text--grey-600" style="display:flex;flex-direction: row;padding: 10px 15px;width: unset;    border-bottom: 1px solid #e2e2e2;align-items: center;">
              <i class="material-icons" style="font-size: 38px;margin-right:6px;">account_circle</i>
              <div style="display:flex;flex-direction:column;">
                <a href="profile.php?u=<?php echo get_username($conn, $post['author_id']);?>" style="display:contents;font-weight:bold">@<?php echo get_username($conn, $post['author_id']);?></a>
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
    </section>
    <!-- publish area -->
    <section class="mdl-layout__tab-panel <?php if(isset($_SESSION['tab']) && $_SESSION['tab'] == 2){echo "is-active";}?>" id="publish">
      <div class="page-content" style="padding:6px 8px">
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
        <!-- create post -->
        <div class="mdl-card mdl-shadow--2dp" style="width: unset;min-height: unset;margin:10px 0px;">
          <div class="" style="font-size: 20px;padding: 14px 16px;color: #757575;min-height: 60px;">
            <form action="" method="post">
              <div class="mdl-textfield mdl-js-textfield" style="width: 100%;">
                <textarea class="mdl-textfield__input" type="text" rows= "3" id="sample5" name="text"></textarea>
                <label class="mdl-textfield__label" for="sample5">Whats up?</label>
              </div>
              <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="" name="post">
                Post
              </button>
            </form>
          </div>
        </div>
        <!-- user posts -->
        <?php
        foreach($user_posts as $post){
        ?>
        <div class="mdl-card mdl-shadow--2dp" style="width:unset;min-height:unset;margin:10px 0px;">
          <div class="mdl-card__supporting-text meta mdl-color-text--grey-600" style="display:flex;flex-direction: row;padding:5px 15px;width:unset;border-bottom:1px solid #e2e2e2;align-items:center;">
              <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                <div style="display:inherit;">
                  <i class="material-icons" style="font-size:18px;margin-right:5px;">edit</i>
                  <span>Posted <?php echo time_ago($post['time']);?></span>
                </div>
                <a href="?post=<?php echo $post['_id'];?>&action=delete" class="mdl-button mdl-js-button mdl-button--icon">
                  <i class="material-icons" style="font-size:18px;">delete_outline</i>
                </a>
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
    </section>
    <!-- people area -->
    <section class="mdl-layout__tab-panel <?php if(isset($_SESSION['tab']) && $_SESSION['tab'] == 3){echo "is-active";}?>" id="people">
      <div class="page-content" style="padding:8px 0px;">
        <!-- response -->
        <?php
        if(isset($_SESSION['message'], $_SESSION['tab']) && $_SESSION['tab'] == 3){
          if($_SESSION['message']['type'] == 'success'){
            echo "<span style=\"display:flex;align-items:center;color:#3bca00;padding:9px 12px;border-radius:2px;background-color:#fff;margin:0px 8px;\"><i class=\"material-icons\">done</i> ".$_SESSION['message']['text']."</span>";
          }
          if($_SESSION['message']['type'] == 'error'){
            echo "<span style=\"display:flex;align-items:center;color:#ef1414;padding:9px 12px;border-radius:2px;background-color:#fff;margin:0px 8px;\"><i class=\"material-icons\">error_outline</i> ".$_SESSION['message']['text']."</span>";
          }
          unset($_SESSION['message'], $_SESSION['tab']);
        }
        ?>
        <!-- people list -->
        <div class="mdl-grid" style="padding:0px;">
          <?php
          foreach($all_users as $user){
            if(am_ifollow($conn, $user['_id'], $_SESSION['id'])){
              continue;
            }
          ?>
          <div class="mdl-cell mdl-cell--4-col mdl-cell--12-col-tablet mdl-cell--12-col-phone mdl-card mdl-shadow--2dp" style="min-height:unset;">
            <div class="mdl-card__supporting-text meta mdl-color-text--grey-600" style="padding:10px 15px;width:unset;border-bottom:1px solid #e2e2e2;align-items:center;">
              <i class="material-icons" style="font-size:38px;margin-right:6px;">account_circle</i>
              <div style="display:flex;flex-direction:column;">
                <a href="profile.php?u=<?php echo get_username($conn, $user['_id']);?>" style="display:contents;font-weight:bold">@<?php echo get_username($conn, $user['_id']);?></a>
                <span>Followers <?php echo get_followers($conn, $user['_id']);?></span>
                <a href="?u=<?php echo get_username($conn, $user['_id']);?>&action=follow" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" style="max-width:100px;margin-top:10px;">
                  Follow
                </a>
              </div>            
            </div>
          </div>  
          <?php
          }
          ?>
        </div>
      </div>
    </section>
  </main>
</div>
</body>
</html>