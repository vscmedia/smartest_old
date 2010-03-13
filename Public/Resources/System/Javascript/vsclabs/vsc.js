var VSC = Class.create({
    
    initialize: function(){
        this.setLocalUrl();
        // alert(window.location.hash);
    },
    
    setLocalUrl: function(url){
    
        if(url){
            this.localUrl = url;
        }else{
            if(window.location.hash){
                var loc = new String(window.location.hash);
                this.localUrl = loc.substr(1);
            }else{
                var loc = new String(window.location);
                this.localUrl = loc.substr(loc.indexOf('#')+1);
            }
        }
    
    }
    
});

/* VSC.Carousel = Class.create({
    
    initialize: function(name, holderDivId, slideDivClass){
        this.name = name;
        this.holderDivId = holderDivId;
        this.slideDivClass = slideDivClass;
        this.vsc = new VSC;
    },
    
    getSlides: function(){
        return $$('#'+this.holderDivId+' .'+this.slideDivClass);
    },
    
    hasSlideWithName: function(name){
        for(var s in this.getSlides()){
            if(s.id == name){
                return true;
            }
        }
        return false;
    },
    
    goTo: function(slideHash){
        if(this.hasSlideWithName(slideHash)){
            // move slider to correct position
        }
    }
    
}); */

/*var Animal = Class.create({
    
  initialize: function(name, sound) {
    this.name  = name;
    this.sound = sound;
  },

  speak: function() {
    alert(this.name + " says: " + this.sound + "!");
  }
});

// subclassing Animal
var Snake = Class.create(Animal, {
  initialize: function($super, name) {
    $super(name, 'hissssssssss');
  }
});

var ringneck = new Snake("Ringneck");
ringneck.speak();
//-> alerts "Ringneck says: hissssssssss!" */