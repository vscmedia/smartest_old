<div id="work-area">
    
  {load_interface file="edit_filegroup_tabs.tpl"}
    
  <h3>Arrange file gallery contents</h3>
  
  <ul id="sortable-gallery-members" style="margin:0px;padding:0px;list-style-type:none">
  
{foreach from=$assets item="membership"}

    <li style="padding:10px;border:1px solid #ccc;border-radius:6px;margin-bottom:8px;background-color:#fff;cursor:move;position:relative" id="membership_{$membership.file.id}">
      
      <a href="#remove" style="position:absolute;top:10px;right:10px" id="membership-remove-{$membership.id}"><img src="{$domain}Resources/Icons/cross.png" alt="" /></a>
      
      <p style="clear:both;margin:0 0 10px 0"><strong>File: {$membership.file.label}</strong> ({$membership.file.url}) <a href="{$domain}assets/editAsset?asset_id={$membership.file.id}"><img src="{$domain}Resources/Icons/pencil.png" alt="" /></a> <a href="{$domain}assets/assetInfo?asset_id={$membership.file.id}"><img src="{$domain}Resources/Icons/information.png" alt="" /></a></p>
  
      <div class="asset-gallery-membership-file" style="float:left;width:150px">
        {if $membership.file.is_image}
          {$membership.file.image.constrain_150x150}
        {else}
          {$membership.file}
        {/if}
      </div>
  
      <div class="asset-gallery-membership-file" style="float:right;width:400px">
        
        <div class="editable" id="membership-caption-{$membership.id}">{$membership.caption}</div>
        
        <script type="text/javascript">
        
          new Ajax.InPlaceEditor('membership-caption-{$membership.id}', sm_domain+'ajax:assets/updateAssetGroupMembershipCaption', {ldelim}
            callback: function(form, value) {ldelim}
              return 'membership_id={$membership.id}&new_caption='+encodeURIComponent(value);
            {rdelim},
            highlightColor: '#ffffff',
            hoverClassName: 'editable-hover',
            savingClassName: 'editable-saving',
            rows: 4,
            cols: 50
            
          {rdelim});
          
          $('membership-remove-{$membership.id}').observe('click', function(event){ldelim}
            
            new Ajax.Request( sm_domain+'ajax:assets/deleteAssetGroupMembership', {ldelim}
              
              onSuccess: function(response) {ldelim}
                new Effect.Fade('membership_{$membership.file.id}');
              {rdelim},
              
              parameters: {ldelim}
                membership_id: {$membership.id}
              {rdelim}
              
            {rdelim});
            
            event.stop();
            
          {rdelim});
          
        </script>
      
      </div>
  
      <div class="breaker"></div>
  
    </li>

{/foreach}

  </ul>

<script type="text/javascript" src="/Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
<script type="text/javascript">

var url = sm_domain+'ajax:assets/updateGalleryOrder';
var groupId = {$group.id};
{literal}
var IDs;
var IDs_string;

var itemsList = Sortable.create('sortable-gallery-members', {
      
      onUpdate: function(){
          
        IDs = Sortable.sequence('sortable-gallery-members');
        IDs_string = IDs.join(',');
        
        new Ajax.Request(url, {
          method: 'get',
          parameters: {group_id: groupId, new_order: IDs_string},
          onSuccess: function(transport) {
            
          }
        });
      },
      
//      handles:$$('#sortable-gallery-members li p a.handle'),
      
      constraint: false,
      scroll: window,
      scrollSensitivity: 35
      
  });
{/literal}
</script>
  
</div>

<div id="actions-area">
  
  {if $request_parameters.item_id && $request_parameters.from}
  <ul class="actions-list">
    <li><b>Workflow options</b></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}datamanager/editItem?item_id={$request_parameters.item_id}'"><img border="0" src="{$domain}Resources/Icons/tick.png"> Return to editing item</a></li>
  </ul>
  {/if}
  
</div>