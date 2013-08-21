  <input type="button" value="{$_cancel_message}" onclick="cancelForm();" />
  <input type="submit" value="{$_continue_message}" onclick="$('sm-form-submit-action').value='continue';return true;" />
  <input type="submit" value="{$_quit_message}" onclick="$('sm-form-submit-action').value='quit';return true;" />
  <input type="hidden" name="_submit_action" id="sm-form-submit-action" value="quit" />
  <input type="hidden" name="_referring_action" value="{$_referring_action}" />