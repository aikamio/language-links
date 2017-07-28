<?php
$_anonymous = true;
require "../assets/init.php";
require "../assets/util/view.php";

/* 本文 */
?>
<form>
  <p><span class='q'>Maill address</span><input type='email' name='mail' max='64' required></p>
  <input type='submit' value='Send mail' id='submit' required data-regex='^(:mail)$'>
  <div class='formfoot'>
    <a href='/login'>Back to login</a>
  </div>
</form>
<?php

  getContent();
  $_head = "<script src='/assets/js/common.js' type='text/javascript'></script>";
  $_title = "Send re-issue mail";
  $_page = "forgot";
  include "../assets/templete.php";