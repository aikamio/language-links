<?php
//直接アクセス禁止
if(get_included_files()[0] === __FILE__) {
  header("Location: /");
  exit;
}

/* クエリ手引き
   $_GET[where], $_GET[order] ..以下に文法を示す
   whereに指定した場合、限定条件。
   orderに指定した場合、優先条件となり、対象に指定したデータの新着順で表示する。(dをつければ投稿順。)
   文を複数記述する場合はコンマで区切る。

   【対象】
   t(this)/省略時 ... 自分(現在のテーブル)を参照する。
   p(parent) ... 自分が紐づいている親を参照する。(自分がquestionならエラー)
   c(child) ... 自分に紐づいた子を参照する。(自分がcommentならエラー)

   【比較】
   a(all)/省略時 ... 全て。
   m(my) ... 投稿者が自分(ログインしているユーザ)。
   s(select) ... $_GET['page']でidを直接指定。

   【投稿順】
   n(new)/省略時 ... 新しい順。
   o(old) ... 古い順。

   【未読/既読】
   r(read)/省略時 ... すべて。
   u(unread) ... 未読のみ。

   $_GET[list] ..表示件数
   n .. 先頭からn件
   n-m .. n件目からm件目まで

   $_GET[language] ..指定言語
   -1 .. すべて(指定言語=Any ではない)
   n .. 対応する言語
   l(learning) .. 自分(ログインしているユーザ)が勉強している言語

   $_GET['page'] ..比較するID

   $_GET['data'] ..取得したいデータ
   常に取得 ..コンテンツID(id)、コンテンツ内容(content)
   u(unread) ..未読の子供の数(unread)
   c(commend) ..評価(commend)、最も古いコメント(oldest)(answerのみ)
   n(name) ..投稿者の名前(name)

   例：?where=cu&order=c&list=100&language=-1 .. 未読のレスポンスがあるコンテンツすべて、新着レスポンス順100件

   【検索結果】
   配列 $_result に格納する。
   */

//$_this(現在テーブル)が正規か
if(!preg_match("/^(question|answer|comment)$/", $_this)){
  errorAnn("Current table was wrong.");
  return;
}

//クエリの指定がない場合
if(!isset($_GET['where'])){
  $_GET['where'] = 't';
}
if(!isset($_GET['order'])){
  $_GET['order'] = 't';
}
if(!isset($_GET['list'])){
  $_GET['list'] = -1;
}
if(!isset($_GET['language'])){
  $_GET['language'] = -1;
}
if(!isset($_GET['data'])){
  $_GET['data'] = "";
}

//簡易に階層構造をしめす
$ar = [0=>"question", 1=>"answer", 2=>"comment",
  "question"=>0, "answer"=>1, "comment"=>2];
if($ar[$_this] >= 1){
  $parent = $ar[$ar[$_this]-1];
}
if($ar[$_this] <= 1){
  $child = $ar[$ar[$_this]+1];
}

//データチェック
$checkpath = $_GET['where'].$_GET['order'];
if(!preg_match("/^[tcpamsnoru,]*$/",$checkpath) |
    (!isset($parent) & have($checkpath, "p")) |
    (!isset($child) & have($checkpath, "c")) |
    !preg_match("/^(-1|[1-9][0-9]*|[1-9][0-9]*-[1-9][0-9]*)$/", $_GET['list']) |
    !preg_match("/^(-1|[0-9]*|l)$/", $_GET['language']) |
    (have($checkpath, "s") &
      (isset($_GET['page']) ? !preg_match("/^([0-9]*)$/", $_GET['page']) : true )) |
    !preg_match("/^[ucn]*$/", $_GET['data'])){
  sessionReset($_page);
  errorAnn("Incorrect query input.");
  return;
}

//各句初期化
$select = "SELECT $_this.id, $_this.content";
$from = " FROM $_this";
$order = "";

//バインドパラメータ
$bindparam = [];

