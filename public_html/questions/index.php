<?php
require "../assets/init.php";
require "../assets/util/view.php";
$_title = "QandA";
$_page = "questions";

//データを取得
$_this = "question";
require "../assets/util/list.php";

//本文
if(isset($_GET['where']) ? !have($_GET['where'],"m","s") : true){
  echo "<h1>All questions</h1>";
}
foreach($_result as $rec){
  echo "<a href='/answers/?where=s&data=cn&page=".$rec['id']."'>"
    ."<p class='line'>" . $rec['content'] . "</p></a>";
}

getContent();
include "../assets/templete.php";