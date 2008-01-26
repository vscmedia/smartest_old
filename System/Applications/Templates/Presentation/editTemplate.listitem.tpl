<script language="javascript">
{literal}
function cancelForm(){
window.location={$domain}{$section}/getMasterTemplates;
}
{/literal}
</script>


<h3>Edit Template Asset</h3>
<form action="{$domain}{$section}/updateTemplate" method="post" name="newTemplate" enctype="multipart/form-data">
  <input type="hidden" name="template_code" value="{$content.template_code}" />
  <input type="hidden" name="template_name" value="{$content.template_name}" />

  <div style="width:100%" id="editTMPL">
    Template Filename :{$content.template_name}
    <textarea name="template_content" id="tpl_textArea" wrap="virtual" >{$template_content}</textarea>
  </div>
  <input type="submit" value="Save Changes" />
  <input type="button" onclick="cancelForm();" value="Cancel" />
</form>