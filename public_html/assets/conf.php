<?php
//直接アクセス禁止
if(get_included_files()[0] === __FILE__) {
  header("Location: /");
  exit;
}

/*---- Config ----*/
$_conf['cookie_max'] = 3; /* Max login of 1 user */
$_conf['reissue_timelimit'] = "-1 hour"; /* (strtotime() format) */
$_conf['token_timelimit'] = "-30 minute";
$_conf['comment_max_in_answers'] = 10;
