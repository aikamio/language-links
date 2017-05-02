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
$pdo->beginTransaction();
$st = $pdo->prepare(
  "INSERT INTO user(name, learning, mothertongue)"
  . " VALUES(:name, :learning, :mothertongue);");
$st->bindValue(':name', $_POST['name']);
$st->bindValue(':learning', $learn);
$st->bindValue(':mothertongue', (int)$_POST['mother'], PDO::PARAM_INT);
$st->execute();
$stGet = $pdo->query("SELECT MAX(id) AS max FROM user;");
$pdo->commit();

/* cookiepassの発行 */
setCookiePass($stGet->fetch()['max']);

/* ホーム画面に遷移 */
header("Location: /");
