<script type="text/javascript">

var propertyId = '{$property.webid}';
var ajaxUrl = '{$domain}ajax:datamanager/regularizeItemClassProperty';

{literal}

  document.observe('dom:loaded', function(){
    $('regularize-start-button').observe('click', function(){
      $('updater').update('<img src="../Resources/System/Images/ajax-loader.gif" alt="" /> Please wait...');
      new Ajax.Updater('updater', ajaxUrl, {
        parameters: {property_id: propertyId}
      });
    });
  });
  
{/literal}
</script>

<div id="work-area">
  <h3>Regularize stored values: {$property.name}</h3>
{if $allow}
  <p>This is for advanced users only. If you don't know what regularizing stored properties is, better ask your administrator. If this is your site and you'd like to learn more, {help id="datamanager:regularizing"}click here{/help}.</p>
{if $num_values > 250}
  <div class="warning">
    <p><strong>Please note</strong></p>
    <p>There are {$num_values} stored values for this property.</p>
    <p>As this is quite a lot, we'd recommend regularizing at times of low traffic. This is usually during the small hours of the morning, Monday-Friday</p>
    <p>Only values for items on this site will be regularized.</p>
  </div>
{else}
  <p>There are {$num_values} stored values for this property on this website. Note that only values for items on this site will be regularized.</p>
{/if}

<div class="special-box">
  <input type="hidden" name="property_id" value="{$property.id}" />
  <div id="updater">
    <input type="button" value="Start..." id="regularize-start-button" />
  </div>
</div>

{else}

<p>As the formatting of this property type has undergone no changes, no regularization is needed at this time.</p>

{/if}

</div>

<div id="actions-area">
  <ul class="actions-list">
    <li class="permanent-action"><a href="{$domain}datamanager/getItemClassProperties?class_id={$property.itemclass_id}">Back to Properties</a></li>
  </ul>
</div>