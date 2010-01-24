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

/* Smartest.UI.TreeView = Class.create({
    
    initialize: function(){
        this.nodeStates = [];
    },
    
    toggleParentNode: function(node_id){
        
        var list_id = 'list_'+node_id;
        
        if(this.nodeStates[list_id] == 0){

    		if(Smartest.UI.allowEffects()){
    		    new Effect.Appear(list_id, { duration: 0.4 });
    		}else{
    			document.getElementById(list_id).style.display = 'block';
    		}

    		document.getElementById('toggle_'+node_id).src = sm_domain+'Resources/System/Images/open.gif';
    		this.nodeStates[list_id] = 1;
    		
    	}else{

    		if(Smartest.UI.allowEffects()){
    		    new Effect.Fade(list_id, { duration: 0.4 });
    		}else{
    			document.getElementById(list_id).style.display = 'none';
    		}

    		document.getElementById('toggle_'+node_id).src = sm_domain+'Resources/System/Images/close.gif';
    		this.nodeStates[list_id] = 0;
    	}
    },
    
    getNodeStatus: function(node_id){
        
    }
    
}); */