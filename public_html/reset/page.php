<?php
$_anonymous = true;
require $_SERVER['DOCUMENT_ROOT']."/assets/init.php";
require $_SERVER['DOCUMENT_ROOT']."/assets/util/view.php";
$_login = false;  //フッターを表示したくないため、ログアウト状態ですって言う。

// /reset/の配下でないか、直下であれば不正アクセスである
$current = get_included_files()[0];
$rightdir = $_SERVER['DOCUMENT_ROOT']."/reset/";
$pos = strpos($current, $rightdir);
$len = strlen($rightdir);
$pos_after = strpos($current, "/", $len);
if($pos != 0 | $pos_after == -1){
  header("Location: /reset");
  exit;
}

//URLが有効かを調べる
$dir = substr($current, $len, $pos_after - $len);
if(checkReissueUrl($dir) === FALSE){
  header("Location: /reset");
  exit;
}

//セッションにURLを記録
$_SESSION['url'] = $dir;

?>
<form>
  <p><span class='q'>
    Maill address</span>
    <input type='email' name='mail' max='64' required>
  </p>
  <p>
    <span class='q'>New password</span>
    <input type='password' name='pass' max='30' required>
  </p>
  <p>
    <span class='q'>Confirm password</span>
    <input type='password' name='confirm' class="confirm" max='30' required>
  </p>
  <input type='submit' value='OK' id='submit'>
</form>
<?php

  getContent();
  $_head = "<script src='/assets/js/common.js' type='text/javascript'></script>".
    "<link rel='stylesheet' href='/reset/style.css' type='text/CSS'>";
  $_title = "Re-issue password";
  $_page = "resetpage";
  $_action = '/reset/submit.php';
  include $_SERVER['DOCUMENT_ROOT']."/assets/templete.php";