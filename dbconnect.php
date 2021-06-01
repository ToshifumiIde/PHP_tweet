<?php

try{
  $db = new PDO(
    //Udemyの講座と異なり、host=localhostに修正
    "mysql:dbname=mini_bbs;host=localhost;charset=utf8;",
    "root",
    "root"
  );
} catch(PDOException $err){
  print("DB接続エラー。エラー内容は" . $err->getMessage() . "です。");
}
?>