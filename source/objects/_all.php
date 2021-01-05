<?php
function get_draweritems($conn){
?>
<div style="text-align:center;padding:20px 0px;color:#ffffff;background-color:#3f51b5;">
<i class="material-icons" style="padding:6px 0px;display:block;font-size:40px;">account_circle</i>
<span class="mdl-layout-title">
  <a href="profile.php?u=<?php echo $_SESSION['username'];?>" style="color:#fff;text-decoration:none;">
  @<?php echo $_SESSION['username'];?>
  </a>
</span>
<div style="padding:8px 0px 0px;">
  <span>followers <?php echo get_followers($conn, $_SESSION['id']);?></span> - 
  <span>following <?php echo get_following($conn, $_SESSION['id']);?></span>
</div>
</div>            
<nav class="demo-navigation mdl-navigation">
  <a class="mdl-navigation__link" href="index.php" style="padding: 12px 24px;"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation" style="margin-right:12px;">home</i>Home</a>
  <a class="mdl-navigation__link" href="about.php" style="padding: 12px 24px;"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation" style="margin-right:12px;">info</i>This Project</a>
  <div style="height:1px;background-color:#dcdcdc;margin:8px 0;"></div>
  <span style="font-size:12px;color:#a7a7a9;padding:6px 24px;">
    Created By: CrazyKID</br>(<a href="https://github.com/crazykiid" target="_blank">github.com/crazykiid</a>)</br>&copy; 2020-21
  </span>
</nav>
<?php
}

function post_scanner($conn, $string){
  $string = htmlspecialchars($string);
  $string = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', "<a href=\"$1\" target=\"_blank\" rel=\"nofollow\">$1</a>", $string);
  $string = preg_replace('/\n+/', '</br>', $string);
  $taglist = [];
  preg_match_all("/@(\\w+)/", $string, $tags);
  foreach($tags[1] as $tag){
    $user_status = is_active($conn, $tag);
    if($user_status){
      $taglist[] = $tag;
    }
  }
  $taglist = array_unique($taglist);
  foreach($taglist as $tag){
    $string = str_replace("@".$tag, "<a href=\"profile.php?u=".$tag."\">@".$tag."</a>", $string);
  }
  return $string;
}

function time_ago($time){
  $old = strtotime($time);
  $now = time();
  $duration = abs($now-$old);
  if($duration < 60){
    if($duration < 2){
      return $duration." second ago";
    }
    return $duration." seconds ago";
  }
  elseif($duration < 3600){
    $duration = floor($duration/60);
    if($duration < 2){
      return $duration." minute ago";
    }
    return $duration." minutes ago";
  }
  elseif($duration < 86400){
    $duration = floor($duration/3600);
    if($duration < 2){
      return $duration." hour ago";
    }
    return $duration." hours ago";
  }
  elseif($duration < 2592000){
    $duration = floor($duration/86400);
    if($duration < 2){
      return $duration." day ago";
    }
    return $duration." days ago";
  }
  elseif($duration < 31104000){
    $duration = floor($duration/2592000);
    if($duration < 2){
      return $duration." month ago";
    }
    return $duration." months ago";
  }
  else{
    $duration = floor($duration/31104000);
    if($duration < 2){
      return $duration." year ago";
    }
    return $duration." years ago";
  }
  return null;
}

function get_userdata($conn, $user){
  $result = $conn->users->findOne(array('username' => $user));
  return $result;
}

function get_username($conn, $id){
  $result = $conn->users->findOne(array('_id' => $id));
  return $result['username'];
}

function get_followers($conn, $id){
  $result = $conn->following->find(array('master' => $id));
  $followers = iterator_to_array($result);
  return count($followers);
}

function get_following($conn, $id){
  $result = $conn->following->find(array('follower' => $id));
  $following = iterator_to_array($result);
  return count($following);
}

function am_ifollow($conn, $master, $follower){
  $result = $conn->following->findOne(array('master' => $master, 'follower' => $follower));
  if($master == $follower){
    return true;
  }
  elseif($result){
    return true;
  }
  return false;
}

function is_active($conn, $username){
  $result = $conn->users->findOne(array('username' => $username));
  if($result){
    return true;
  }
  return false;
}

function is_exist_username($conn, $username){
  $result = $conn->users->findOne(array('username' => $username));
  if($result){
    return true;
  }
  return false;
}

function is_exist_email($conn, $email){
  $result = $conn->users->findOne(array('email' => $email));
  if($result){
    return true;
  }
  return false;
}
?>