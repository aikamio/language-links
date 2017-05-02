<?php
require "../assets/init.php";
require "../assets/util/view.php";
$_title = "My page";
$_page = "user";

?>
<h2>Learning Score</h2>
<div>
  <?php
    $list = languages();
    foreach(explode(",", $_USER['learning']) as $langid){
      echo "<p>".$list[$langid]['name'];
      echo " <span class='langlv'>LV<span class='langnum'>5</span></span></p>";
    }
  ?>
</div>
<h2>Your latest question</h2>
<div>
  <?php
    setGet("where=m&order=c&list=1&data=u");
    $_this = "answer";
    require "../assets/util/list.php";
    if(count($_result) >= 1){
      echo "<a href='/answers/?where=s&data=cn&page=".$_result[0]['id']."'><p>"
        . $_result[0]['content']
        . "<span class='new'>New:" . $_result[0]['unread'] . "</span>"
        . "</p></a>";
      if(count($_result) >= 2){
        echo "<p class='more'><a href='/questions/?where=m&order=cu&list=100'>And more</a></p>";
      }
    }
  ?>
</div>
<h2>New arrival corrections</h2>
<div>
  <?php
    setGet("where=m,cu&order=c&data=c");
    $_this = "answer";
    require "../assets/util/list.php";
    foreach($_result as $rec){
      echo "<a href='/detail/?page=".$_rec['id']."'><p>"
        . $_rec['content']
        . "<span class='comment'>←" . $_rec['oldest'] . "</span>"
        . "</p></a>";
    }
  ?>
</div>
<?php

getContent();
include "../assets/templete.php";