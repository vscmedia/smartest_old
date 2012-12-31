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
    },
    
    destroyShapeElement: function(element){
        element.raphael.remove();
        delete element;
    }

});

VSC.SVG = Class.create({
    
    initialize: function(canvasDivName, width, height, varName){
        
        // alert(canvasDivName);
        this.paper = Raphael(canvasDivName, width, height);
        this.toolkit = new VSC.SVGToolkit();
        this.width = width;
        this.height = height;
        this.elements = new Array();
        if(varName){
            this.varName = varName;
        }
        
    },
    
    getElementByName: function(name){
        return this.elements[name];
    },
    
    webkit: function(){
        
        this.paper.safari();
        
    },
    
    addCircle: function(name, x, y, r, useCentre){
        if(useCentre){
            var c = this.paper.circle(x, y, r);
        }else{
            var c = this.paper.circle(x+r, y+r, r);
        }
        c.hide();
        c.attr({"stroke-width": 0, fill: "#fff"});
        c.show();
        if(!this.elements[name]){
            this.elements[name] = new VSC.SVG.Circle(name, c, 'Circle');
            this.elements[name].vscCanvasVarName = this.varName;
            return this.elements[name];
        }
    },
    
    addRectangle: function(name, x, y, w, h, r){
        var rect = this.paper.rect(x, y, w, h, r);
        rect.hide();
        rect.attr({"stroke-width": 0, fill: "#fff"});
        rect.show();
        this.elements[name] = new VSC.SVG.Rectangle(name, rect, 'Rectangle');
        this.elements[name].vscCanvasVarName = this.varName;
        return this.elements[name];
    },
    
    addText: function(content, name, x, y){
        
        var text = this.paper.text(x, y, content);
        text.hide();
        this.elements[name] = new VSC.SVG.Text(name, text, 'Text');
        this.elements[name].vscCanvasVarName = this.varName;
        return this.elements[name];
        
    },
    
    fadeOutAndDestroy: function(name){},
    
    path: function(name, data){
        this.elements[name] = this.paper.path(data);
        this.elements[name][0].id = name;
        return this.elements[name];
    },
    
    addLine: function(name, x1, y1, x2, y2){
        var d = "M"+x1+','+y1+'L'+x2+','+y2;
        return this.path(name, d);
    },
    
    getRawElement: function(name){
        return this.elements[name][0];
    }
    
});

VSC.SVG.Shape = Class.create({

    initialize: function(name, raphaelShapeObject, shapeName){
        
        this.name = name;
        this.className = shapeName ? shapeName : 'Shape';
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
        setTimeout(function(){VSC.SVGToolkit.destroyShapeElement(elmnt);}, this.defaultTransitionTime+50);
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
    },
    
    attr: function(args){
        return this.raphael.attr(args);
    },
    
    animate: function(args, ms, easing){
        return this.raphael.animate(args, ms, easing);
    },
    
    addLabel: function(text, position, textAttributes, visibleAtStart){
        // alert(this.vscCanvasVarName);
        /* var v = this.vscCanvasVarName;
        var c = window['LS.VARS']; */
        // alert(c);
        // this.label = new VSC.SVG.Text(this.name+'-label');
    },
    
    hideLabel: function(animate){
        
    },
    
    showLabel: function(animate){
        
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

VSC.SVG.Text = Class.create(VSC.SVG.Shape, {});

VSC.SVG.Rectangle = Class.create(VSC.SVG.Shape, {});