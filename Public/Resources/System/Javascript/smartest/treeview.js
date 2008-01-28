function toggleParentNodeFromOpenState(node_id){

	var list_id = 'list_'+node_id;

	if(treeNodes[list_id] == 0){
		
		if(!getUIEffectsAreOk()){
			document.getElementById(list_id).style.display = 'block';
		}else{
			new Effect.Appear(list_id, { duration: 0.4 });
		}
		
		document.getElementById('toggle_'+node_id).src = sm_domain+'Resources/System/Images/open.gif';
		treeNodes[list_id] = 1;
	}else{
		
		if(!getUIEffectsAreOk()){
			document.getElementById(list_id).style.display = 'none';
		}else{
			new Effect.Fade(list_id, { duration: 0.4 });
		}
		
		document.getElementById('toggle_'+node_id).src = sm_domain+'Resources/System/Images/close.gif';
		treeNodes[list_id] = 0;
	}
}

function toggleParentNodeFromClosedState(node_id){

	var list_id = 'list_'+node_id;

	if(treeNodes[list_id] == 1){
		
		if(!getUIEffectsAreOk()){
			document.getElementById(list_id).style.display = 'none';
		}else{
			new Effect.Fade(list_id, { duration: 0.4 });
		}
		
		document.getElementById('toggle_'+node_id).src = sm_domain+'Resources/System/Images/close.gif';
		treeNodes[list_id] = 0;
	}else{
		
		if(!getUIEffectsAreOk()){
			document.getElementById(list_id).style.display = 'block';
		}else{
			new Effect.Appear(list_id, { duration: 0.4 });
		}
		
		document.getElementById('toggle_'+node_id).src = sm_domain+'Resources/System/Images/open.gif';
		treeNodes[list_id] = 1;
	}
}