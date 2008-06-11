  <h3>Create a New Page</h3>
  
  <div class="instruction">Step 1: Choose which type of page you're going to make</div>
  <form action="{$domain}{$section}/addPage" method="post">
    
    <input type="hidden" name="page_parent" value="{$page_parent}" />
    <input type="hidden" name="stage" value="2">
    
    <select name="page_type">
      <option value="NORMAL" selected="selected">Regular Web-page</option>
      <option value="ITEMCLASS">Object Meta-page</option>
      {* <option value="TAG">Tag list-page</option> *}
    </select>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Next &gt;&gt;" />
      </div>
    </div>
    
  </form>