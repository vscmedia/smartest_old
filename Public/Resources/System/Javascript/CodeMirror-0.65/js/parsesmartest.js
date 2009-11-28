var SmartestParser = Editor.Parser = (function(){
  if (!XMLParser)
    throw new Error("XML parser must be loaded for Smartest mode to work.");
    
  XMLParser.configure({useHTMLKludges: true});
  
  function parseSmartest(stream){
    
    var htmlParser = XMLParser.make(stream), localParser = null, inTag = false;
    // var iter = {next: top};
    
    function top() {
      
      var token = htmlParser.next();
      alert (token.content);
      
    }
    
  }
  
  return {make: parseSmartest, electricChars: ":?>"};
  
})();