<?php

require_once("define.php");

//db接続情報の定義
define("DB_HOST",$define["host"]);
define("DB_USER",$define["user"]);
define("DB_PASS",$define["pass"]);
define("DB_NAME",$define["name"]);

session_start();
//変数の初期化
$error_message = array();
$clean = array();
$message_array = array();

//dbへデータを送信する
if(!empty($_POST['btn_submit'])){

  date_default_timezone_set('Asia/Tokyo');

  $week_name = array("Sun","Mon","Tue","We  d","Thu","Fri","Sat");
  $w = date('w');
  $view_date = date("Y/m/d")."(".$week_name[$w].") ".date("H:i:s");

  //  view_nameのバリデーション(空白のチェック)
  if(empty($_POST['view_name'])) {
    $error_message[] = "please fill your name!";
  }else{//  view_nameのサニタイズ(無害化)
    $clean['view_name'] = htmlspecialchars($_POST['view_name'],ENT_QUOTES);
  }

  //  messageのバリデーション(空白のチェック)
  if(empty($_POST['message'])){
    $error_message[] = "please fill your message!";
  }else{//  messageのサニタイズ(無害化)
    $clean['message'] = htmlspecialchars($_POST['message'],ENT_QUOTES);
  }


  if(empty($error_message)) {
    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

    if($mysqli->connect_errno){
      $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
    }else{
      $mysqli->set_charset('utf8');

      $sql = "INSERT INTO homework2(view_name,message,view_date,like_count) VALUES('$clean[view_name]','$clean[message]','$view_date',0)";
      $res = $mysqli->query($sql);

      if($res) {
        $_SESSION['success_message'] = 'success!!';
      }else{
        $error_message[] = 'fail to comment...';
      }

      $mysqli->close();
    }
    header("Location: ./");
  }
}

//dbからデータを受け取る
$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if($mysqli->connect_errno){
  $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
}else{
  $sql = "SELECT id,view_name,message,view_date,like_count FROM homework2 order by id desc";
  $res = $mysqli->query($sql);

  if($res){
    $message_array = $res->fetch_all(MYSQLI_ASSOC);
  }

  $mysqli->close();
}
?>


<?php
//アクセスカウンタ

if(empty($_POST['btn_submit'])){
  $fp = fopen('count.txt','r+');
  if($fp){
    if(flock($fp,LOCK_EX)){
      $counter = fgets($fp,12);
      $counter += 1;
      rewind($fp);
      if(fwrite($fp,$counter) === FALSE){
        echo ('<p>'.'fail to count'.'</p>');
      }
      flock($fp,LOCK_UN);
    }
  }
  fclose($fp);
}

?>



<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>homework BBS Ⅱ</title>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<style>

body{
  background-color: #eef;
}


h1{
  font-size: 30px;
  text-align: center;
}

/***********************
アクセスカウンタ
************************/
.counter{
  text-align: center;
  width: 200px;
  margin: 0 auto 30px auto ;
  font-weight: 20px;
}

/***********************
結果表示部分
************************/
.success_message{
  border:solid 1px;
  border-color: blue;
  padding: 15px;
  margin: 0 90px;
  border-radius: 8px;
  list-style-type: none;
}

.error_message{
  border:solid 1px;
  border-color: red;
  padding: 15px;
  margin: 0 90px;
  border-radius: 8px;
  list-style-type: none;
}

/***********************
入力部分
************************/

form{
  background-color: white;
  margin: 15px 90px;
  padding-bottom: 15px;
}

input[type="text"]{
  width: 180px;
  height: 20px;
  border-radius: 5px;
}

textarea{
  width: 70%;
  height: 70px;
  border-radius: 5px;
}

.input-view_name{
  margin:15px;
  padding-top: 10px;
}

.input-message{
  margin:5px 15px;
}

.input-btn_submit{
  margin: 0 15px;
  background: #96ddff;
  padding:8px;
  border-radius: 10px;
}

.input-btn_submit:hover{
  background-color: #40b4df;
  cursor: pointer;
  border-radius: 10px;
}

hr{
  margin: 30px;
}
/***********************
表示部分
************************/

.comment_view{
  background-color: #fff;
  margin: 15px 90px;
  padding : 2px 0px 0px 0px;
}

.comment_name{
  margin: 0 15px 5px 15px;
  font-weight: bold;
  float :left;
}

.comment_date{
  font-size: 14px;
  margin:4px 16px 0 0;
  float:left;
}

.comment_message{
  margin:5px 15px 0 15px;
  padding-bottom: 5px;
  clear:left;
}

.fa-thumbs-o-up{
  border:none;
  float: left;
}

.fa {
  margin :4px 5px 0 0;
  height:20px;
  color:#000;
}


</style>
</head>

<body>
<h1>homework BBS Ⅱ</h1>

<div class="counter">
  <?php
  $fp = fopen("count.txt","r");
  if($fp){
    $count = fgets($fp,12);
    echo '<p>you are '.$count.' th visitor!</p>';
  }
  fclose($fp);
  ?>
</div>

<!-- success_messageのチェック -->

<?php if(empty($_POST['btn_submit']) && !empty($_SESSION['success_message'])): ?>
  <ul class="success_message">
    <li>・<?php echo $_SESSION['success_message'];?></li>
    <?php unset($_SESSION['success_message']) ?>
  </ul>
<?php endif ?>

<!-- $error_messageのチェック -->
<?php if(!empty($error_message)): ?>
  <ul class="error_message">
    <?php foreach($error_message as $value): ?>
      <li>・<?php echo $value."<br>"; ?></li>
    <?php endforeach ?>
  </ul>
<?php endif ?>


<form method="post">
  <!-- view_name input -->
  <div class="input-view_name">
    <label for="view_name">your name</label>
    <br>
    <input id="view_name" type="text" name="view_name" value="">
  </div>
  <!-- message_id input -->
  <div class="input-message">
    <label for="message">message</label>
    <br>
    <textarea id="message" name="message"></textarea>
  </div>
  <!-- btn_submit input -->
  <input class="input-btn_submit" type="submit" name="btn_submit" value="comment!" >
</form>
<hr>

<section>


<article>
<?php foreach ($message_array as $value): ?>

  <div class="comment_view">
    <div class="comment_name">
      <?php echo $value['view_name']."<br>"; ?>
    </div>
    <div class="comment_date">
      <?php echo $value['view_date']; ?>
    </div>
    <div class="view_like">
      <a href="like.php?message_id=<?php echo $value['id']; ?>"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></a>
    </div>
    <div class="like_count">
      <?php if($value['like_count'] !== "0"): ?>
        <?php echo "+".$value['like_count'] ?>
      <?php endif; ?>
    </div>
    <div class="comment_message">
      <?php echo $value['message']."<br>"; ?>
    </div>
  </div>

<?php endforeach ?>
</article>
</section>
</body>
</html>
