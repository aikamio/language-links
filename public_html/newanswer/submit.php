<?php
require "../assets/init.php";
sessionReset("newanswer");

/* データチェック
   $_GET : page
   $_POST : content */

checkToken();

checkCriticalParameter('page');
if(getContentFromData("q", $_GET['page']) === FALSE){
  noDataError();
}

if(!isInput($_POST['content'])){
  errorAnn("Please input text.");
  header("Location:.");
  exit;
}

/* 回答を登録 */
$st = $pdo->prepare(
  "INSERT INTO answer(content, userid, questionid)"
  . " VALUES(:content, :userid, :questionid);");
$st->bindValue(':content', $_POST['content']);
$st->bindValue(':userid', $_USER['id'], PDO::PARAM_INT);
$st->bindValue(':questionid', (int)$_GET['page'], PDO::PARAM_INT);
$st->execute();
var_dump($pdo->errorInfo());

$_SESSION['announce']['page'] = "answers";
sucAnn("Your answer is posted.");
header("Location: /answers?where=ps&order=c&page=".$_GET['page']);