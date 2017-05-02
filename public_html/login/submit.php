<?php
$_anonymous = true;
require "../assets/init.php";
sessionReset("login");

/* データチェック
   $_POST : name, pass */

checkToken();

if(!isInput($_POST['name'])){
  errorAnn("Please input name.");
  header("Location:.");
  exit;
}

if(!isInput($_POST['pass'])){
  errorAnn("Please input password.");
  header("Location:.");
  exit;
}

/* ユーザー情報の確認 */

$st = $pdo->prepare("SELECT id FROM user WHERE name = :name OR mail = :name;");
$st->execute(array(':name'=>$_POST['name']));
while(true){
  $rec = $st->fetch();
  if($rec === FALSE){
    break;
  }
  if(userpassIsCollect($rec['id'], $_POST['pass'])){
    /* ログイン処理 */
    setCookiePass($rec['id']);
    header("Location:/");
    exit;
  }
}

errorAnn("There is no user information, or password is incollect.");
header("Location:.");
