<?php 
session_start();//sessionの開始
require("dbconnect.php");//db接続

//まずはログインしているか念のため確認
if(isset($_SESSION["id"])){
  //削除候補のmessageをDBから取得してくる。
  //そのために、まずは現在ログインしているidを取得する
  $id = $_REQUEST["id"];//ここが何故$_REQUEST["id"]なのかが不明
  //削除候補のmessageをDBから選択する。
  $messages = $db->prepare("SELECT * FROM posts WHERE id=?");
  // 実際にdbから引っ張ってくるのは現在ログインしているidと一致するもの。
  $messages->execute(array(
    $id
  ));
  $message = $messages->fetch();

  if($message["member_id"] == $_SESSION["id"]){
    $del = $db->prepare("DELETE FROM posts WHERE id=?");
    $del->execute(array(
      $id
    ));
  }
}

header("Location: index.php");
exit();
?>