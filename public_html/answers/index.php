<?php
require "../assets/init.php";
require "../assets/util/view.php";
$_title = "Answers";
$_page = "answers";

//タイトル, 書き込みボタン
if( isset($_GET['where']) &&
    isset($_GET['page']) &&
    isInt($_GET['page']) &&
    have($_GET['where'],"s") &&
    have($_GET['where'],"p") &&
    ($result = getQuestionContent($_GET['page'])) !== false){
  $_popright =
    "<a class='circlebutton' href='/newanswer/?page=".$_GET['page']."'>"
    ."<img src='/assets/img/pen.png'></a>";
  echo "<h1>" . html($result) ."</h1>";
}

//データを取得
$_this = "answer";
require "../assets/util/list.php";

if(isset($_result)){
  //本文
  echo "<div class='box'>";
  foreach($_result as $rec){
    echo "<a href='/detail/?page=".html($rec['id'])."'><div class='line'>"
      ."<span class='answer'>" . html($rec['content']);
    if(isset($rec['name'])){
      echo "<span class='name'>" . html($rec['name']) ."</span>";
    }
    echo "</span>";
    if(isset($rec['commend'])){
      echo "<img src='/assets/img/commend" . html($rec['commend']) . ".png'>";
    }
    if(isset($rec['oldest'])){
      $comment = $rec['oldest'];
      if(strlen($comment) > $_conf['comment_max_in_answers']){
        $comment = substr($comment(0,$_conf['comment_max_in_answers']-1)) . "...";
      }
      echo "<p class='commend'>" . html($comment) . "</p>";
    }
    echo "</div></a>";
  }
  echo "</div>";
}

getContent();
include "../assets/templete.php";
