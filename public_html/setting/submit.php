<?php
require "../assets/init.php";
sessionReset("setting");

/* データチェック
   $_POST : name, learn0-, mother, mail, pass, confirm */

checkToken();

if(isInput($_POST['name'])){
  $_USER['name'] = $_POST['name'];
}

$learn = "";
foreach(languages() as $lang){
  $id = $lang['id'];
  $key = 'learn'.$id;
  if(isset($_POST[$key])){
    if($_POST[$key] == "on"){
      if($learn != ""){
        $learn .= ",";
      }
      $learn .= $id;
    }
  }
}
if($learn != ""){
  $_USER['learning'] = $learn;
}

if(!preg_match("/[^-0-9]+/", $_POST['mother']) && $_POST['mother']!=""){
  $_USER['mothertongue'] = (int)$_POST['mother'];
}

if(isInput($_POST['mail'])){
  $_USER['mail'] = $_POST['mail'];
}

if(isInput($_POST['password']) && $_POST['password'] === $_POST['confirm']){
  $_USER['userpass'] = getHash($_POST['password']);
}

/* ユーザー情報を更新（login_checkしているため再確認は不要） */
$st = $pdo->prepare(
  "UPDATE user SET name = :name, learning = :learning, mothertongue = :mothertongue, mail = :mail, userpass = :userpass WHERE id = :id");
$st->bindValue(':name', $_USER['name']);
$st->bindValue(':learning', $_USER['learning']);
$st->bindValue(':mothertongue', $_USER['mothertongue'], PDO::PARAM_INT);
$st->bindValue(':mail', $_USER['mail']);
$st->bindValue(':userpass', $_USER['userpass']);
$st->bindValue(':id', $_USER['id']);
$st->execute();

sucAnn("Update is completed.");
header("Location:.");