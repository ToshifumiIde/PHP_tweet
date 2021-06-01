<?php
session_start();

//ログアウト = $_SESSIONの情報を空配列にする
$_SESSION = array();
//続いてcookieの情報を削除していく
if(ini_set("session.use_cookies")){
  $params = session_get_cookie_params();
  setcookie(
    session_name() . "" ,
    time() - 42000 ,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}
session_destroy();
setcookie("email" , "" , time() -3600);

//全ての情報が削除し終えたらlogin画面に遷移させる
header("Location: login.php");
exit();//以降、処理を実行しない。

?>