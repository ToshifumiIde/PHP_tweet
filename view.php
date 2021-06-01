<?php
session_start();
require("dbconnect.php");

//urlパラメータが空の場合、トップに強制移動しexit()を実行
if(empty($_REQUEST["id"])){
  header("Location: index.php");
  exit();
}

//dbから
$posts = $db->prepare("SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.id=?");
$posts->execute(array(
  $_REQUEST["id"]//urlパラメータから取得されたidを1件取得する
));

?>


<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  <p>&laquo;<a href="index.php">一覧にもどる</a></p>

  <?php if($post = $posts->fetch()): ?>
    <div class="msg">
    <img src="member_picture/<?php print(htmlspecialchars($post["picture"],ENT_QUOTES)) ;?>" width="200" height="200"/>
    <p>
      <?php print(htmlspecialchars($post["message"] , ENT_QUOTES)) ;?>
      <span class="name">（<?php print(htmlspecialchars($post["name"],ENT_QUOTES));?>）</span>
    </p>
    <p class="day">
    <?php print(htmlspecialchars($post["created"],ENT_QUOTES));?>
    </p>
    </div>
  <?php else: ?>
    <p>その投稿は削除されたか、URLが間違えています</p>
  <?php endif; ?>
  </div>
</div>
</body>
</html>
