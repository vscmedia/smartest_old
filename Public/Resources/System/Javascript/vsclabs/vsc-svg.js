function destroyShapeElement(element){
    element.raphael.remove();
    delete element;
}

VSC.SVGToolkit = Class.create({

    getRandomBetween: function(min, max){
        return Math.floor(Math.random()*(max-min+1))+min;
    },
    
    getRGBColorString: function(params){
        return "rgb("+params.red+","+params.green+","+params.blue+")";
    },
    
    getHSBColorString: function(params){
        return "hsb("+params.hue/360*100+"%,"+params.saturation+"%,"+params.brightness+"%)";
    }

});

VSC.SVG = Class.create({
    
    initialize: function(canvasDivName, width, height){
        
        // alert(canvasDivName);
        this.paper = Raphael(canvasDivName, width, height);
        this.toolkit = new VSC.SVGToolkit();
        this.width = width;
        this.height = height;
        this.elements = new Array();
        
    },
    
    getElementByName: function(name){
        return this.elements[name];
    },
    
    addCircle: function(name, x, y, r){
        var c = this.paper.circle(x+r, y+r, r);
        c.hide();
        c.attr({"stroke-width": 0, fill: "#fff"});
        c.show();
        if(!this.elements[name]){
            this.elements[name] = new VSCJSArtCircle(name, c);
            return this.elements[name];
        }
    },
    
    addRectangle: function(name, x, y, w, h, r){
        var rect = this.paper.rect(x, y, w, h, r);
        rect.hide();
        rect.attr({"stroke-width": 0, fill: "#fff"});
        rect.show();
        this.elements[name] = new VSC.SVG.Rectangle(name, rect);
        return this.elements[name];
    },
    
    fadeOutAndDestroy: function(name){}
    
});

VSC.SVG.Shape = Class.create({

    initialize: function(name, raphaelShapeObject){
        
        this.name = name;
        this.raphael = raphaelShapeObject;
        this.defaultTransitionTime = 300;
        this.defaultTransition = "linear";
        
    },
    
    setColor: function(color, a){
        if(a){
            this.raphael.animate({fill: color}, this.defaultTransitionTime);
        }else{
            this.raphael.attr({fill: color});
        }
    },
    
    setOpacity: function(newOpacity, a){
        
        var o = newOpacity/100;
        
        if(a){
            this.raphael.animate({"fill-opacity": o}, this.defaultTransitionTime, this.defaultTransition);
        }else{
            this.raphael.attr({"fill-opacity": o});
        }
    },
    
    move: function(newx, newy, duration, transition){
        var t = transition ? transition : "<>";
        var d = duration ? duration : this.defaultTransitionTime;
        this.raphael.animate({x: newx, y: newy}, d);
    },
    
    fadeOutAndDestroy: function(){
        this.setOpacity(0, true);
        var elmnt = this;
        setTimeout(function(){destroyShapeElement(elmnt);}, this.defaultTransitionTime+50);
    },
    
    hide: function(){
        this.raphael.hide();
    },
    
    show: function(){
        this.raphael.show();
    },
    
    destroy: function(){
        this.raphael.remove();
        delete this;
    }

});

VSC.SVG.Circle = Class.create(VSC.SVG.Shape, {
    
    move: function(x, y, duration, transition){
        var t = transition ? transition : this.defaultTransition;
        var d = duration ? duration : 300;
        var newx = x+this.raphael.attr('r');
        var newy = y+this.raphael.attr('r');
    },
    
    setRadius: function(newRadius, a, transition){
        
        if(transition){
            t = transition;
        }else{
            t = this.defaultTransition;
        }
        
        if(a){
            this.raphael.animate({"r": newRadius}, this.defaultTransitionTime, t);
        }else{
            this.raphael.attr({"r": newRadius});
        }
    },
    
    getRadius: function(){
        return this.raphael.attr("attr");
    }
    
});

VSC.SVG.Rectangle = Class.create(VSC.SVG.Shape, {});