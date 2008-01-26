var settingsCallback = {
	editUser: function(result) {		
		document.getElementById("user-table").innerHTML = result.comment_content;
	},
}

var setsCallback = {
	copySet: function(result) {		
		document.getElementById("set-table").innerHTML = result.comment_content;
	},
}

var remoteSettings = new Settings(settingsCallback);
var remoteSets = new Sets(setCallback);


