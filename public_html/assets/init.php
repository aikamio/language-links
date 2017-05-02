<?php
//直接アクセス禁止
if(get_included_files()[0] === __FILE__) {
  header("Location: /");
  exit;
}

/* 引数の説明
   $_anonymous = TRUE なら遷移せずreturnする。
   $_login にはログイン状態か否かを格納。フッターの表示などに使う。 */

//基本ライブラリのインポート
require $_SERVER['DOCUMENT_ROOT'].'/assets/conf.php';
require $_SERVER['DOCUMENT_ROOT'].'/assets/util/util.php';
require $_SERVER['DOCUMENT_ROOT'].'/assets/util/database.php';

session_start();

/* セッション変数のまとめ

  $_SESSION['announce'] .. テンプレートのアナウンスボックスで表示するエラーテキストなど
    ['page'] .. 表示すべきページ（違うページに移動したら消す）
    ['type'] .. アナウンスの種類(文字色などに反映)
      error : エラーテキスト
      success : 正常な実行結果テキスト
    ['text'] .. テキスト

  $_SESSION['url'] .. submit.phpに呼び出し元を渡すために使用(/resetで使用) */

// cookieを調べてログイン処理
if(isset($_COOKIE['termid']) & isset($_COOKIE['password'])){
  $pdo->beginTransaction();
  $st = $pdo->prepare("SELECT id,userid,pass FROM cookiepass WHERE termid = :termid");
  $st->execute(array(':termid'=>$_COOKIE['termid']));
  while(true){
    $rec = $st->fetch();
    if($rec === false){
      break;
    }
    if(password_verify($_COOKIE['password'],$rec['pass'])){
      // ログイン処理
      // ユーザー情報を$_USERに入れる
      $st = $pdo->prepare("SELECT * FROM user WHERE id = :id");
      $st->execute(array(':id'=>$rec['userid']));
      $_USER = $st->fetch();
      $_login = true;
      //日付を更新
      $stUpdate = $pdo->prepare("UPDATE cookiepass SET date = :date WHERE id = :id");
      $stUpdate->bindValue(':date', date("Y-m-d H:i:s"));
      $stUpdate->bindValue(':id', $rec['id'], PDO::PARAM_INT);
      $stUpdate->execute();
      $pdo->commit();
      return;
    }
  }
  $pdo->commit();
}

// 非ログイン処理
$_login = false;
if($_anonymous){
  return;
}
header ("Location:/newuser");
exit;