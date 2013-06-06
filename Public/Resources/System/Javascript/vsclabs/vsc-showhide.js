VSC.ShowHide = Class.create({
    
    initialize: function(controlElementsClassSelector, targetElementsClassSelector, options){
        
        $$(controlElementsClassSelector).each(function(el){
            el.observe('click', function(event){
                document.fire('showhide:changed', {focusElement: el.id});
                event.stop();
            });
        });
        
        document.observe('showhide:changed', this.showElementFromTrigger.bindAsEventListener(this));
        
        $$(targetElementsClassSelector).each(function(tel){
            tel.hide();
        });
        
        this.options = {};
        this.fadeInTransitions = {};
        this.fadeOutTransitions = {};
        
        if(options){
            
            if(options.hasOwnProperty('onChange')){
                this.options.onChange = options.onChange;
            }else{
                this.options.onChange = function(){};
            }
            
            if(options.hasOwnProperty('onChangeTo')){
                this.options.onChangeTo = options.onChangeTo;
            }else{
                this.options.onChangeTo = function(){};
            }
            
            if(options.hasOwnProperty('onShow')){
                this.options.onShow = options.onShow;
            }else{
                this.options.onShow = function(){};
            }
            
            if(options.hasOwnProperty('onHide')){
                this.options.onHide = options.onHide;
            }else{
                this.options.onHide = function(){};
            }
            
        }else{
            this.options.onChange = function(){};
            this.options.onChangeTo = function(){};
            this.options.onShow = function(){};
            this.options.onHide = function(){};
        }
        
        this.currentFocusElementId = null;
        
    },
    
    showElementFromTrigger: function(event){
        this.showElement(event.memo.focusElement);
    }, 
    
    showElement: function(elementId){
        
        if(this.currentFocusElementId == elementId){
            
            // User has clicked same trigger twice - close the display that was opened the first time
            this.setElementInvisible(elementId);
            this.currentFocusElementId = null;
            this.options.onHide($(elementId+'-display'));
            
        }else{
            
            // User has clicked a different trigger - show the corresponding display
            if($(this.currentFocusElementId)){
                this.setElementInvisible(this.currentFocusElementId);
            }else{
                this.options.onShow($(elementId+'-display'));
            }
            
            this.setElementVisible(elementId);
            this.currentFocusElementId = elementId;
            
            this.options.onChangeTo($(elementId+'-display'));
            
        }
        
        this.options.onChange();
        
    },
    
    closeCurrent: function(){
        this.setElementInvisible(this.currentFocusElementId);
        this.currentFocusElementId = null;
    },
    
    setElementVisible: function(elementId){
        if($(elementId) && $(elementId+'-display')){
            // show element
            $(elementId+'-display').fire('vsc:shown');
            // $(elementId+'-display').appear({duration:0.45});
            
            /* if(this.fadeInTransitions.hasOwnProperty(elementId)){
                this.fadeInTransitions[elementId].cancel();
            }
            if(this.fadeOutTransitions.hasOwnProperty(elementId)){
                this.fadeOutTransitions[elementId].cancel();
            } */
            
            this.fadeInTransitions[elementId] = new Effect.Appear(elementId+'-display', {duration:0.45});
            
            $(elementId).addClassName('current');
        }
    },
    
    setElementInvisible: function(elementId){
        if($(elementId) && $(elementId+'-display')){
            // hide element
            $(elementId+'-display').fire('vsc:hidden');
            // $(elementId+'-display').fade({duration:0.45});
            
            /* if(this.fadeInTransitions.hasOwnProperty(elementId)){
                this.fadeInTransitions[elementId].cancel();
            }
            if(this.fadeOutTransitions.hasOwnProperty(elementId)){
                this.fadeOutTransitions[elementId].cancel();
            } */
            
            this.fadeOutTransitions[elementId] = new Effect.Fade(elementId+'-display', {duration:0.45});
            
            $(elementId).removeClassName('current');
        }
    }
    
});