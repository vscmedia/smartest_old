var selectedPage = null;
var selectedPageName = null;
var selectedItemCategory = null;
var lastRow;
var lastRowColor;
var lastItemCategory;

var SmartestInterface = Class.create();

SmartestInterface.prototype = {

    trim: function(myString){
    	return myString.replace("/^[\s_\.'\"-]+|[\s_\.'\"-]+$/", '');
    },

    toVarName: function(myString){ 
    	var replaceString = myString.replace(/[\s_,\.@-]+/g, '_');
    	replaceString = replaceString.replace(/[:\/\'\"]+/g, '');
    	var trimmed = this.trim(replaceString);
    	var lc = trimmed.toLowerCase();
    	return lc;
    },

    toSlug: function(myString){ 
    	var replaceString = myString.replace(/[\s_,\.@-]+/g, '-');
    	replaceString = replaceString.replace(/[\'\"]+/g, '');
    	var trimmed = this.trim(replaceString);
    	var lc = trimmed.toLowerCase();
    	return lc;
    },

    cancelForm: function(){
    	history.go(-1);
    },

    nothing: function(){
	
    },

    getUIEffectsAreOk: function(){
    	return (sm_user_agent.appName == 'Explorer') ? false : true;
    },

    isIE6OrBelow: function(){
    	return (sm_user_agent.appName == 'Explorer' && (sm_user_agent.appVersion.charAt(0)*1) < 7) ? true : false;
    },

    setSelectedItem: function(id, name, category){

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
    			if(!this.getUIEffectsAreOk()){
    				document.getElementById(lastActionsDiv).style.display = 'none';
    			}else{
    				// alert('fading down '+lastActionsDiv);
    				// new Effect.Fade(lastActionsDiv, { duration: 0.2 });
    				document.getElementById(lastActionsDiv).style.display = 'none';
    			}
    		}
		
    		if(!this.getUIEffectsAreOk()){
    			document.getElementById(actionsDiv).style.display = 'block';
    		}else{
    			// alert('fading up '+actionsDiv);
    			// new Effect.Appear(actionsDiv, { duration: 0.2 });
    			document.getElementById(actionsDiv).style.display = 'block';
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
    },

    workWithItem: function(pageAction){
	
    	var editForm = document.getElementById('pageViewForm');	
    	if(selectedPage && editForm){
    		editForm.action="/"+sm_section+"/"+pageAction;
    		editForm.submit();
    	}
    },

    setView: function(viewName, list_id){
    	if(viewName == "grid"){
    		document.getElementById(list_id).className="options-grid";
    	}else if(viewName == "list"){
    		document.getElementById(list_id).className="options-list";
    	}
    },

    hideUserMessage: function(message_id){
	    if(!this.getUIEffectsAreOk()){
		    document.getElementById(message_id).style.display = 'none';
	    }else{
		    new Effect.Fade(message_id, { duration: 0.5 });
	    }
    }

}