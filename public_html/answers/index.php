<?php
require "../assets/init.php";
require "../assets/util/view.php";
$_title = "Answers";
$_page = "answers";

//タイトル
if((isset($_GET['where'])&isset($_GET['page'])) ? have($_GET['where'],"s") : false){
  $st = $pdo->prepare("SELECT content FROM question WHERE id = :id LIMIT 1");
  $st->bindValue(':id', $_GET['page'], PDO::PARAM_INT);
  $st->execute();
  if(($rec = $st->fetch()) !== FALSE){
    echo "<h1>" . $rec['content'] ."</h1>";
  }
}

//データを取得
$_this = "answer";
require "../assets/util/list.php";

if(isset($_result)){
  //本文
  echo "<div class='box'>";
  foreach($_result as $rec){
    echo "<a href='/detail/?page=".$rec['id']."'><div class='line'>"
      ."<span class='answer'>" . $rec['content'];
    if(isset($rec['name'])){
      echo "<span class='name'>" . $rec['name'] ."</span>";
    }
    echo "</span>";
    if(isset($rec['commend'])){
      echo "<img src='/assets/img/commend" . $rec['commend'] . ".png'>";
    }
    if(isset($rec['oldest'])){
      $comment = $rec['oldest'];
      if(strlen($comment) > $_conf['comment_max_in_answers']){
        $comment = substr($comment(0,$_conf['comment_max_in_answers']-1)) . "...";
      }
      echo "<p class='commend'>" . $comment . "</p>";
    }
    echo "</div></a>";
  }
  echo "</div>";
}

getContent();
$_popright =
  "<a class='circlebutton' href='/newanswer/?page=".$_GET['page']."'>"
  ."<img src='/assets/img/pen.png'></a>";
include "../assets/templete.php";