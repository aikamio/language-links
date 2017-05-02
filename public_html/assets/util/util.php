<?php
//直接アクセス禁止
if(get_included_files()[0] === __FILE__) {
  header("Location: /");
  exit;
}

/* Utility methods */

/* Return time after 1 year (use for setcookie) */
function year(){
  return time() + 60*60*24*356;
}

/* Make random key
   Author : http://ameblo.jp/linking/entry-10289895826.html */
function makeRandStr($length) {
  $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
  $r_str = "";
  for ($i = 0; $i < $length; $i++) {
    $r_str .= $str[rand(0, count($str)-1)];
  }
  return $r_str;
}

/* Get hash */
function getHash($_pass){
  return password_hash($_pass, PASSWORD_BCRYPT);
}

/* Set parameter */
function setGet($_str){
  $_GET = [];
  $list = explode("&", $_str);
  foreach($list as $value){
    $index = strpos($value, "=");
    if($index === FALSE){
      continue;
    }
    $_GET[substr($value, 0, $index)] = substr($value, $index+1);
  }
}

/* Make session of this page clear */
function sessionReset($_page){
  $_SESSION['announce'] = [];
  $_SESSION['announce']['page'] = $_page;
}

/* Check is there data other than blanks */
function isInput($_data){
  return preg_match("/\S/", $_data);
}

/* Error announce */
function errorAnn($_text){
  $_SESSION['announce']['type'] = "error";
  $_SESSION['announce']['text'] = $_text;
}

/* OK announce */
function sucAnn($_text){
  $_SESSION['announce']['type'] = "success";
  $_SESSION['announce']['text'] = $_text;
}

/* if error head to home */
function checkCriticalParameter($key){
  if(isset($_GET[$key]) ? !preg_match("/^[0-9]+$/", $_GET[$key]) : true){
    $_SESSION['announce'][$key] = "home";
    errorAnn("Parameter is incorrect.");
    header("Location: ..");
    exit;
  }
}

/* Put out no-data-error and head to home */
function noDataError(){
  $_SESSION['announce']['page'] = "home";
  errorAnn("The content is not exist.");
  header("Location: ..");
  exit;
}

/* Replace by array */
function replace($_text, $_array){
  $pos = strpos($_text,":");
  if($pos !==FALSE){
    $_text = substr($_text,0,$pos).$_array[substr($_text,$pos+1)];
  }
  return $_text;
}

/* Put new column to 2dim-array */
function arraySetColumn($_list, $_key, $_value){
  for($i=0, $max=count($_list); $i<$max; $i++){
    $_list[$i][$_key] = $_value;
  }
}

/* Search 2dim-array width second key and value */
function dimArraySearch($_list, $_key, $_value){
  $array = [];
  for($i=0,$max=count($_list);$i<$max;$i++){
    if($_list[$i][$_key] === $_value){
      array_push($array, $i);
    }
  }
  return $array;
}

/* 文字が含まれる 複数指定化 */
function have(){
  $_str = func_get_args()[0];
  $_keys = array_slice(func_get_args(), 1);
  foreach($_keys as $key){
    if(strpos($_str, $key) !== FALSE){
      return true;
    }
  }
  return false;
}