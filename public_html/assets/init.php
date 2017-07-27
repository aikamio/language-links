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

// ログイン処理
if(($_login = login()) | ( isset($_anonymous) ? $_anonymous : false )){
  return;
}
header ("Location:/newuser");
exit;