<?php

//スーパーグローバル変数（どこからでもアクセス可能）
//PHPの定義済み変数のポスト変数。
//HTTPの「POSTメソッドで送信された値を取得する」変数。
//HTML入力フォーム（formタグから）の値を受診して処理することが可能。
//POSTは連想配列として使用。$_POST[][]でアクセス可能。
//formタグのmethod属性にpostを指定することで、formのパラメータをサーバーに送信可能。
//formのinputタグやcheckboxタグ、selectタグなどで指定するname属性の値が連想配列のkey名となる。value属性が値そのものとなる。
//$_POSTの["name"]が空（name="name"のvalueが空）だった場合、エラーメッセージを表示する。
if($_POST["name"] ===""){
	$error["name"] = "blank";//error配列を準備し、["name"]内に追加
} else {

}
//エラーメッセージの表示は名前の下に実施したいが、
//このif文ごと名前入力欄の下に持っていくと後でsessionを使えなくなるから、
//条件分の結果だけ変数で格納し、名前入力欄に格納するのが吉。

;?>


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
        <input type="text" name="name" size="35" maxlength="255" value="" />
				<?php if($error["name"] === "blank") :?>
					<p class="error">ニックネームを入力してください。</p>
				<?php endif?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        <input type="text" name="email" size="35" maxlength="255" value="" />
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
      <input type="password" name="password" size="10" maxlength="20" value="" />
    </dd>
		<dt>写真など</dt>
		<dd>
      <input type="file" name="image" size="35" value="test"  />
    </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
