<?php
$_anonymous = true;
require "../assets/init.php";
require "../assets/util/view.php";

/* 本文 */
?>
<form>
  <p><span class='q'>Name</span><input type='text' name='name' required></p>
  <p class='q'>Which language do you want to learn?</p><p>
  <?php
    $list = languages();
    checkboxes($list, "learn:id", "What you want to learn", ":name");
  ?>
  </p><p class='q'>What is your mother tongue?</p><p>
  <?php
    array_push($list, array('id'=>'-1', 'name'=>'Any', 'on'=>true));
    radios($list, "mother", ":id", true, ":name");
  ?>
  </p>
  <input type='submit' value='Start' id='submit'>
  <div class='formfoot'>
    <a href='/login'>Login</a>
  </div>
</form>
<?php
  $_content = ob_get_contents();
  ob_end_clean();

/* その他情報 */
  $_head = '<script src="/assets/js/common.js" type="text/javascript"></script>';
  $_title = 'Welcome';
  $_page = 'newuser';
  include '../assets/templete.php';