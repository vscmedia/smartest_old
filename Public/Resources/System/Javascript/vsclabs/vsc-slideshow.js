// Written by Marcus Gilroy-Ware
// © VSC Creative Ltd. 2011

VSC.Slideshow = Class.create({
    
    initialize: function(holderId){
        this.options = {};
        this.name = 'slideshow';
        this.options.holderId = holderId;
        this.goToSlide($$('#'+holderId+' #slides .slide').first().id);
        this.currentPosition = 0;
        var IDs = [];
        $$('#'+holderId+' #slides .slide').each(function(s){
            IDs.push(s.id);
        });
        this.IDs = IDs;
        this.startAutoAdvance();
    },
    
    goToSlide: function(slideId){
        if(this.currentSlideId != slideId){
            if($$('#'+this.options.holderId+' #slides #'+slideId).size() > 0){
                
                if(this.currentSlideId){
                    $$('#'+this.options.holderId+' #slides #'+this.currentSlideId)[0].fade({duration:0.28});
                    $$('#'+this.options.holderId+' #slides #'+slideId)[0].appear({delay:0.3, duration:0.6});
                    this.updateNav(slideId);
                }else{
                    $$('#'+this.options.holderId+' #slides #'+slideId)[0].appear({duration:0.6});
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
        
        $$('#'+this.options.holderId+' #slides-nav li').each(function(b){
            b.removeClassName('current');
        });
        $$('#'+this.options.holderId+' #slides-nav li')[this.getSlideIndexFromId(slideId)].addClassName('current');
        
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
    
    startAutoAdvance: function(){
        // periodicalexecuter on nextslide
        var ns = this.nextSlide.bind(this);
        this.heartbeat = new PeriodicalExecuter(ns, 4);
    },
    
    goToSlideFromClick: function(slideId){
        // stop periodical executer if it is running
        this.heartbeat.stop();
        // go to the requested slide
        this.goToSlide(slideId);
        // set a new timeout on starting the periodicalexecuter
        this.startAutoAdvance();
    },
    
    goToSlideFromClickByPosition: function(pos){
        this.heartbeat.stop();
        this.goToSlideByPosition(pos);
        this.startAutoAdvance();
    }
    
});