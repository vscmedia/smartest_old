var selectedPage = null;
var selectedPageName = null;
var selectedItemCategory = null;
var lastRow;
var lastRowColor;
var lastItemCategory;

String.prototype.trim = function(){
	return this.replace("/^[\s_\.'\"-]+|[\s_\.'\"-]+$/", '');
}

String.prototype.toVarName = function(){ 
	var replaceString = this.replace(/[\s_,\.@-]+/g, '_');
	replaceString = replaceString.replace(/[\'\"]+/g, '');
	var trimmed = replaceString.trim();
	var lc = trimmed.toLowerCase();
	return lc;
};

String.prototype.toSlug = function(){ 
	var replaceString = this.replace(/[\s_,\.@-]+/g, '-');
	replaceString = replaceString.replace(/[\'\"]+/g, '');
	var trimmed = replaceString.trim();
	var lc = trimmed.toLowerCase();
	return lc;
};

String.prototype.toUserName = function(){ 
	var replaceString = this.trim();
	// replaceString = replaceString.replace('-', '');
	replaceString = replaceString.replace(/[^\w\._]+/g, '');
	return replaceString.toLowerCase();
};

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
	// history.go(-1);
	if(sm_cancel_uri){
	  // alert(sm_cancel_uri);
	  window.location=sm_cancel_uri;
	}
}

function nothing(){}

function toggleFormAreaVisibilityBasedOnCheckbox(checkbox_id, form_div_id){
  if($(checkbox_id).checked){
    new Effect.BlindDown(form_div_id, {duration: 1.5, transition: Effect.Transitions.spring});
  }else{
    new Effect.BlindUp(form_div_id, {duration: 0.5, transition: Effect.Transitions.sinoidal});
  }
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
			
			setTimeout('new Effect.BlindUp("'+lastActionsDiv+'", { duration: 0.2 })', 1);
			
		}
		
		if(lastItemCategory != selectedItemCategory && document.getElementById(actionsDiv)){
		    
		    setTimeout('new Effect.BlindDown("'+actionsDiv+'", { duration: 0.2 })', 250);
			
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
		// input not found
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
	new Effect.Fade(message_id, { duration: 0.5 });
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

var Smartest = Class.create({});

Smartest.UI = Class.create({
    
    toggleFormAreaVisibilityBasedOnCheckbox: function(checkbox_id, form_div_id){
        if($(checkbox_id).checked){
            new Effect.BlindDown(form_div_id, {duration: 1.5, transition: Effect.Transitions.sinoidal});
        }else{
            new Effect.BlindUp(form_div_id, {duration: 0.5, transition: Effect.Transitions.sinoidal});
        }
    },
    
    updateSpansByClassName: function(className, content){
        $$('span.'+className).find(function(e){e.update(content);});
    },
    
    allowEffects: function(){
        return (sm_user_agent.appName == 'Explorer') ? false : true;
    }
    
});

Smartest.UI.Menu = Class.create({
    
    isVisible: false,
    
    initialize: function(id, link_id){
        this.menuId = id;
        this.linkId = link_id;
        $(this.menuId).style.display = 'none';
    },
    
    show: function(){
        new Effect.BlindDown(this.menuId, { duration: 0.2 });
        this.isVisible = true;
        $(this.linkId).className = 'js-menu-activator-current';
    },
    
    hide: function(){
        new Effect.BlindUp(this.menuId, { duration: 0.2 });
        this.isVisible = false;
        $(this.linkId).className = 'js-menu-activator';
    },
    
    toggleVisibility: function(){
        if(this.isVisible){
            this.hide();
        }else{
            this.show();
        }
    }
    
});

Smartest.UI.OptionSet = Class.create({
    
    initialize: function(formId, inputId, optionClass, listId){
        
        this.setFormId(formId);
        this.setPrimaryInputId(inputId);
        this.optionClass = optionClass;
        
        if($(listId)){
            this.setListId(listId);
        }
    },
    
    setFormId: function(id){
        if($(id)){
            this.form = $(id);
        }else{
            // TODO: create a new form and append it to the document
        }
    },
    
    setPrimaryInputId: function(id){
        if($(id)){
            this.primaryInput = $(id);
        }else{
            // TODO: create a new input and append it to the form
        }
    },
    
    setListId: function(id){
        this.listId = id;
        this.listStyle = $(this.listId).hasClassName('options-list') ? 'list' : 'grid';
    },
    
    showOnly: function(className){
        var cats = $$('.'+this.optionClass).partition(function(item){return item.hasClassName(className)});
        cats[1].find(function(item){new Effect.Fade(item, {duration: 0.2}); });
        cats[0].find(function(item){new Effect.Appear(item, {duration: 0.2, delay: 0.21}); });
    },
    
    setSelectedItem: function(id, category, params){
        
        this.currentCategoryName = category ? category : 'default';
        
        if(this.currentCategoryName == 'default' || !this.currentCategoryName){
    		this.actionsDivId = 'item-specific-actions';
    	}else{
    		this.actionsDivId = category+'-specific-actions';
    		if(this.lastItemCategoryName){
    			this.lastActionsDivId = this.lastItemCategoryName+'-specific-actions';
    		}
    	}
    	
    	if($(this.actionsDivId)){
            
            if(this.lastItemCategoryName){
    		    if(this.lastItemCategoryName != this.currentCategoryName){
                    if($(this.lastActionsDivId)){
                        new Effect.BlindUp(this.lastActionsDivId, { duration: 0.2 });
                    }
                    new Effect.BlindDown(this.actionsDivId, { duration: 0.2, delay: 0.21, transition: Effect.Transitions.sinoidal});
                }
    	    }else{
    	        new Effect.BlindDown(this.actionsDivId, { duration: 0.4, transition: Effect.Transitions.sinoidal });
    	    }
    	    
    	}
    	
    	if(this.lastItemId){
    	    var lastDomID = this.lastItemCategoryName+'_'+this.lastItemId;
    	    if($(lastDomID)){
    		    $(lastDomID).className = "option";
		    }
    	}
    	
    	var domID = this.currentCategoryName+'_'+id;
        
        if($(domID)){
    		$(domID).className = "selected-option";
    	}
    	
    	if(params && params.updateFields){
    	    $H(params.updateFields).each(function(f){
    	        new Smartest.UI().updateSpansByClassName(f.key, f.value);
    	    });
    	}
    	
    	this.lastItemId = id;
    	this.lastItemCategoryName = this.currentCategoryName;
    	this.primaryInput.value = id;
    	
    },
    
    getOptionElement: function(id, category){
        if($(id)){
            return $(id);
        }else{
            if(category){
                var domID = category+'_'+id;
                if($(domID)){
                    return $(domID);
                }
            }else if(this.currentCategoryName){
                var domID = this.currentCategoryName+'_'+id;
                if($(domID)){
                    return $(domID);
                }
            }
        }
    },
    
    workWithItem: function(action, params){
        
        var app = (params && params.application) ? params.application : sm_section;
        
        if(this.lastItemId){
            if((params && params.confirm && confirm(params.confirm)) || (!params || (params && !params.confirm))){
                this.form.action = sm_domain+app+"/"+action;
	            this.form.submit();
            }
        }
    },
    
    setView: function(view){
        if(view == 'grid'){
            $(this.listId).className = 'options-grid';
        }
        if(view == 'list'){
            $(this.listId).className = 'options-list';
        }
    }
    
});

Smartest.UI.CheckBoxGroup = Class.create({
    
    initialize: function(className){
        this.className = className;
    },
    
    selectAll: function(){
        $$('input.'+this.className).find(function(e){e.checked = true;});
    },
    
    selectNone: function(){
        $$('input.'+this.className).find(function(e){e.checked = false;});
    }
    
});

Smartest.UI.TagsList = Class.create({
    
    initialize: function(){
        
    },
    
    tagItem: function(item_id, tag_id){
        var url = sm_domain+'ajax:datamanager/tagItem?item_id='+item_id+'&tag_id='+tag_id;
        new Ajax.Request(url, {
            method: 'get',
            onSuccess: function(transport) {
                var l = $('tag-link-'+tag_id);
                l.addClassName('selected');
            } 
        });
    },
    
    unTagItem: function(item_id, tag_id){
        var url = sm_domain+'ajax:datamanager/unTagItem?item_id='+item_id+'&tag_id='+tag_id;
        // alert(url);
        new Ajax.Request(url, {
            method: 'get',
            onSuccess: function(transport) {
                var l = $('tag-link-'+tag_id);
                l.removeClassName('selected');
            } 
        });
    },
    
    toggleItemTagged: function(item_id, tag_id){
        var l = $('tag-link-'+tag_id);
        if(l.hasClassName('selected')){
            this.unTagItem(item_id, tag_id);
        }else{
            this.tagItem(item_id, tag_id);
        }
    },
    
    tagPage: function(){
        
    },
    
    unTagPage: function(){
        
    }
    
});