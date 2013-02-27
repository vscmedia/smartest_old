<script src="//raw.github.com/DmitryBaranovskiy/raphael/master/raphael-min.js"></script>
<script src="//g.raphaeljs.com/g.raphael.js"></script>
<script src="//g.raphaeljs.com/g.pie.js"></script>

<div id="work-area">
    
    {load_interface file="edit_property_tabs.tpl"}
    
  <h3>Item property values</h3>
  <div class="instruction">{$num_stored_values} total stored values for this property with {$values._count} unique values. Data reuse rate: {$reuse_rate}:1</div>
{*  <ul>
{foreach from=$values item="value" key="i"}
    <li>{$value.value.label}: {$value.count} ({$value.percent}%)</li>
{/foreach}
  </ul> *}
  <div class="raphael-canvas" id="graph" style="width:700px;height:260px">
    
  </div>
  <script type="text/javascript">
    var paper = Raphael("graph");
    paper.piechart(
      125, // pie center x coordinate
      125, // pie center y coordinate
      120,  // pie radius
      [{foreach from=$values item="value" name="graphbuild"}{$value.count}{if !$smarty.foreach.graphbuild.last}, {/if}{/foreach}], // values
      {ldelim}
        legend: [{foreach from=$values item="value" name="graphlegend"}'{$value.value.label} - ({$value.count}; {$value.percent}%)'{if !$smarty.foreach.graphlegend.last}, {/if}{/foreach}]
      {rdelim}
    );
  </script>
  
  <div class="edit-form-row">
      <div class="buttons-bar">
          <input type="button" value="Done" onclick="cancelForm();" />
      </div>
  </div>
  
</div>

<div id="actions-area">
  
</div>