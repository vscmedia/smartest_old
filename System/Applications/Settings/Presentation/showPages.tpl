<h3>Pages created by {$content.username} </h3>
<a name="top"></a>
<div id="work-area">

<div id="options-view-chooser">
View: <a href="#" onclick="setView('list', 'options_grid')">List</a> /
<a href="#" onclick="setView('grid', 'options_grid')">Icon</a>
</div>
{if $content.pageCount!=0}
<ul class="options-grid" id="options_grid">
{foreach from=$pages key=key item=page}
  <li >
    <a class="option" href="#" >
      <img border="0" src="{$domain}Resources/Icons/page_code.png">
      {$page.page_name}</a>
	</li>
{/foreach}

{else}<font color="Red">This User hasn't created any page!</font>{/if}
</ul>
</div>






