option_sortitems = 1;  // Ёлементы списков отсортированы?

// перемещение <option> из одного <SELECT>'а в другой 
function option_move(fbox,tbox) {
    var i=0, j=0;
    
    for (i=fbox.options.length-1; i>=0; i--) {
        if(fbox.options[i].selected) {
            if (option_sortitems && tbox.options.length>0) {
                if (fbox.options[i].text < tbox.options[0].text)
                    tbox.add(new Option(fbox.options[i].text, fbox.options[i].value),0);
                else
                    for (j=tbox.options.length; j>0; j--)
                        if (fbox.options[i].text >= tbox.options[j-1].text) {
                            tbox.add(new Option(fbox.options[i].text, fbox.options[i].value), j);
                            break;
                        }
            } else {
                tbox.add(new Option(fbox.options[i].text, fbox.options[i].value));
            }            
            fbox.remove(i);
        }
    }
}

// сн€тие выделени€ со всего <select>'а
function option_unselect(box) {
    for (var i=0; i<box.options.length; i++)
        box.options[i].selected = false;
}

//хитро...
function option_compress(box, field) {
    field.value = "";
    for (var i=0; i<box.options.length; i++) {
        if (i>0)
            field.value += ",";
        field.value += box.options[i].value;
    }    
    
   /* if (arguments.length>2){
            arguments[2].value = "";
            for (var i=0; i<box.options.length; i++) {
                if (i>0)
                    arguments[2].value += ",";
                arguments[2].value += box.options[i].text;
            }    
    }*/
    option_unselect(box);
}

//добавление <option> с сортировкой по тексту
function option_add(box, intext, invalue) {
      if (box.options.length<=0)  {
          box.add(new Option(intext, invalue), 0);
          return true;
      } 
      if (intext < box.options[0].text)  {
          box.add(new Option(intext, invalue), 0);
          return true;
      } 
      if (intext > box.options[box.options.length-1].text) {
          box.add(new Option(intext, invalue));
          return true;
      } 
     for (var i=box.options.length; i >= 0; i--) {
          if (intext >= box.options[i-1].text || box.options[i-1].value<0) {
                box.add(new Option(intext, invalue), i);
                break;
          }
      }
   
}

//удаление <option>
function option_delete(box, del_value) {
    for (var i=0; i<box.options.length; i++)
        if (box.options[i].value==del_value) {
            box.remove(i);
            break;
        }
}

// приведение списков в пор€док
function option_select(form,name,index) {
    the_select = form.elements(name+'['+index+']');
    del_value = the_select.options[the_select.selectedIndex].value;
    add_value = selected_options[index];
    for (var i=0; i<the_select.options.length; i++) 
        if (the_select.options[i].value == selected_options[index]) {
            add_text = the_select.options[i].text;
            break;
        }
    for (i=0; i<selected_options.length; i++) {
        if (i != index) {
            if (add_value > 0)
                option_add(form.elements(name+'['+i+']'), add_value, add_text);
            if (del_value > 0)
                option_delete(form.elements(name+'['+i+']'), del_value);
        }
    }
    selected_options[index] = del_value;
}

// подсписки
function suboption(box, subbox, values, descriptions) {
    for (var i = subbox.options.length-1; i>=0; i--)
        subbox.remove(i);
    for (i = 0; i<values[box.selectedIndex].length; i++)
        subbox.add(new Option(descriptions[box.selectedIndex][i], values[box.selectedIndex][i]));
    subbox.options[0].selected=true;
}

//функци€ добавл€ет новую опцию с текстом txt и value val в список box (без сортировки)
function new_option(box, txt, val){
    var  new_opt = new Option (txt, val); 
    box.options[box.length] = new_opt;   
}
//функци€ удал€ет из списка все выделенное
function option_delete_selected(box){
    var flag;
    while (1) {
           flag=false; 
           for (var i=0; i<box.length; i++){
                   if (box.options[i].selected){
                           box.options[i]=null;
                           flag=true;
                           break;
                   }    
           }
           if (!flag) break;
    }              
}
// копирует значение одного элемента в другой  
function valueCopy(elementFrom, elementTo)
{
    elementTo.value = elementFrom.value;
}

function option_compress(box, field) 
{
    field.value = "";
    for (var i=0; i<box.options.length; i++) {
        if (i>0)
            field.value += ",";
        field.value += box.options[i].value;
    }    
}

