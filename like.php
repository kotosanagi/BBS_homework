<?php
require_once("define.php");

//db接続情報の定義
define("DB_HOST",$define["host"]);
define("DB_USER",$define["user"]);
define("DB_PASS",$define["pass"]);
define("DB_NAME",$define["name"]);

$id = $_GET['message_id'];

if(!empty($_GET['message_id'])){
  $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if($mysqli->connect_errno){
    $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
  }else{
    $sql = "SELECT like_count FROM homework2 WHERE id = $id";
    $res = $mysqli->query($sql);
    if($res){
      $ret = $res->fetch_all(MYSQLI_ASSOC);//fetch_allできていない？
    }
    $mysqli->close();
  }
}


foreach ($ret as $value){
  $like = $value['like_count'];
}

$like += 1;

$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($mysqli->connect_errno){
  $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
}else{
  $mysqli->set_charset('utf8');
  $sql = "UPDATE homework2 SET like_count = $like WHERE id = $id";
  $res = $mysqli->query($sql);
  var_dump($res);
  $mysqli->close();
}

header("Location: ./index.php");
?>
