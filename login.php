<?php
require("./dbconnect.php");//DB接続実施
session_start();//ログイン情報の変数をsessionに保存しておきたいので、session_start()を実行

//そもそもデータがPOSTされているか確認
if(!empty($_POST)){
  //データPOSTされている場合、emailとpasswordが空でなかった場合に、
  //ユーザーから入力されたemailとpasswordに合致する、DB上のemailとpasswordの情報を引っ張ってくる。
  if($_POST["email"] !== "" && $_POST["password"] !== ""){
    $login = $db->prepare("SELECT * FROM members WHERE email=? AND password=?");
    //membersからemailとpasswordをprepare文で選択する。
    //emailとpasswordの中身は、executeでユーザーが入力したemailとpasswordを格納する。
    $login->execute(array(
      $_POST["email"],
      sha1($_POST["password"])
    ));
    //sha1を用いてpasswordを暗号化したため、sha1を用いてこちらも暗号化する。
    //sha1を用いた暗号化は、同じ文字列であれば同じ暗号結果を返す。
    $member = $login->fetch();//ユーザーが入力したemailとpasswordのデータをDBから引っ張ってくる。
    //メンバーが存在していたら
    if($member){
      header('Location: index.php');
      $_SESSION["id"] = $member("id");
      $_SESSION["time"] = time();
      //header()関数は、実行前に別の処理を入れない。入れるとエラー。半角空白などもNG。
      //sessionにはpasswordは保存しない。cookieよりは安全性があるが、sessionハイジャックといったことも起こりうる。
      // var_dump($member);
      exit();
    } else {
      //ログインに失敗した場合
      $error["login"] = "failed";
    }
  } else {
    //$_POST["email"]か$_POST["password"]が空だった場合
    $error["login"] = "blank";
  }
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css" />
<title>ログインする</title>
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ログインする</h1>
  </div>
  <div id="content">
    <div id="lead">
      <p>メールアドレスとパスワードを記入してログインしてください。</p>
      <p>入会手続きがまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="join/">入会手続きをする</a></p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'] , ENT_QUOTES); ?>" />
          <?php if($error["login"] === "blank"):?>
          <p class="error">*メールアドレスとパスワードの両方を入力してください</p>
          <?php endif ;?>
          <?php if($error["login"] === "failed"):?>
          <p class="error">*ログインに失敗しました。メールアドレスとパスワードをご確認ください。</p>
          <?php endif ;?>
        </dd>
        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['password'] , ENT_QUOTES); ?>" />
        </dd>
        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">次回からは自動的にログインする</label>
        </dd>
      </dl>
      <div>
        <input type="submit" value="ログインする" />
      </div>
    </form>
  </div>
  <div id="foot">
    <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
  </div>
</div>
</body>
</html>