//取得するデータ (SELECT, JOIN)
if(have($_GET['data'], "u") & isset($child)){  //未読の子供の数
  $select .= ", unread";
  $from .= " LEFT JOIN ("
      ." SELECT COUNT(id) AS unread, {$_this}id"
      ." FROM $child"
      ." WHERE true"
      ." GROUP BY {$_this}id"
    .") AS unreadcount ON unreadcount.{$_this}id = $_this.id";
}
if(have($_GET['data'], "c") & $_this == "answer"){  //評価と最も古いコメント
  $select .= ", answer.commend, oldest";
  $from .= " LEFT JOIN ("
      ." SELECT content AS oldest, answerid"
      ." FROM comment AS cmt1"
      ." JOIN ("
        ."SELECT MIN(id) AS minid FROM comment GROUP BY answerid"
      .") AS cmt2 ON cmt1.id = minid"
    .") AS cmt3 ON cmt3.answerid = answer.id";
}
if(have($_GET['data'], "n")){  //投稿者の名前
  $select .= ", name";
  $from .= " JOIN user ON $_this.userid = user.id";
}

//where文,order文 (JOIN, ORDER)
foreach(['where', 'order'] as $sentence){
  $input = explode(",",$_GET[$sentence]);
  $max = count($input);
  for($i=0; $i<$max; $i++){
    $short = $input[$i];
    $k = "$sentence$i";
    $target = have($short,"p")? $parent :( have($short,"c")? $child : $_this );
    $from .=
      " " . (($sentence == 'order')? "LEFT" : "" ) . " JOIN ("
        ." SELECT * FROM $target AS target$k JOIN ("
          ." SELECT ".( have($short,"o")? "MIN(id)" : "MAX(id)" )
          ." AS compareValue$k FROM $target"
          ." WHERE ";
    if(have($short,"m")){
      $from .= "userid = :userid";
      $bindparam[':userid'] = $_USER['id'];
    }else if(have($short,"s")){
      $from .= "id = :select";
      $bindparam[':select'] = $_GET['page'];
    }else{
      $from .= "true";
    }
    $from .=
          " AND "
          .( have($short,"u")? "true" : "true" ) /* 未読判定 */
          ." GROUP BY "
          .( have($short,"c")? $_this."id" : "id" )
        .") AS compare$k ON target$k.id = compare$k.compareValue$k"
      .") AS table$k ON "
      .( have($short,"c")? "$_this.id = table$k.{$_this}id" :
        ( have($short,"p")? "$_this.{$parent}id = table$k.id" :
        "$_this.id = table$k.id" ));
    if($sentence == 'order'){
      $order .= (($i == 0)? " ORDER BY " : " , ")
        . " compareValue$k IS NULL ASC , compareValue$k "
        . ( have($short,"o")? "ASC" : "DESC" );
    }
  }
}

$liststart = 0;
$listnum = -1;

//表示件数 (LIMIT)
if(preg_match("/^[1-9][0-9]*$/", $_GET['list'])){ //最新N件
  $listnum = (int)$_GET['list'];
}else if(preg_match("/^[1-9][0-9]*-[1-9][0-9]*$/", $_GET['list'])){ //N件目～M件目
  $index = strpos($_GET['list'], "-");
  $liststart = (int)substr($_GET['list'], 0, $index) - 1;
  $listnum = (int)substr($_GET['list'], $index+1) - $liststart;
}
if($listnum >= 0){
  $order .= " LIMIT :liststart, :listnum";
  $bindparam[':liststart'] = $liststart;
  $bindparam[':listnum'] = $listnum;
}

//言語 (WHERE)
if(preg_match("/^[0-9]*$/", $_GET['language'])){
  if($_GET['language'] < count(languages())){
    $from .= " WHERE question.language = :language";
    $bindparam[':language'] = (int)$_GET['language'];
  }
}else if("l" == $_GET['language']){
  $from .= " WHERE question.language IN(-1";
  $i=0;
  foreach(explode(",",$_USER['mothertongue']) as $lang){
    $select .= " ,:lang$i";
    $bind[':lang$i'] = $lang;
    $i++;
  }
}

$query = $select.$from.$order;

//PDOを実行
$st = $pdo->prepare($query);
foreach($bindparam as $key => $value){
  $st->bindValue(
    $key,
    $value,
    gettype($value) == "integer" ? PDO::PARAM_INT : PDO::PARAM_STR );
}
/* デバッグ用
echo "<hr>";
$st->debugDumpParams();
echo "<hr>".$query."<hr>";
*/
$st->execute();
$_result = $st->fetchall();