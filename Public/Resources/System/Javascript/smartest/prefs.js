Smartest.PreferencesBridge = Class.create({
    
    initialize: function(appShortName){
        this.urlRoot = sm_domain+sm_section+'/';
    },
    
    getApplicationPreference: function(pref_name){
        url = this.urlRoot+'getApplicationPreferenceFromAjax';
        new Ajax.Request(url, {
            method: 'post',
            parameters: {'pref_name': pref_name},
            onSuccess: function(){
                
            }
        });
    },
    
    setApplicationPreference: function(pref_name, value){
        url = this.urlRoot+'setApplicationPreferenceFromAjax';
        new Ajax.Request(url, {
            method: 'post',
            parameters: {'pref_name': pref_name, 'pref_value': value},
            onSuccess: function(){
                
            }
        });
    },
    
    getGlobalPreference: function(pref_name){
        url = this.urlRoot+'getGlobalPreferenceFromAjax';
        new Ajax.Request(url, {
            method: 'post',
            parameters: {'pref_name': pref_name},
            onSuccess: function(){
                
            }
        });
    },
    
    setGlobalPreference: function(pref_name, value){
        url = this.urlRoot+'setGlobalPreferenceFromAjax';
        new Ajax.Request(url, {
            method: 'post',
            parameters: {'pref_name': pref_name, 'pref_value': value},
            onSuccess: function(){
                
            }
        });
    }

});