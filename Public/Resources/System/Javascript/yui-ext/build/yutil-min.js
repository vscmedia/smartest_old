/*
 * YUI Extensions 0.33 RC2
 * Copyright(c) 2006, Jack Slocum.
 */


YAHOO.namespace('ext');YAHOO.namespace('ext.util');YAHOO.namespace('ext.grid');YAHOO.ext.Strict=(document.compatMode=='CSS1Compat');YAHOO.ext.SSL_SECURE_URL='javascript:false';window.undefined=undefined;Function.prototype.createCallback=function(){var args=arguments;var method=this;return function(){return method.apply(window,args);};};Function.prototype.createDelegate=function(obj,args,appendArgs){var method=this;return function(){var callargs=args||arguments;if(appendArgs===true){callArgs=Array.prototype.slice.call(arguments,0);callargs=callArgs.concat(args);}else if(typeof appendArgs=='number'){callargs=Array.prototype.slice.call(arguments,0);var applyArgs=[appendArgs,0].concat(args);Array.prototype.splice.apply(callargs,applyArgs);}
return method.apply(obj||window,callargs);};};Function.prototype.defer=function(millis,obj,args,appendArgs){return setTimeout(this.createDelegate(obj,args,appendArgs),millis);};Function.prototype.createSequence=function(fcn,scope){if(typeof fcn!='function'){return this;}
var method=this;return function(){var retval=method.apply(this||window,arguments);fcn.apply(scope||this||window,arguments);return retval;};};Function.prototype.createInterceptor=function(fcn,scope){if(typeof fcn!='function'){return this;}
var method=this;return function(){fcn.target=this;fcn.method=method;if(fcn.apply(scope||this||window,arguments)===false){return;}
return method.apply(this||window,arguments);;};};YAHOO.ext.util.Browser=new function(){var ua=navigator.userAgent.toLowerCase();this.isOpera=(ua.indexOf('opera')>-1);this.isSafari=(ua.indexOf('webkit')>-1);this.isIE=(window.ActiveXObject);this.isIE7=(ua.indexOf('msie 7')>-1);this.isGecko=!this.isSafari&&(ua.indexOf('gecko')>-1);if(ua.indexOf("windows")!=-1||ua.indexOf("win32")!=-1){this.isWindows=true;}else if(ua.indexOf("macintosh")!=-1){this.isMac=true;}}();YAHOO.print=function(arg1,arg2,etc){if(!YAHOO.ext._console){var cs=YAHOO.ext.DomHelper.insertBefore(document.body.firstChild,{tag:'div',style:'width:250px;height:350px;overflow:auto;border:3px solid #c3daf9;'+'background:white;position:absolute;right:5px;top:5px;'+'font:normal 8pt arial,verdana,helvetica;z-index:50000;padding:5px;'},true);new YAHOO.ext.Resizable(cs,{transparent:true,handles:'all',pinned:true,adjustments:[0,0],wrap:true,draggable:(YAHOO.util.DD?true:false)});cs.on('dblclick',cs.hide);YAHOO.ext._console=cs;}
var msg='';for(var i=0,len=arguments.length;i<len;i++){msg+=arguments[i]+'<hr noshade style="color:#eeeeee;" size="1">';}
YAHOO.ext._console.dom.innerHTML=msg+YAHOO.ext._console.dom.innerHTML;YAHOO.ext._console.dom.scrollTop=0;YAHOO.ext._console.show();};YAHOO.printf=function(format,arg1,arg2,etc){var args=Array.prototype.slice.call(arguments,1);YAHOO.print(format.replace(/\{\{[^{}]*\}\}|\{(\d+)(,\s*([\w.]+))?\}/g,function(m,a1,a2,a3){if(m.chatAt=='{'){return m.slice(1,-1);}
var rpl=args[a1];if(a3){var f=eval(a3);rpl=f(rpl);}
return rpl?rpl:'';}));}
YAHOO.util.CustomEvent.prototype.fireDirect=function(){var len=this.subscribers.length;for(var i=0;i<len;++i){var s=this.subscribers[i];if(s){var scope=(s.override)?s.obj:this.scope;if(s.fn.apply(scope,arguments)===false){return false;}}}
return true;};YAHOO.extendX=function(subclass,superclass,overrides){YAHOO.extend(subclass,superclass);subclass.override=function(o){YAHOO.override(subclass,o);};if(!subclass.prototype.override){subclass.prototype.override=function(o){for(var method in o){this[method]=o[method];}};}
if(overrides){subclass.override(overrides);}};YAHOO.override=function(origclass,overrides){if(overrides){var p=origclass.prototype;for(var method in overrides){p[method]=overrides[method];}}};YAHOO.ext.util.DelayedTask=function(fn,scope,args){var timeoutId=null;this.delay=function(delay,newFn,newScope,newArgs){if(timeoutId){clearTimeout(timeoutId);}
fn=newFn||fn;scope=newScope||scope;args=newArgs||args;timeoutId=setTimeout(fn.createDelegate(scope,args),delay);};this.cancel=function(){if(timeoutId){clearTimeout(timeoutId);timeoutId=null;}};};YAHOO.ext.util.Observable=function(){};YAHOO.ext.util.Observable.prototype={fireEvent:function(){var ce=this.events[arguments[0].toLowerCase()];return ce.fireDirect.apply(ce,Array.prototype.slice.call(arguments,1));},addListener:function(eventName,fn,scope,override){eventName=eventName.toLowerCase();if(!this.events[eventName]){throw'You are trying to listen for an event that does not exist: "'+eventName+'".';}
this.events[eventName].subscribe(fn,scope,override);},delayedListener:function(eventName,fn,scope,delay){var newFn=function(){setTimeout(fn.createDelegate(scope,arguments),delay||1);}
this.addListener(eventName,newFn);return newFn;},removeListener:function(eventName,fn,scope){this.events[eventName.toLowerCase()].unsubscribe(fn,scope);},purgeListeners:function(){for(var evt in this.events){if(typeof this.events[evt]!='function'){this.events[evt].unsubscribeAll();}}}};YAHOO.ext.util.Observable.prototype.on=YAHOO.ext.util.Observable.prototype.addListener;YAHOO.ext.util.Config={apply:function(obj,config,defaults){if(defaults){this.apply(obj,defaults);}
if(config){for(var prop in config){obj[prop]=config[prop];}}
return obj;}};if(!String.escape){String.escape=function(string){return string.replace(/('|\\)/g,"\\$1");};};String.leftPad=function(val,size,ch){var result=new String(val);if(ch==null){ch=" ";}
while(result.length<size){result=ch+result;}
return result;};if(YAHOO.util.Connect){YAHOO.util.Connect.setHeader=function(o){for(var prop in this._http_header){if(typeof this._http_header[prop]!='function'){o.conn.setRequestHeader(prop,this._http_header[prop]);}}
delete this._http_header;this._http_header={};this._has_http_headers=false;};}
if(YAHOO.util.DragDrop){YAHOO.util.DragDrop.prototype.defaultPadding={left:0,right:0,top:0,bottom:0};YAHOO.util.DragDrop.prototype.constrainTo=function(constrainTo,pad,inContent){if(typeof pad=='number'){pad={left:pad,right:pad,top:pad,bottom:pad};}
pad=pad||this.defaultPadding;var b=getEl(this.getEl()).getBox();var ce=getEl(constrainTo);var c=ce.dom==document.body?{x:0,y:0,width:YAHOO.util.Dom.getViewportWidth(),height:YAHOO.util.Dom.getViewportHeight()}:ce.getBox(inContent||false);this.resetConstraints();this.setXConstraint(b.x-c.x-(pad.left||0),c.width-b.x-b.width-(pad.right||0));this.setYConstraint(b.y-c.y-(pad.top||0),c.height-b.y-b.height-(pad.bottom||0));}}