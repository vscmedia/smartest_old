var selectedPage = null;
var selectedPageName = null;
var selectedItemCategory = null;
var lastRow;
var lastRowColor;
var lastItemCategory;

function trim(myString){
	return myString.replace("/^[\s_\.'\"-]+|[\s_\.'\"-]+$/", '');
}

function toVarName(myString){ 
	var replaceString = myString.replace(/[\s_,\.@-]+/g, '_');
	replaceString = replaceString.replace(/[:\/\'\"]+/g, '');
	var trimmed = this.trim(replaceString);
	var lc = trimmed.toLowerCase();
	return lc;
}

function toSlug(myString){ 
	var replaceString = myString.replace(/[\s_,\.@-]+/g, '-');
	replaceString = replaceString.replace(/[\'\"]+/g, '');
	var trimmed = this.trim(replaceString);
	var lc = trimmed.toLowerCase();
	return lc;
}

function cancelForm(){
	history.go(-1);
}

function nothing(){
	
}

function getUIEffectsAreOk(){
	return (sm_user_agent.appName == 'Explorer') ? false : true;
}

function isIE6OrBelow(){
	return (sm_user_agent.appName == 'Explorer' && (sm_user_agent.appVersion.charAt(0)*1) < 7) ? true : false;
}

function setSelectedItem(id, name, category){

	var row='item_'+id;
	var editForm = document.getElementById('editForm');

	category = category ? category : 'default';

	// alert(category);

	selectedPage = id;
	selectedItemCategory = category;

	if(category == 'default' || !category){
		var actionsDiv = 'item-specific-actions';
	}else{
		var actionsDiv = category+'-specific-actions';
		if(lastItemCategory){
			var lastActionsDiv = lastItemCategory+'-specific-actions';
		}
	}
	
	if(document.getElementById(actionsDiv)){
		
		if(lastItemCategory != selectedItemCategory && document.getElementById(lastActionsDiv)){
			//if(!getUIEffectsAreOk()){
			//	document.getElementById(lastActionsDiv).style.display = 'none';
			//}else{
				// alert('fading down '+lastActionsDiv);
				
				setTimeout('new Effect.BlindUp("'+lastActionsDiv+'", { duration: 0.2 })', 1);
				// document.getElementById(lastActionsDiv).style.display = 'none';
			//}
		}
		
		if(lastItemCategory != selectedItemCategory && document.getElementById(actionsDiv)){
		    //if(!getUIEffectsAreOk()){
			//    document.getElementById(actionsDiv).style.display = 'block';
		    //}else{
			    // alert('fading up '+actionsDiv);
			    setTimeout('new Effect.BlindDown("'+actionsDiv+'", { duration: 0.2 })', 250);
			    // document.getElementById(actionsDiv).style.display = 'block';
		    //}
	    }
		
	}

	if(lastRow && document.getElementById(lastRow)){
		document.getElementById(lastRow).className = "option";
	}
	
	if(document.getElementById(row)){
		document.getElementById(row).className = "selected-option";
	}
	
	lastRow = row;
	lastItemCategory = selectedItemCategory;

	if(document.getElementById("item_id_input")){
		document.getElementById("item_id_input").value = id;
	}else{
		// alert('input not found');
	}
}

function getWebContent(url){
    new Ajax.Request(url,
      {
        
        method:'get',
        
        onSuccess: function(transport){
          var response = transport.responseText || "Not Found";
          // alert("Success! \n\n" + response);
          return response;
        },
        
        onFailure: function(){ return false; }
      });
}

function workWithItem(pageAction){
	
	// alert(sm_domain+sm_section+"/"+pageAction);
	var editForm = document.getElementById('pageViewForm');	
	
	if(selectedPage && editForm){
	    
	    // alert(pageAction.split("/").length());
	    
	    //if(pageAction.split("/").length() > 1){
	    //    editForm.action = sm_domain+pageAction;
	    //}else{
		    editForm.action = sm_domain+sm_section+"/"+pageAction;
	    //}
	    
	    editForm.submit();
	}
}

function setView(viewName, list_id){
	if(viewName == "grid"){
		document.getElementById(list_id).className="options-grid";
	}else if(viewName == "list"){
		document.getElementById(list_id).className="options-list";
	}
}

function toggleMenuVisibility(menu_id){
    var menu = $(menu_id);
    if(menu.style.display='none'){
        new Effect.BlindDown(menu_id, { duration: 0.3 });
        // menu.style.display='block';
    }else if(menu.style.display='block'){
        new Effect.BlindUp(menu_id, { duration: 0.3 });
        // menu.style.display='none';
    }
}

function hideUserMessage(message_id){
	// if(!getUIEffectsAreOk()){
	//	document.getElementById(message_id).style.display = 'none';
	// }else{
		new Effect.Fade(message_id, { duration: 0.5 });
	// }
}

function getCaretPosition(textarea_id){
    
    var textArea = document.getElementById(textarea_id);
    
    if(sm_user_agent.appName == 'Explorer'){
        // Internet Explorer
        if(document.selection){
            
            var marker = "__SM_TEXT_MARKER__";
            // var realSelectionRange	= document.selection.createRange();
            // var otherSelectionRange = realSelectionRange.duplicate();
            // var excapedText = 
            var otherTextArea = Object.clone(textArea);
            alert(otherTextArea);
            // var caret_pos = 0;
            
            // create a real selection from the real textarea
            var realSelectionRange	= document.selection.createRange();
            
            // create a fake copy
            var otherSelectionRange = realSelectionRange.duplicate();
            // otherSelectionRange.moveToElementText(otherTextArea);
            
            // put the marker right before it
            // var otherSelectionRange.text = marker+otherSelectionRange.text;
            
            // alert its text
            // alert(otherSelectionRange.text);
            
            var caret_pos = 0;
        }else{
            textarea.focus();
            var caret_pos = textarea.value.length;
        }
        // var dul	= sel.duplicate();
        // var len	= 0;
        // alert(sel.text.length);
        // alert(textarea);
        // dul.moveToElementText(textarea);
        // sel.text = c;
        // len = (dul.text.indexOf(c));
        // sel.moveStart('character', -1);
        // sel.text = "";
        // return len;
        // var caret_pos = len;
    }else{
        // Gecko & Safari:
        var caret_pos = textArea.selectionStart;
    }
    
    return caret_pos;
    
}