function Smartest(){
	
	this.toVarName = toVarName;
	this.toSlug = toSlug;
	this.cancelForm = cancelForm;
	this.nothing = nothing;
	this.setSelectedItem = setSelectedItem;
	this.setView = setView;
	this.setDomain = setDomain;
	this.setSection = setSection;
	this.setMethod = setMethod;
	this.trim = trim;
	
	function trim(myString){
		return myString.replace("/^[\s_-\.'\"]+|[\s_-\.'\"]+$/", '');
	}

	function toVarName(myString){ 
		var replaceString = myString.replace(/[\s_-,\.@]+/g, '_');
		replaceString = replaceString.replace(/[:\/\'\"]+/g, '');
		var trimmed = this.trim(replaceString);
		var lc = trimmed.toLowerCase();
		return lc;
	}

	function toSlug(myString){ 
		var replaceString = myString.replace(/[\s_-,\.@]+/g, '-');
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
	
	function setDomain(domain){
		this.domain = domain;
	}
	
	function setSection(section){
		this.section = section;
	}
	
	function setMethod(method){
		this.method = method;
	}

	function setSelectedItem(id, category){
	
		var row='item_'+id;
		var editForm = document.getElementById('editForm');
	
		category = category ? category : 'default';
	
		// alert(category);
	
		selectedPage = id;
	
		if(category == 'default' || !category){
			var actionsDiv = 'item-specific-actions';
		}else{
			var actionsDiv = category+'-specific-actions';
		}
	
		if(document.getElementById(actionsDiv)){
			document.getElementById(actionsDiv).style.display = 'block';
		}
	
		if(lastRow){
			document.getElementById(lastRow).className = "option";
		}
	
		// alert(row);
	
		document.getElementById(row).className = "selected-option";
		lastRow = row;
	
		if(document.getElementById("item_id_input")){
			document.getElementById("item_id_input").value = id;
			// alert(document.getElementById("item_id_input"));
		}
	}

	function setView(viewName, list_id){
		if(viewName == "grid"){
			document.getElementById(list_id).className="options-grid";
		}else if(viewName == "list"){
			document.getElementById(list_id).className="options-list";
		}
	}
}