<?php
session_start();//sessionの開始
require("dbconnect.php");//DB接続。dbconnect.phpをrequireしておく。

//
//$_SESSION["id"]が存在し、時間が1時間以上経過していた場合
if($_SESSION["id"] && $_SESSION["time"] + 3600 > time()){
  $_SESSION["time"] = time();//$_SESSION_idに現在時刻を上書きして格納
  $members = $db->prepare("SELECT * FROM members WHERE id=?");
  $members->execute(array(
    $_SESSION["id"]//id=?の?に$_SESSION["id"]を格納
  ));
  $member = $members->fetch();//現在取得できたデータを保存している
  //ログインしているユーザーの情報がDBから引き出された。
} else {
  header("Location: login.php");
  //ログインが完了していない場合、ログイン画面に強制的に移動
  exit();
}


//$_POSTのスーパーグローバル変数が空でなければ中の処理に移動。
if(!empty($_POST)){
  if($_POST["message"] !== ""){
    //textareaのname="message"が空でなければ、DBにユーザーが入力したメッセージを登録する。
    $message = $db->prepare("INSERT INTO posts SET member_id=?, message=?, reply_message_id =?, created=NOW()");
    //messageの追加なので、今回はINSERT INTO posts SET で開始。
    //***必ず「,」で区切ること。***（エラーで2時間無駄にした）
    //prepareでDBに接続し、IDやmessageは変数としてexecuteで値を格納する。
    $message->execute(array(
      $member["id"],         //$_SESSION["id"]も同じ値だが、DBから取ってきた方が確実のため、$member["id"]で対応
      $_POST["message"],     //ユーザーからPOSTされた$_POST["message"]をmessage=?の?に格納
      $_POST["reply_post_id"]//ユーザーから
    ));
    //ちなみに$_POST["message"]にデータを持ち続けてしまうため、
    //自分自身をもう一度呼び出し、POSTをリセットする。
    header("Location: index.php");//index.phpを呼び出し$_POSTのデータをリセットしているため、再読み込みをしてもmessageが重複しない。
    exit();
  }
}

//DBからreadする機能の追加
$posts = $db->query("SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC");
//ユーザーが入力した値を呼び出すわけではないため、prepareではなく、queryを用いての呼び出しでOK。
//今回はreadのため、SELECT文で呼び出しのみ実行。
//members.nameとmembers.pictureを呼び出す。members.idとposts.member_idとが合致するところから。リレーションして取得する。
//今回はmembersとpostsにそれぞれmとpのショートカット（エイリアス）をつけているため、mとpで呼び出しが可能。

//?res= の部分がクリックされたら、つまりリクエストされたら返信の処理を実行
if(isset($_REQUEST["res"])){
  //返信の処理
  $response = $db->prepare("SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?");
  $response->execute(array(
    $_REQUEST["res"]
  ));
  $table = $response->fetch();//ここの理解が浅い
  $message = "@" . $table["name"] . " " . $table["message"];
}
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
        <dt>
        <?php print(htmlspecialchars($member["name"],ENT_QUOTES));?>さん、メッセージをどうぞ
        </dt>
        <dd><textarea name="message" cols="50" rows="5"><?php print(htmlspecialchars($message , ENT_QUOTES)) ;?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST["res"],ENT_QUOTES))?>" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>


<!-- メッセージ表示開始箇所 -->
<?php foreach($posts as $post): ?>
  <div class="msg">
    <img 
      src="member_picture/<?php print(htmlspecialchars($post["picture"] , ENT_QUOTES));?>" 
      width="48" 
      height="48" 
      alt="<?php print(htmlspecialchars($post["name"],ENT_QUOTES)) ;?>" 
    />
    <p>
      <?php print(htmlspecialchars($post["message"] , ENT_QUOTES)) ;?><span class="name">（<?php print(htmlspecialchars($post["name"] , ENT_QUOTES))?>）</span>[<a href="index.php?res=<?php print(htmlspecialchars($post["id"] , ENT_QUOTES))?>">Re</a>]
    </p>
    <p class="day">
      <a href="view.php?id=<?php print(htmlspecialchars($post["id"],ENT_QUOTES)) ;?>">
        <?php print(htmlspecialchars($post["created"] , ENT_QUOTES));?>
      </a>
      <!-- 返信元のidが存在する場合のみ、リンクを表示 -->
      <?php if($post["reply_message_id"] > 0) :?>
        <a href="view.php?id=<?php print(htmlspecialchars($post["reply_message_id"],ENT_QUOTES))?>">
        返信元のメッセージ
        </a>
      <?php endif; ?>
      <!-- ログインしているユーザIDと投稿されているmessageのユーザIDが合致している時のみ、削除ボタンを表示する -->
      <?php if($_SESSION["id"]== $post["member_id"]):?>
      [<a href="delete.php?id=<?php print(htmlspecialchars($post["id"],ENT_QUOTES)) ;?>" style="color: #F33;">
        削除
      </a>]
      <?php endif; ?>
    </p>
  </div>
<?php endforeach ;?>
<!-- メッセージ表示終了箇所 -->

<ul class="paging">
<li><a href="index.php?page=">前のページへ</a></li>
<li><a href="index.php?page=">次のページへ</a></li>
</ul>
  </div>
</div>
</body>
</html>
