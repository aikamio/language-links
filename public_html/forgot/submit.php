<?php
$_anonymous = true;
require "../assets/init.php";
sessionReset("forgot");

/* データチェック
   $_POST : mail */

checkToken();

if(!isInput($_POST['mail'])){
  errorAnn("Please input mailaddress.");
  header("Location:.");
  exit;
}

/* ユーザー情報を探す ただしパスワードが設定されているもののみ */
$st = $pdo->prepare("SELECT id FROM user WHERE mail = :mail AND NOT userpass IS null LIMIT 1");
$st->execute(array(':mail'=>$_POST['mail']));
$rec = $st->fetch();
if($rec === FALSE){
  errorAnn("The mailaddress not found.");
  header("Location:.");
  exit;
}

/* パスワード再設定ページを作成 */
$url = makeRandStr(30);
$st = $pdo->prepare("INSERT INTO reissue(userid, url, valid, date) VALUES(:userid, :url, :valid, :date)");
$st->bindValue(':userid', $rec['id']);;
$st->bindValue(':url', $url);
$st->bindValue(':valid', 1, PDO::PARAM_INT);
$st->bindValue(':date', date("Y-m-d H:i:s"));
$st->execute();
mkdir("../reset/$url");
$content = "<?php include '../page.php';";
$result = file_put_contents("../reset/$url/index.php", $content);
chmod("../reset/$url", 0775);
chmod("../reset/$url/index.php", 0666);

/* メールの送信 */
$subject = "Re-issue password for Language-Links";
$text =
  "Please tap the URL below.\n".
  "(URL is valid for 1 hour only.)\n\n".
  "http://192.168.142.129/reset/$url/";
$header = "From: noreply@example.com";
mail($_POST['mail'],$subject,$text,$header);
sucAnn("Re-issue mail was posted.");
header("Location:.");
