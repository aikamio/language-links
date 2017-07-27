<?php
  //直接アクセス禁止
  if(get_included_files()[0] === __FILE__) {
    header("Location: /");
    exit;
  }
  /* パラメータ一覧
     【必須】
       $_title      タイトル文字列
       $_page       元のページ名（セッションの照合に用いる）
       $_content    内容
     【任意】
       $_head       追加のヘッダ（jsファイルの読み込みなど）
       $_popright   右ポップボタン
       $_action     formサブミット時のアクション（通常はsubmit.phpを呼び出す）

    CSSについて
    ・/assets/css/style.css を読み込む。
    ・同じディレクトリに style.css があれば読み込む。
    ・body,.fluffywrapper,.fluffyinnerにはstyle.cssでCSSを指定(上書き)しない。
     代わりに.innerbodyを用いる。
  */

  //formのプロパティを自動で追加
  $pos = strpos($_content, "<form");
  if($pos !== FALSE){
    $pos += strlen("<form");
    if(!isset($_action)){
      $_action = "submit.php";
    }
    //トークンの発行
    $token = makeRandStr(30);
    $_content = substr($_content, 0, $pos)." action='".$_action."'".
      " class='formbox' onsubmit='return registercheck(document.register);'".
      " method='post' id='form'>".
      "<input type='hidden' value='$token' name='token'>".
      substr($_content, $pos + strlen(">"));
    //登録
    setToken(( isset($_USER) ? $_USER['id'] : -1 ), $token );
  }
?>
<html>
  <head>
    <meta http-equiv="content-language" content="ja">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-script-type" content="text/javascript">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/assets/css/style.css" type="text/CSS">
    <?php
      if(isset($_head)){
        echo $_head;
      }
      if(file_exists("style.css")){
        echo "<link rel='stylesheet' href='style.css' type='text/CSS'>";
      }
    ?>
    <title><?php echo $_title; ?></title>
  </head>
  <body>
    <div class="fluffywrapper">
      <div class="fluffyinner">
        <div class="innerbody">
          <?php
            if(isset($_SESSION['announce']['text']) & isset($_SESSION['announce']['page'])){
              if($_SESSION['announce']['text'] != "" & $_SESSION['announce']['page'] == $_page){
                echo "<div class='announce ".$_SESSION['announce']['type']."'>".
                  "<p>".$_SESSION['announce']['text']."</p></div>";
                $_SESSION['announce']['text'] = "";
              }
            }
            echo $_content;
          ?>
        </div>
      </div>
      <?php if($_login === false){ ob_start(); } /* ログイン時のみ表示 */ ?>
      <div class="pop left">
        <a class='cutebutton home' href='/'><img src='/assets/img/home.png'></a>
        <a class='cutebutton user' href='/user'><img src='/assets/img/user.png'></a>
      </div>
      <?php if($_login === false){ ob_end_clean(); } ?>
      <div class="pop right">
        <?php
          if(isset($_popright)){
            echo $_popright;
          }
        ?>
      </div>
    </div>
  </body>
</html>