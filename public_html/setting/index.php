<?php
  require '../assets/init.php';
  require '../assets/util/view.php';

?>
<form>
  <p>
    <span class='q'>Name</span>
    <input type='text' name='name' <?php
      echo "value='".$_USER['name']."'";
    ?>>
  </p>
  <p>
    <span class='q'>Learning language</span>
    <?php
      $max=count($learning = explode(",", $_USER['learning']));
      $list = languages();
      for($i=0; $i<$max; $i++){
        $list[$learning[$i]]['on'] = true;
      }
      checkboxes($list, "learn:id", "Learning language", ":name");
    ?>
  </p>
  <p>
    <span class='q'>Mother tongue</span>
    <?php
      arraySetColumn($list, 'on', false);
      array_push($list, array('id'=>'-1', 'name'=>'Other'));
      $id = dimArraySearch($list, 'id', $_USER['mothertongue'])[0];
      $list[$id]['on'] = true;
      radios($list, "mother", ":id", true, ":name");
    ?>
  </p>
  <p>
    <span class='q'>Mailaddress</span>
    <input type='email' name='mail'<?php
      if(isset($_USER['mail'])){
        echo " value='".$_USER['mail']."'";
      }
    ?>>
  </p>
  <p>
    <span class='q'>Password</span>
    <input type='password' name='password'>
  </p>
  <p>
    <span class='q'>Confirm password</span>
    <input type='password' name='confirm' class="confirm">
  </p>
  <a href="/"><input type='button' value='Back' class='formbutton'></a>
  <input type='submit' value='Register' class='formbutton'>
</form>
<?php

  getContent();
  $_head = "<script src='/assets/js/common.js' type='text/javascript'></script>";
  $_title = "Edit user information";
  $_page = "setting";
  include "../assets/templete.php";