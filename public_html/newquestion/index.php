<?php
  require "../assets/init.php";
  require "../assets/util/view.php";

?>
<h2>Make Q&amp;A</h2>
<form>
  <textarea name="content" required></textarea>
  <p class="q">Language</p>
  <p>
    <?php
      $list = languages();
      array_push($list, array('id'=>'-1', 'name'=>'Any', 'on'=>true));
      radios($list, "language", ":id", true, ":name");
    ?>
  </p>
  <input type='submit' value='Make' class='formbutton'>
</form>
<?php

getContent();
$_head = "<script src='/assets/js/common.js' type='text/javascript'></script>";
$_title = "Make question";
$_page = "newquestion";
include "../assets/templete.php";