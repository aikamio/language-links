
/* フォーム送信前の確認 */
function registercheck(form){
  /* submitボタンを押せなくする(時短のため後ろから検索) */
  submitAble(form, false);

  try{
    /* チェックボックスの必須確認 */
    var requires = [];
    for(var i=0;i<form.length;i++){
      var input = form[i];
      var requiredValue = input.getAttributeNode("required-value");
      if(input.type == "checkbox" & requiredValue != null){
        var key = requiredValue.value;
        var flag = false;
        for(var j=0;j<requires.length;j++){
          if(requires[j]['key'] == key){
            if(!requires[j]['input'] & input.checked){
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
    }
    for(var i=0;i<requires.length;i++){
      if(!requires[i]['input']){
         throw new Error(requires[i]['key'] + " is required.");
      }
    }

    /* パスワードの確認の一致
       confirmクラスの1つ前のpassword欄をパスワードと解釈する */
    var pass = null;
    for(var i=form.length-1;i>=0;i--){
      var input = form[i];
      if(input.type == "password"){
        if(haveClass(input, "confirm")){
          if(isInput(input.value)){
            pass = input.value;
          }
        }else if(pass != null){
          if(pass == input.value){
            pass = null;
          }else{
            throw new Error("Confirm password is incollect.");
          }
        }
      }
    }
  }catch(e){
    //alert(e);
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