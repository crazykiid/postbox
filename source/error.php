<?php
require "vendor/autoload.php";
require "config/_database.php";

session_start();

// database connection
$obj_db = new Database;
$conn = $obj_db->getConnection();

// errors
if(isset($_GET['e'])){

  $e = trim($_GET['e']);

  if($e == "400" || $e == "401" || $e == "403" || $e == "404"){
    $error = $e;
  }
  else{
    $error = "unknown";
  }
}
?>
<!DOCTYPE html>
<html>
<title>Error</title>
<head>
	<meta name="viewport" content="width=device-width, minimal-ui, user-scalable=no">
  <link rel="stylesheet" href="/assets/css/icon.css">
  <link rel="stylesheet" href="/assets/css/material.indigo-pink.min.css">
  <script defer src="/assets/js/material.min.js"></script>
  <style type="text/css">
  body{
    background-color:#e8e8e8;
  }
  </style>
</head>
<body>
<div class="mdl-layout__container">
	<div class="mdl-layout mdl-js-layout is-upgraded" data-upgraded=",MaterialLayout">
    <div style="background-color:#fff;width:330px;margin:100px auto;text-align:center;padding:30px 0px;color:#F44336;font-size:18px;">
      <?php echo "Error ".$error;?>
    </div>
  </div>
</div>
</body>
</html>