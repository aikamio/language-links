<?php
require "assets/init.php";
require "assets/util/view.php";

?>
<div class='box_wrap col'>
  <a href='/newanswer/today.php' id='today' class='box'>
    Today's Q&amp;A
  </a>
  <div class='box_wrap'>
    <a href='/mail' id='mail' class='box image'>
      <img src='/assets/img/mail.png'>
    </a>
    <div class='box_wrap col' style='flex-grow:10'>
      <a href='/user' id='user' class='box'>
        My page
      </a>
      <div class='box_wrap'>
        <a href='/questions' id='questions' class='box'>
          All Q&amp;A
        </a>
        <a href='/newquestion' id='newquestion' class='box'>
          Make Q&amp;A
        </a>
      </div>
    </div>
  </div>
</div>
<h2>Other</h2>
<ul class='linklist'>
  <a href='/login'><li>Login as another user</li></a>
  <a href='/setting'><li>Confirm my property</li></a>
</ul>
<?php

  getContent();
  $_title = "Home";
  $_page = "home";
  include "assets/templete.php";