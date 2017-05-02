<?php
require "../assets/init.php";
require "../assets/util/view.php";

//データチェック
checkCriticalParameter('page');
$st = $pdo->prepare(
  "SELECT question.content AS q, answer.content AS a, name"
  ." FROM answer"
  ." JOIN question ON answer.questionid = question.id"
  ." JOIN user ON answer.userid = user.id"
  ." WHERE answer.id = :id");
$st->bindValue(':id', $_GET['page'], PDO::PARAM_INT);
$st->execute();
$rec = $st->fetch();
if($rec === FALSE){
  noDataError();
}

//本文
?>
<h1><?php echo $rec['q']; ?></h1>
<h3><?php echo $rec['a']; ?></h3>
<p><?php echo $rec['name']; ?></p>
<form>
  <div class="box">
    <label>
      <input type='radio' name='commend' value='0'>
      <img src="/assets/img/commend0.png">Close.
    </label>
    <label>
      <input type='radio' name='commend' value='1'>
      <img src="/assets/img/commend1.png">Good.
    </label>
    <label>
      <input type='radio' name='commend' value='2'>
      <img src="/assets/img/commend2.png">Marvelous.
    </label>
  </div>
  <textarea name="content"></textarea>
  <input type="submit" value="Send">
</form>
<?php

//データを取得
setGet("where=s&order=t&data=n&page=".$_GET['page']);
$_this = "comment";
require "../assets/util/list.php";

//本文
if($_result !== FALSE){
  $i = 0;
  foreach($_result as $rec){
    echo "<p class='comment'><span>" . $rec['content'] . "</span>" . $rec['name'] . "</p>";
  }
}

getContent();
$_action = 'javascript:alert("Sorry, this function is not implemented.");';
$_head = "<script src='/assets/js/common.js' type='text/javascript'></script>";
$_title = "Write comments";
$_page = "detail";
include "../assets/templete.php";