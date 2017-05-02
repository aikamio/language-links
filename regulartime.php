<?php
//ルートディレクトリ以下にあれば実行しない
if(strpos(__FILE__, "/html/") !== FALSE){
  echo "Unauthorized access for ".__FILE__;
  exit;
}
echo "Searching files to delete. [".date("Y-m-d H:i:s")."]\n";

require 'html/assets/conf.php';
require 'html/assets/util/database.php';
require 'html/assets/util/common.php';

//期限の切れたurlのリストを取得
$date = date("Y-m-d H:i:s", strtotime($_conf['reissue_timelimit']));
$pdo->beginTransaction();
$st = $pdo->prepare("SELECT url FROM reissue WHERE valid = 1 AND date <= :date;");
$st->bindValue(':date', $date);
$st->execute();

//invalidする
$stInvalid = $pdo->prepare("UPDATE reissue SET valid = 0 WHERE valid = 1 AND date <= :date;");
$stInvalid->bindValue(':date', $date);
$stInvalid->execute();
$pdo->commit();

//フォルダの消去
while(($rec = $st->fetch()) !== FALSE){
  deleteDirectory("/var/www/html/reset/".$rec['url']); //絶対パスを指定(ファイルの実行場所に左右される)
  echo $rec['url']." is deleted.\n";
}
echo "All files done.\n";