<?php
//直接アクセス禁止
if(get_included_files()[0] === __FILE__) {
  header("Location: /");
  exit;
}

//Start buffer
ob_start();

/* View methods */

/* Output checkboxes by array
   checked = $_array['on'] */
function checkboxes($_array, $_name, $_required, $_label){
  foreach($_array as $cb){
    echo "<label><input type='checkbox' name='".replace($_name,$cb)."'";
    if($_required == true){
      echo " required-value=".$_required;
    }
    if(isset($cb['on']) ? $cb['on'] : false){
      echo " checked";
    }
    echo "> ".replace($_label,$cb)."</label>";
  }
}

/* Output radiobuttons by array
   checked = $_array['on'] */
function radios($_array, $_name, $_value, $_required, $_label){
  foreach($_array as $rb){
    echo "<label><input type='radio' name='$_name' value='".replace($_value,$rb)."'";
    if(isset($rb['on']) ? $rb['on'] : false){
      echo " checked";
    }
    echo "> ".replace($_label,$rb)."</label>";
  }
}

/* Get buffer to $_content and end ob-mode */
function getContent(){
  global $_content;
  $_content = ob_get_contents();
  ob_end_clean();
}