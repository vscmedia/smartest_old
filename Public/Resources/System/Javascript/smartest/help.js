Smartest.HelpViewer = Class.create({
    
    isVisible: false,
    history: [],
    current: null,
    
    load: function(help_id){
        
        var url = sm_domain+'ajax:smartest/help/ajax/view/'+help_id;
        
        if(!this.isVisible){
            this.showNew();
        }
        
        this.updateTo(url);
        // this.history
        this.current = help_id;
        
    },
    
    showNew: function(){
        if(!this.isVisible){
            $('help-updater').update("<p>Loading...</p>");
            $('help').appear({duration: 0.4, to: 0.9});
            this.isVisible = true;
        }
    },
    
    updateTo: function(url){
        $('help-updater').hide();
        $('help-updater').update("<p>Loading...</p>");
        new Ajax.Updater($('help-updater'), url, {evalScripts: true});
        $('help-updater').appear({duration: 0.4, delay: 0.3});
    },
    
    hideViewer: function(){
        if(this.isVisible){
            $('help').fade({duration: 0.3});
            this.isVisible = false;
        }
    },
    
    back: function(){
        
    }

});