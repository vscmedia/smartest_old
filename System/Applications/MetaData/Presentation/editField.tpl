<div id="work-area">
<h3 id="definePageProperty">Define Page Property: {$content.pageproperty_name}</h3>


<form id="defineProperty" name="defineProperty" action="{$domain}{$section}/savePagePropertyValue" method="POST" style="margin:0px">
<input type="hidden" name="page_id" value="{$content.page_id}">
<input type="hidden" name="pageproperty_id" value="{$content.pageproperty_id}">
<!--<input type="hidden" name="page_oldurl" value="{$content.url}">-->

<div id="edit-form-layout">
  <div class="edit-form-row">
    <div class="form-section-label">Property Value:</div>
    <input type="text" name="page_property" value="{$pageproperty_value.pagepropertyvalue_draft_value}" />
  </div>
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" name="action" value="Save" />
      <input type="button" value="Cancel" onclick="cancelForm();" />
    </div>
  </div>
</div>

</form>

</div>