<?php
session_start();//sessionの利用開始
require("dbconnect.php");
//DBへのアクセスが必要なため、dbconnect.phpをrequireしておく。

//$_SESSION["id"]が存在し、時間が
if($_SESSION["id"] && $_SESSION["time"] + 3600 > time()){
  $_SESSION["time"] = time();//現在時刻を上書きして格納
  $members = $db->prepare("SELECT * FROM members WHERE id=?");
  $members->execute(array($_SESSION["id"]));
  $member = $members->fetch();//現在取得できたデータを保存している
  //ログインしているユーザーの情報がDBから引き出された。
} else {
  header("Location: login.php");
  //ログインが完了していない場合、ログイン画面に強制的に移動
  exit();
}

if(!empty($_POST)){//POSTが空でなければ中の処理に移動。
  if($_POST["message"] !== ""){//textareaのname="message"が空でなければDBにユーザーが入力したメッセージを登録する。
    $message = $db->prepare("INSERT INTO posts SET member_id=?, message=?, created=NOW()");
    //messageの追加なので、今回はINSERT INTO posts SET で開始。
    //***必ず「,」で区切ること。***（エラーで2時間無駄にした）
    //prepareでDBに接続し、IDやmessageは変数としてexecuteで値を格納する。
    $message->execute(array(
      $member["id"],//$_SESSION["id"]も同じ値だが、DBから取ってきた方が確実のため、$member["id"]で対応
      $_POST["message"]
    ));
    //ちなみに$_POST["message"]にデータを持ち続けてしまうため、
    //自分自身をもう一度呼び出し、POSTをリセットする。
    header("Location: index.php");//index.phpを呼び出し$_POSTのデータをリセットしているため、再読み込みをしてもmessageが重複しない。
    exit();
  }
}

$posts = $db->query("SELECT m.name, m.picture,p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC");//ユーザーが入力した値を呼び出すわけではないため、prepareではなく、queryを用いての呼び出しで構わない。

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
    <div style="text-align: right">
      <a href="logout.php">ログアウト</a>
    </div>
    <form action="" method="post">
      <dl>
        <dt><?php print(htmlspecialchars($member["name"],ENT_QUOTES));?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"></textarea>
          <input type="hidden" name="reply_post_id" value="" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>

    <div class="msg">
    <img src="member_picture" width="48" height="48" alt="" />
    <p><span class="name">（）</span>[<a href="index.php?res=">Re</a>]</p>
    <p class="day"><a href="view.php?id="></a>
<a href="view.php?id=">
返信元のメッセージ</a>
[<a href="delete.php?id="
style="color: #F33;">削除</a>]
    </p>
    </div>

<ul class="paging">
<li><a href="index.php?page=">前のページへ</a></li>
<li><a href="index.php?page=">次のページへ</a></li>
</ul>
  </div>
</div>
</body>
</html>
