Smartest.HelpViewer = Class.create({
    
    isVisible: false,
    
    load: function(help_id){
        
        var url = sm_domain+'ajax:smartest/help/ajax/view/'+help_id;
        
        if(!this.isVisible){
            this.showNew();
        }
        
        this.updateTo(url);
        
    },
    
    showNew: function(){
        if(!this.isVisible){
            $('help-updater').innerHTML = "<p>Loading...</p>";
            $('help').appear({duration: 0.4, to: 0.9});
            this.isVisible = true;
        }
    },
    
    updateTo: function(url){
        $('help-updater').hide();
        $('help-updater').innerHTML = "<p>Loading...</p>";
        new Ajax.Updater($('help-updater'), url);
        $('help-updater').appear({duration: 0.4, delay: 0.3});
    },
    
    hideViewer: function(){
        if(this.isVisible){
            $('help').fade({duration: 0.3});
            this.isVisible = false;
        }
    }

});