var currentMenuId;
var lastMenuId;
var hideMenuTimeOut = 0;

function hideMenu(id){
    // alert('hide: '+id);
    var menu_holder_id = 'menu-holder-'+id;
    document.getElementById(menu_holder_id).style.display='none';
    // new Effect.Fade(menu_holder_id, {duration: 0.5});
}

function fadeMenu(id){
    // alert('hide: '+id);
    var menu_holder_id = 'menu-holder-'+id;
    // document.getElementById(menu_holder_id).style.display='none';
    new Effect.Fade(menu_holder_id, {duration: 0.6});
}

function mouseOutMenu(id){
    // alert('hide: '+id);
    // hideMenuTimeOut = setTimeOut('hideMenu('+id+')', 3000);
    // hideMenu(id);
}

function mouseOverMenu(id){
    // alert('over');
    currentMenuId = id;
    
    if(currentMenuId == lastMenuId && hideMenuTimeOut){
        // clearTimeOut(hideMenuTimeOut);
    }
    
    var current_menu_holder_id = 'menu-holder-'+id;
    var last_menu_holder_id = 'menu-holder-'+lastMenuId;
    
    if(document.getElementById(last_menu_holder_id) && currentMenuId != lastMenuId){
        // document.getElementById(last_menu_holder_id).style.display='none';
        hideMenu(lastMenuId);
    }
    
    document.getElementById(current_menu_holder_id).style.display='block';
    // new Effect.BlindDown(current_menu_holder_id, {duration: 0.1});
    
    lastMenuId = currentMenuId;
}

function hideAllMenus(){
    hideMenu(1);
    hideMenu(2);
    hideMenu(3);
    hideMenu(4);
    hideMenu(5);
    hideMenu(6);
    hideMenu(7);
}