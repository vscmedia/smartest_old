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
		editForm.action = sm_domain+sm_section+"/"+pageAction;
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

function hideUserMessage(message_id){
	// if(!getUIEffectsAreOk()){
	//	document.getElementById(message_id).style.display = 'none';
	// }else{
		new Effect.Fade(message_id, { duration: 0.5 });
	// }
}