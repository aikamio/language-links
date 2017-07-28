<?php
$_anonymous = true;
require "../assets/init.php";
require "../assets/util/view.php";

/* 本文 */
?>
<form>
  <p><span class='q'>Name or maill address</span><input type='text' name='name' max='30' required></p>
  <p><span class='q'>Password</span><input type='password' name='pass' max='30' required data-regex='^[0-9a-zA-Z]+$'></p>
  <input type='submit' value='Login' id='submit'>
  <div class='formfoot'>
    <a href='/forgot'>Forgot Password</a>
  </div>
</form>
<?php

  getContent();
  $_head = "<script src='/assets/js/common.js' type='text/javascript'></script>";
  $_title = "Login";
  $_page = "login";
  include "../assets/templete.php";