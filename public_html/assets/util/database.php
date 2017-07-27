<?php
//直接アクセス禁止
if(get_included_files()[0] === __FILE__) {
  header("Location: /");
  exit;
}
/* $pdo ： PDO object */
$pdo = new PDO("mysql:host=localhost;dbname=practice;charset=utf8;", "develop", "9876tttt");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

/*---- Methods around database ----*/

/* Register new user */
function makeNewUser($name, $learn, $mother){
  global $pdo;
  $pdo->beginTransaction();
  $st = $pdo->prepare(
    "INSERT INTO user(name, learning, mothertongue)"
    . " VALUES(:name, :learning, :mothertongue);");
  $st->bindValue(':name', $name);
  $st->bindValue(':learning', $learn);
  $st->bindValue(':mothertongue', $mother, PDO::PARAM_INT);
  $st->execute();
  $stGet = $pdo->query("SELECT MAX(id) AS max FROM tr_user;");
  $pdo->commit();
  /* cookiepassの発行 */
  setCookiePass($stGet->fetch()['max']);

}

/* Set new cookiepass to this terminal */
function setCookiePass($userid){
  require_once('../assets/util/util.php');
  global $pdo;
  global $_conf;
  /* termidの発行 */
  $term = year();
  $termid = makeRandStr(8);
  setcookie('termid',$termid,$term,'/');
  /* cookiepassの発行 */
  $pass = makeRandStr(30);
  setcookie('password',$pass,$term,'/');
  $hash = getHash($pass);
  /* トランザクション開始 */
  $pdo->beginTransaction();
  /* 現在設定されているcookiepassのidを取得 */
  $st = $pdo->prepare("SELECT id FROM tr_cookiepass WHERE userid = :userid AND valid = 1 ORDER BY date");
  $st->bindValue(':userid', $userid, PDO::PARAM_INT);
  $st->execute();
  $rec = $st->fetchall();
  /* 設定数以上なら最も古いものをinvalidする。 */
  if(count($rec) >= $_conf['cookie_max']){
    $stInvalid = $pdo->prepare("UPDATE tr_cookiepass SET valid = 0 WHERE id =:oldid");
    $stInvalid->bindValue(':oldid', $rec[0]['id'], PDO::PARAM_INT);
    $stInvalid->execute();
  }
  /* 新しいものを登録 */
  $st = $pdo->prepare("INSERT INTO tr_cookiepass (userid, termid, pass, valid, date) VALUES(:userid, :termid, :pass, :valid, :date);");
  $st->bindValue(':userid', $userid);
  $st->bindValue(':termid', $termid);
  $st->bindValue(':pass', $hash);
  $st->bindValue(':valid', 1, PDO::PARAM_INT);
  $st->bindValue(':date', date("Y-m-d H:i:s"));
  $st->execute();
  $pdo->commit();
}

function login(){
  global $pdo;
  global $_USER;
  $pdo->beginTransaction();
  $st = $pdo->prepare("SELECT id,userid,pass FROM tr_cookiepass WHERE termid = :termid");
  $st->execute(array(':termid'=>$_COOKIE['termid']));
  while(true){
    $rec = $st->fetch();
    if($rec === false){
      break;
    }
    if(password_verify($_COOKIE['password'],$rec['pass'])){
      // ログイン処理
      // ユーザー情報を$_USERに入れる
      $st = $pdo->prepare("SELECT * FROM tr_user WHERE id = :id");
      $st->execute(array(':id'=>$rec['userid']));
      $_USER = $st->fetch();
      //日付を更新
      $stUpdate = $pdo->prepare("UPDATE tr_cookiepass SET date = :date WHERE id = :id");
      $stUpdate->bindValue(':date', date("Y-m-d H:i:s"));
      $stUpdate->bindValue(':id', $rec['id'], PDO::PARAM_INT);
      $stUpdate->execute();
      $pdo->commit();
      return true;
    }
  }
  $pdo->commit();
  return false;
}

