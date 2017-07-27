<?php
$_anonymous = true;
require "../assets/init.php";
sessionReset("newuser");

/* データチェック
   $_POST : name, learn(0～), mother */

checkToken();

if(!isInput($_POST['name'])){
  errorAnn("Name is required.");
  header("Location: ");
  exit;
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
if($learn == ""){
  errorAnn("What do you want to learn is required.");
  header("Location: ");
  exit;
}

if(preg_match("/[^-0-9]+/", $_POST['mother']) | $_POST['mother']==""){
  errorAnn("Mother tongue is required.");
  header("Location: ");
  exit;
}

/* データベースに登録 */
makeNewUser($_POST['name'], $learn, (int)$_POST['mother']);

/* ホーム画面に遷移 */
header("Location: /");
