<?php
require "../assets/init.php";
sessionReset("newquestion");

/* データチェック
   $_POST : content, language */

checkToken();

if(!isInput($_POST['content'])){
  errorAnn("Please input question.");
  header("Location:.");
  exit;
}

if(preg_match("/[^-0-9]+/", $_POST['language']) | $_POST['language']==""){
  errorAnn("Please choose language to answer.");
  header("Location:.");
  exit;
}

/* 質問を登録 */
$st = $pdo->prepare(
  "INSERT INTO question(content, userid, language)"
  . " VALUES(:content, :userid, :language);");
$st->bindValue(':content', $_POST['content']);
$st->bindValue(':userid', $_USER['id'], PDO::PARAM_INT);
$st->bindValue(':language', (int)$_POST['language'], PDO::PARAM_INT);
$st->execute();
var_dump($pdo->errorInfo());

$_SESSION['announce']['page'] = "questions";
sucAnn("Your question is posted.");
header("Location: /questions");