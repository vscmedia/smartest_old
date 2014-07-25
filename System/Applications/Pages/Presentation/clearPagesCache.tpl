<script language="javascript">
  
  var toggle = 0;
  
  {literal}
  function toggleCacheResultDetails(){
    
    if(toggle){
      // the details are showing - hide them
      toggle = 0;
      new Effect.BlindUp('cache-details', {duration:0.7});
      $('show-details-link').innerHTML = 'Show Details';
    }else{
      //the details are hidden - show them
      toggle = 1;
      new Effect.BlindDown('cache-details', {duration:0.7});
      $('show-details-link').innerHTML = 'Hide Details';
    }
    
  }
  {/literal}
  
</script>

<div id="work-area">
  
  <h3>Clear pages cache</h3>
  
  <div class="instruction">
    
    To make Smartest even faster for your site visitors, some web pages are saved to a cache so that they can be sent instantly, without any pages needing to be built again from your templates.
    
  </div>
  
  <input type="button" id="clear-cache-button" value="Clear the cache" />
  <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="loader" style="display:none" />
  <script type="text/javascript">
    
    var url = '{$domain}ajax:{$section}/clearPagesCache';
{literal}

    $('clear-cache-button').observe('click', function(){
        
        $('loader').show();
        $('clear-cache-button').disabled = true;
        
        new Ajax.Updater('cache-details', url, {onComplete: function(){
            
            new Effect.Morph('cache-details', {style: 'height:300px'});
            $('loader').hide();
            
        }});
        
    });

{/literal}
    
  </script>
  
  <div id="cache-details" style="height:1px;border:1px solid #ccc;padding:5px;margin-top:20px;overflow-y:scroll"></div>
  
</div>

<div id="actions-area">
    
  <ul class="actions-list" id="non-specific-actions">
    
    <li class="permanent-action"><a href="#" onclick="cancelForm();" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Done</a></li>
    
  </ul>
    
</div>