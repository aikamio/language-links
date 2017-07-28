<?php
require "../assets/init.php";
require "../assets/util/view.php";

/* データチェック */
checkCriticalParameter('page');
$q = getContentFromData("q", $_GET['page']);
if($q === FALSE){
  noDataError();
}

?>
<h1><?php echo html($q); ?></h1>
<form>
  <textarea name="content" required></textarea>
  <input type='submit' value='OK.' class='formbutton'>
</form>
<?php

getContent();
$_action = "submit.php?page=".$_GET['page'];
$_head = "<script src='/assets/js/common.js' type='text/javascript'></script>";
$_title = "Make answer";
$_page = "newanswer";
include "../assets/templete.php";