String.prototype.fragment = function (vertical){
    
    var v = vertical ? true : false;
    
    if(v){
        var s = this.replace(/(.)/g, "<span style=\"display:none\" class=\"appearing-letter\">$1</span><br />");
    }else{
        var s = this.replace(/(.)/g, "<span style=\"display:none\" class=\"appearing-letter\">$1</span>");
    }
    
    return s;
}

VSC.Effect = {};

var instances = new Array;

VSC.Effect.DisplayProgressively = Class.create({
    
    initialize: function(descriptor, pid, params){
        
        // this.finalElements;
        this.finalElementsIndex = 0;
        this.processId = pid;

        // this.finalElements = new Array;
        this.finalElements = $$(descriptor);
        
        // do we want to display them vertically, like japanese?
        this.vertical = params && params.vertical ? true : false;
        this.letters = params && params.individualizeLetters ? true : false;
        this.transition = params && params.transition ? params.transition : Effect.Transitions.linear;
        this.interval = params && params.interval ? params.interval : 300;
        this.duration = params && params.duration ? params.duration : 1.0;
        
        if(params && params.effect && (params.effect == 'appear' || params.effect == 'grow' || params.effect == 'blindDown')){
            this.effect = params.effect;
        }else{
            this.effect = 'appear';
        }
        
        var myID = this.id;
        instances[myID] = this;
        this.executer = setInterval(function(){instances[myID].showNextElement()}, this.interval);
        
    },
    
    cancel: function(){
        clearInterval(this.executer);
    },
    
    showNextElement: function(){
        
        if(this.finalElementsIndex < this.finalElements.length){

            var el_id;
            
            // if the element doesn't have an id, give it one
            if(!this.finalElements[this.finalElementsIndex].id){
                this.finalElements[this.finalElementsIndex].id = this.processId+'-element-'+this.finalElementsIndex;
                el_id = this.processId+'-element-'+this.finalElementsIndex;
            }else{
                el_id = this.finalElements[this.finalElementsIndex].id;
            }
            
            if(this.finalElements[this.finalElementsIndex].innerHTML.stripTags().length && this.letters){
                new VSC.Effect.DisplayLettersProgressively(finalElements[finalElementsIndex], this.vertical);
            }else{
                if(this.effect == 'grow'){
                    new Effect.Grow(el_id, {duration:this.duration, transition:this.transition});
                }else if(this.effect == 'blindDown'){
                    
                }else{
                    new Effect.Appear(el_id, {duration:this.duration, transition:this.transition});
                }
            }

            this.finalElementsIndex++;
            
        }else{
            
            clearInterval(this.executer);
            
        }

    }
    
});

VSC.Effect.DisplayLettersProgressively = Class.create({
    
    initialize: function(element, vertical, params){
    
        // capture the original string
        this.originalContents = element.innerHTML;
        
        this.interval = params && params.interval ? params.interval : 0.08;
        this.duration = params && params.duration ? params.duration : 0.5;
        
        this.element = element;
    
        // fill the element with invisible spans
        this.element.innerHTML = this.originalContents.stripTags().fragment(vertical);
    
        // make the actual element visible
        this.element.style.display = 'block';
    
        var selector = '#'+this.element.id+' span.appearing-letter';
    
        var spans = $$(selector);
        var spanIndex = 0;
    
        new PeriodicalExecuter(function(){
        
            if(spanIndex < spans.length){
                spans[spanIndex].id = element.id+'-letter-'+spanIndex;
                new Effect.Appear(spans[spanIndex].id, {duration: 0.4});
                spanIndex++;
            }
        
        }, this.interval);
    
    }
    
});