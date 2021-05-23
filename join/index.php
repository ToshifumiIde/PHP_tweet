<?php
session_start();//sessionをstart
//sessionに値を保存するのはheader()関数の直前
require("../dbconnect.php");


//h関数の作成
function h($str){
	return htmlspecialchars($str , ENT_QUOTES);
}

//全て空だった場合、初期値のためエラーメッセージの表示は不要。
//ユーザーが何かしらformに入力しているものの、
//全ての入力が完了していない場合、ファイルの種類が異なる場合の条件分岐
if(!empty($_POST)){
	//$_POSTはスーパーグローバル変数（どこからでもアクセス可能）
	//PHPの定義済み変数のポスト変数。
	//HTTPの「POSTメソッドで送信された値を取得する」変数。
	//HTML入力フォーム（formタグから）の値を受診して処理することが可能。
	//POSTは連想配列として使用。$_POST[][]でアクセス可能。
	//formタグのmethod属性にpostを指定することで、formのパラメータをサーバーに送信可能。
	//formのinputタグやcheckboxタグ、selectタグなどで指定するname属性の値が連想配列のkey名となる。value属性が値そのものとなる。
	//$_POSTの["name"]が空（name="name"のvalueが空）だった場合、エラーメッセージを表示する。
	//$_POST["name"]には、ユーザーが入力したname="name"のvalueが格納される。
	
	if($_POST["name"] ===""){
		$error["name"] = "blank";//error配列を準備し、["name"]内に追加
	}
	if($_POST["email"] ===""){
		$error["email"] = "blank";//error配列を準備し、["name"]内に追加
	}
	if($_POST["password"] ===""){
		$error["password"] = "blank";//error配列を準備し、["name"]内に追加
	}
	if(strlen($_POST["password"]) < 4){
		$error["password"] = "length";
	}
	//エラーメッセージの表示は名前の下に実施したいが、
	//このif文ごとhtmlの名前入力欄の下に持っていくと、後でSESSIONを使えなくなるから、
	//条件文の結果だけ変数で格納し、名前入力欄に格納するのが吉。

	//fileの拡張子がzipなどの場合、ファイルの保存ができない様に設定する。
	$fileName = $_FILES["image"]["name"];
	if(!empty($fileName)){
		$ext = substr($fileName , -3);//fileの拡張子を切り出すため、substr()関数で後ろ3文字を抽出
		if($ext != "jpg" && $ext != "gif" && $ext !="png"){
			$error["image"] = "type";
		}
	}
	
	//アカウントの重複をチェック
	if(empty($error)){
		$member = $db->prepare("SELECT COUNT(*) AS cnt FROM members WHERE email=?");
		$member->execute(array($_POST["email"]));
		$record = $member->fetch();
		if($record["cnt"] > 0){
			$error["email"] = "duplicate";
		}
	}


	if(empty($error)){
		$_SESSION["join"] = $_POST;//POSTで受け取った配列を丸ごとSESSION["join"]に渡す。
		$image = date("YmdHis") . $_FILES["image"]["name"];//uploadするイメージファイルの名前を作成。
		//$_FILESのグローバル変数
		//20210522110612myface.pngなど。同じ時間に同じファイル名が来ると消されるが、今回は練習のためこの程度でOK。
		move_uploaded_file($_FILES["image"]["tmp_name"] , "../member_picture/" . $image);
		//$_FILESというグローバル変数は、input type="file"のファイルフィールドから得られた内容
		//["tmp_name"]は「一時的に」UPLOADされている場所。この状態のままだと後でデータが消える可能性があるため、
		//move_uploaded_file()関数を用いて、保存したい場所に保存し直す。
		//第一引数：現在のファイルがある場所、第二引数：実際に保存したい場所（ファイル名を含めたパスの指定が必要）
		$_SESSION["join"]["image"] = $image;
		//後にSESSIONとして利用するため、$_SESSION["join"]に["image"]という格納先を作り、そこに$imageを保存する。
		header("Location: check.php");//check.phpに移動
		exit();
	}
}

//check.phpからindex.phpに遷移するタイミングでhref="index.php?action=rewrite"とactionにrewriteを設定している。
//$_SESSION["join"]でSESSIONが正しくチェックされているか確認する。
if($_REQUEST["action"] == "rewrite" && isset($_SESSION["join"])){
	$_POST = $_SESSION["join"];//SESSIONに格納されている値を$_POSTに代入する。
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>次のフォームに必要事項をご記入ください。</p>
<!-- 自分自身のファイルに転送。POSTにつき、POSTで受け取れるかどうか -->
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        <input 
					type="text" 
					name="name" 
					size="35" 
					maxlength="255" 
					value="<?php print(h($_POST["name"])) ;?>" 
				/>
				<?php if($error["name"] === "blank") :?>
					<p class="error">*ニックネームを入力してください。</p>
				<?php endif?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        <input type="text" name="email" size="35" maxlength="255" value="<?php print(h($_POST["email"]))?>" />
				<?php if($error["email"] === "blank"):?>
					<p class="error">*メールアドレスを入力してください</p>
				<?php endif;?>
				<?php if($error["email"] === "duplicate"):?>
					<p class="error">*指定されたメールアドレスは、既に登録されています。</p>
				<?php endif;?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
      <input type="password" name="password" size="10" maxlength="20" value="<?php print(h($_POST["password"]));?>" />
			<?php if($error["password"] ==="length"):?>
				<p class="error">*パスワードは4文字以上で入力してください。</p>
			<?php elseif($error["password"] === "blank"):?>
				<p class="error">パスワードを入力してください。</p>
			<?php endif;?>
    </dd>
		<dt>写真など</dt>
		<dd>
      <input type="file" name="image" size="35" value="test"  />
			<?php if($error["image"] === "type"):?>
				<p class="error">*写真などは「.jpg」「.gif」「.png」の画像を指定してください</p>
			<?php endif ;?>
			<?php if(!empty($error)):?>
			<p class="error">*恐れ入りますが、改めて画像を指定してください</p>
			<?php endif ;?>
    </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