/* Check user password (Return false if userpass isnt exist) */
function userpassIsCollect($_id, $_pass){
  global $pdo;
  $st = $pdo->prepare("SELECT tr_userpass FROM user WHERE id = ':id';");
  $st->execute(array(':id'=>$_id));
  $hash = $st->fetch()['userpass'];
  return $hash == null | password_verify($_pass, $hash);
}

/* Check Re-issue Url */
function checkReissueUrl($_dir){
  global $pdo;
  global $_conf;
  $st = $pdo->prepare("SELECT tr_userid FROM reissue ".
    "WHERE url = :url AND valid = 1 AND date >= :date LIMIT 1");
  $st->bindValue(':url', $_dir);
  $st->bindValue(':date', date("Y-m-d H:i:s", strtotime($_conf['reissue_timelimit'])));
  $st->execute();
  $rec = $st->fetch();
  return $rec;
}

/* Get language list */
$_languages = null;  /* 下記関数以外では使用しない */
function languages(){
  global $pdo;
  global $_languages;
  if($_languages == null){
  $st = $pdo->query("SELECT * FROM mt_language");
    $_languages = $st->fetchall();
  }
  return $_languages;
}

/* Get question data from answer id */
function getQuestion($_id){
  global $pdo;
  $st = $pdo->prepare(
    "SELECT question.content as q"
    ." FROM tr_answer"
    ." JOIN question ON answer.questionid = question.id"
    ." WHERE answer.id = :id LIMIT 1");
  $st->bindValue(':id', $_id, PDO::PARAM_INT);
  $st->execute();
  $rec = $st->fetch();
  if($rec === FALSE){
    return FALSE;
  }
  return $rec['q'];
}

/* Get content */
function getContentFromData($_type, $_id){
  global $pdo;
  $table = ($_type == "c") ? "tr_comment" :( ($_type == "a") ? "tr_answer" : "tr_question");
  $st = $pdo->prepare("SELECT content FROM $table WHERE id = :id");
  $st->bindValue(':id', $_id, PDO::PARAM_INT);
  $st->execute();
  if(($rec = $st->fetch()) === FALSE){
    return FALSE;
  }
  return $rec['content'];
}

/* Set form token to db */
function setToken($user, $token){
  global $pdo;
  $st = $pdo->prepare(
    "INSERT INTO tr_token(token, userid, valid, date)"
    . " VALUES(:token, :userid, :valid, :date);");
  $st->bindValue(':token', $token);
  $st->bindValue(':userid', $user, PDO::PARAM_INT);
  $st->bindValue(':valid', 1, PDO::PARAM_INT);
  $st->bindValue(':date', date("Y-m-d H:i:s"));
  $st->execute();
}

/* If it's unauthorized access, head to home */
function checkToken(){
  global $pdo;
  global $_USER;
  global $_conf;
  if(isset($_POST['token']) ? isInput($_POST['token']) : false){
    $pdo->beginTransaction();
    $st = $pdo->prepare(
      "SELECT id FROM tr_token"
      ." WHERE token = :token"
      ." AND userid = :userid"
      ." AND valid = 1"
      ." AND date >= :date");
    $st->bindValue(':token', $_POST['token'], PDO::PARAM_INT);
    $st->bindValue(
      ':userid',
      isset($_USER) ? $_USER['id'] : -1,
      PDO::PARAM_INT);
    $st->bindValue(':date', date("Y-m-d H:i:s", strtotime($_conf['token_timelimit'])));
    $st->execute();
    $rec = $st->fetch();
    if($rec !== FALSE){
      //無効にする
      $stInvalid = $pdo->prepare("UPDATE tr_token SET valid = :valid WHERE id =:id;");
      $stInvalid->bindValue(':valid', 0, PDO::PARAM_INT);
      $stInvalid->bindValue(':id', $rec['id'], PDO::PARAM_INT);
      $stInvalid->execute();
      $pdo->commit();
      //通過
      return;
    }
    //無効
    $pdo->rollBack();
  }
  $_SESSION['announce']['page'] = "home";
  errorAnn("Unauthorized access.");
  header("Location: ..");
  exit;
}