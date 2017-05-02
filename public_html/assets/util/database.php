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
  $st = $pdo->prepare("SELECT id FROM cookiepass WHERE userid = :userid AND valid = 1 ORDER BY date");
  $st->bindValue(':userid', $userid, PDO::PARAM_INT);
  $st->execute();
  $rec = $st->fetchall();
  /* 設定数以上なら最も古いものをinvalidする。 */
  if(count($rec) >= $_conf['cookie_max']){
    $stInvalid = $pdo->prepare("UPDATE cookiepass SET valid = 0 WHERE id =:oldid");
    $stInvalid->bindValue(':oldid', $rec[0]['id'], PDO::PARAM_INT);
    $stInvalid->execute();
  }
  /* 新しいものを登録 */
  $st = $pdo->prepare("INSERT INTO cookiepass (userid, termid, pass, valid, date) VALUES(:userid, :termid, :pass, :valid, :date);");
  $st->bindValue(':userid', $userid);
  $st->bindValue(':termid', $termid);
  $st->bindValue(':pass', $hash);
  $st->bindValue(':valid', 1, PDO::PARAM_INT);
  $st->bindValue(':date', date("Y-m-d H:i:s"));
  $st->execute();
  $pdo->commit();
}

/* Check user password (Return false if userpass isnt exist) */
function userpassIsCollect($_id, $_pass){
  global $pdo;
  $st = $pdo->prepare("SELECT userpass FROM user WHERE id = ':id';");
  $st->execute(array(':id'=>$_id));
  $hash = $st->fetch()['userpass'];
  return $hash == null | password_verify($_pass, $hash);
}

/* Check Re-issue Url */
function checkReissueUrl($_dir){
  global $pdo;
  global $_conf;
  $st = $pdo->prepare("SELECT userid FROM reissue ".
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
  $st = $pdo->query("SELECT * FROM language");
    $_languages = $st->fetchall();
  }
  return $_languages;
}

/* Get question data from answer id */
function getQuestion($_id){
  global $pdo;
  $st = $pdo->prepare(
    "SELECT question.content as q"
    ." FROM answer"
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
  $table = ($_type == "c") ? "comment" :( ($_type == "a") ? "answer" : "question");
  $st = $pdo->prepare("SELECT content FROM $table WHERE id = :id");
  $st->bindValue(':id', $_id, PDO::PARAM_INT);
  $st->execute();
  if(($rec = $st->fetch()) === FALSE){
    return FALSE;
  }
  return $rec['content'];
}

/* If it's unauthorized access, head to home */
function checkToken(){
  global $pdo;
  global $_USER;
  global $_conf;
  if(isset($_POST['token']) ? isInput($_POST['token']) : false){
    $pdo->beginTransaction();
    $st = $pdo->prepare(
      "SELECT id FROM token"
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
      $stInvalid = $pdo->prepare("UPDATE token SET valid = :valid WHERE id =:id;");
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