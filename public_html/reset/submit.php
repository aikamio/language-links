<?php
$_anonymous = true;
require "../assets/init.php";

/* セッションからURLを取得 */
sessionReset("resetpage");
$url = $_SESSION['url'];
$_SESSION['url'] = null;

/* URLの期限をチェック */
if(($rec = checkReissueUrl($url)) === FALSE){
  header("Location: /reset");
  exit;
}
$userid = (int)$rec['userid'];

/* データチェック
   $_POST : mail, pass, confirm */
header("Location:$url");

checkToken();

if(!isInput($_POST['mail'])){
  errorAnn("Please input mail address.");
  exit;
}

if(!isInput($_POST['pass'])){
  errorAnn("Please input password.");
  exit;
}else if($_POST['pass'] !== $_POST['confirm']){
  errorAnn("Confirm password id incorrect.");
  exit;
}

/* ユーザー情報の確認 */
$pdo->beginTransaction();
$st = $pdo->prepare("SELECT id FROM user WHERE id = :id AND mail = :mail LIMIT 1");
$st->bindValue(':id', $userid, PDO::PARAM_INT);
$st->bindValue(':mail', $_POST['mail']);
$st->execute();
$rec = $st->fetch();
if($rec === FALSE){
  errorAnn("There is no user information, or mail address is incollect.");
  exit;
}
$st = $pdo->prepare("UPDATE user SET userpass = :userpass WHERE id = :id");
$st->bindValue(':userpass', getHash($_POST['pass']));
$st->bindValue(':id', $userid, PDO::PARAM_INT);
$st->execute();
$stDelete = $pdo->prepare("UPDATE reissue SET valid = 0 WHERE url = :url");
$stDelete->bindValue(':url', $url);
$stDelete->execute();
$pdo->commit();

//フォルダの消去
require "../assets/util/common.php";
deleteDirectory($url);

$_SESSION['announce']['page'] = "done";
sucAnn("Password changed.");
header("Location: done.html");