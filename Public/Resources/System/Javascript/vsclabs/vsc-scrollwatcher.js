// Fires a custom event every time the document is scrolled. It does so by 
// checking for a change at a set interval (0.05 seconds by default).
// Also passes the current scrollTop and delta (change) in the memo object to make things
// easier for event handlers.

// Thanks to Mike Ross for the getScrollTop function http://www.themikeross.com/scrolltop-javascript-for-all-browsers

(function() {
    
    var ScrollWatcher = {
      
        CURRENT: 0,
        INTERVAL: 0.05,
    
        checkScrollTop: function(pe) {
            var st = ScrollWatcher.getScrollTop();
            if (ScrollWatcher.CURRENT != st) {
                document.fire('scrolled:vertically', { currentScrollTop: st, delta: (st-ScrollWatcher.CURRENT) });
                ScrollWatcher.CURRENT = st;
            }
        },
    
        getScrollTop: function documentScrollTop() {
            return (document.documentElement.scrollTop + document.body.scrollTop
            == document.documentElement.scrollTop) ?
            document.documentElement.scrollTop : document.body.scrollTop;
        }
    
    };
  
    new PeriodicalExecuter(ScrollWatcher.checkScrollTop, ScrollWatcher.INTERVAL);
  
})();