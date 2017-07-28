
/* フォーム送信前の確認 */
function registercheck(form){
  console.log('test start');
  /* submitボタンを押せなくする(時短のため後ろから検索) */
  submitAble(form, false);

  try{
    var requires = [];
    var pass = null;
    for(var i=form.length-1;i>=0;i--){
      /* チェックボックスの必須確認 */
      var input = form[i];
      var type = input.type;
      var requiredValue = input.getAttributeNode("required-value");
      if(type == "checkbox" && requiredValue != null){
        var key = requiredValue.value;
        var flag = false;
        for(var j=0;j<requires.length;j++){
          if(requires[j]['key'] == key){
            if(!requires[j]['input'] && input.checked){
              requires[j]['input'] = true;
            }
            flag = true;
            break;
          }
        }
        if(!flag){
          requires.push({'key':key, 'input':input.checked});
        }
      }
      /* バリデーションチェック
         対象：text, password
         空欄は対象外 */
      if(( type == "text" || type == "password" || type=="email" )&&
         ( input.value.length > 0 )&& input.dataset.regex){
        if(!input.value.match(
            new RegExp(
              input.dataset.regex.replace(
                /\(:mail\)/, "[a-zA-Z0-9\\.!#$%&'*+=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\\.[a-zA-Z0-9-]+)*" )))){
          throw new Error(input.name + " is invalid.");
        }
      }
      /* パスワードの確認の一致
         confirmクラスの1つ前のpassword欄をパスワードと解釈する
         上位のfor文でお尻から検索することに注意 */
      if(type == "password"){
        if(haveClass(input, "confirm")){
          pass = input.value;
        }else if(pass != null){  //nullと""を区別しなければいけないことに注意
          if(pass == input.value){
            pass = null;
          }else{
            throw new Error("Confirm password is incollect.");
          }
        }
      }
    }
    /* チェックボックスの必須確認結果 */
    for(var i=0;i<requires.length;i++){
      if(!requires[i]['input']){
         throw new Error(requires[i]['key'] + " is required.");
      }
    }
  }catch(e){
    alert(e.message);
    submitAble(form, true);
    return false;
  }
  return true;
}

/* クラスを持つか */
function haveClass(element, haveclass){
  var classname = element.getAttributeNode("class");
  if(classname == null){
    return false;
  }
  var array = classname.value.split(" ");
  for(var i=0;i<array.length;i++){
    if(array[i] == haveclass){
      return true;
    }
  }
  return false;
}

/* submitボタンの有効無効切り替え */
function submitAble(form, able){
  for(var i=form.length-1;i>=0;i--){
    var input = form[i];
    if(input.type == "submit"){
      input.disabled = !able;
      return;
    }
  }
}

/* 空白以外の文字を含むか */
function isInput(text){
  return text.match(/\S/) != null;
}