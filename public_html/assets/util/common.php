<?php
//直接アクセス禁止
if(get_included_files()[0] === __FILE__) {
  header("Location: /");
  exit;
}

/* This file is for often access from local */

/* Author : asn at asn24 dot dk
   Pull from : http://php.net/manual/ja/function.rmdir.php */
//Delete folder function
function deleteDirectory($dir) {
if (!file_exists($dir)) return true;
if (!is_dir($dir) || is_link($dir)) return unlink($dir);
  foreach (scandir($dir) as $item) {
    if ($item == '.' || $item == '..') continue;
    if (!deleteDirectory($dir . "/" . $item)) {
      chmod($dir . "/" . $item, 0777);
      if (!deleteDirectory($dir . "/" . $item)) return false;
    };
  }
  return rmdir($dir);
}