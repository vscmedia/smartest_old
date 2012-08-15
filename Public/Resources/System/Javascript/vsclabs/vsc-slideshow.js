// Written by Marcus Gilroy-Ware
// © VSC Creative Ltd. 2012

VSC.Slideshow = Class.create({
    
    initialize: function(holderId, options){
        this.options = {};
        this.name = 'slideshow';
        this.options.holderId = holderId;
        this.options.frequency = options.frequency ? options.frequency : 4;
        this.goToSlide($$('#'+holderId+' .slides .slide').first().id);
        this.currentPosition = 0;
        var IDs = [];
        $$('#'+holderId+' .slides .slide').each(function(s){
            IDs.push(s.id);
        });
        this.IDs = IDs;
        
        if(options.hasOwnProperty('autostart')){
            this.options.autostart = options.autostart;
        }else{
            this.options.autostart = false;
        }
        
        if(this.options.autostart){
            this.startAutoAdvance();
        }
        
        // this.startAutoAdvance();
        // alert(holderId);
    },
    
    goToSlide: function(slideId){
        if(this.currentSlideId != slideId){
            if($$('#'+this.options.holderId+' .slides #'+slideId).size() > 0){
                
                if(this.currentSlideId){
                    // $$('#'+this.options.holderId+' .slides #'+this.currentSlideId)[0].fade({duration:0.28});
                    $$('#'+this.options.holderId+' .slides #'+this.currentSlideId)[0].fade({duration:0.7, transition: Effect.Transitions.sinoidal});
                    // $$('#'+this.options.holderId+' .slides #'+slideId)[0].appear({delay:0.3, duration:0.6});
                    $$('#'+this.options.holderId+' .slides #'+slideId)[0].appear({duration:0.7, transition: Effect.Transitions.sinoidal});
                    this.updateNav(slideId);
                }else{
                    $$('#'+this.options.holderId+' .slides #'+slideId)[0].appear({duration:0.6});
                }
                
                this.currentSlideId = slideId;
            }
        }   
    },
    
    goToSlideByPosition: function(pos){
        if(this.IDs[pos]){
            this.goToSlide(this.IDs[pos]);
            this.currentPosition = pos;
        }
    },
    
    updateNav: function(slideId){
        if($('#'+this.options.holderId+' .slides-nav')){
            $$('#'+this.options.holderId+' .slides-nav li').each(function(b){
                b.removeClassName('current');
            });
            $$('#'+this.options.holderId+' .slides-nav li')[this.getSlideIndexFromId(slideId)].addClassName('current');
        }
    },
    
    getSlideIndexFromId: function(slideId){
        var rightIndex = -1;
        
        $A(this.IDs).each(function(sid, index){
            if(sid == slideId){
                rightIndex = index;
            }
        });
        
        return rightIndex;
    },
    
    nextSlide: function(){
        var nextSlidePos = this.currentPosition +1;
        if(nextSlidePos >= this.IDs.length){
            nextSlidePos = 0;
        }
        this.goToSlideByPosition(nextSlidePos);
    },
    
    getAutoAdvanceFrequency: function(){
        return this.options.frequency;
    },
    
    startAutoAdvance: function(){
        // periodicalexecuter on nextslide
        var ns = this.nextSlide.bind(this);
        var freq = this.getAutoAdvanceFrequency.bind(this);
        this.heartbeat = new PeriodicalExecuter(ns, freq());
    },
    
    goToSlideFromClick: function(slideId){
        // stop periodical executer if it is running
        this.pause();
        // go to the requested slide
        this.goToSlide(slideId);
        // set a new timeout on starting the periodicalexecuter
        if(this.options.autostart){
            this.startAutoAdvance();
        }
    },
    
    goToSlideFromClickByPosition: function(pos){
        this.pause();
        this.goToSlideByPosition(pos);
        if(this.options.autostart){
            this.startAutoAdvance();
        }
    },
    
    goToNextSlideFromClick: function(){
        // stop periodical executer if it is running
        this.pause();
        // go to the requested slide
        this.nextSlide();
        // set a new timeout on starting the periodicalexecuter
        if(this.options.autostart){
            this.startAutoAdvance();
        }
    },
    
    pause: function(){
        if(this.hasOwnProperty('heartbeat')){
            this.heartbeat.stop();
        }
    }
    
});