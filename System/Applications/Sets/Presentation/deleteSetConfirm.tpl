<div id="work-area">
    <h3>Delete this data set?</h3>
    <div class="instruction">Are you sure you want to delete the data set "{$set.label}"</div>
    <form action="{$domain}{$section}/deleteSet" method="post">
      <input type="hidden" name="set_id" value="{$set.id}" />
      <div class="edit-form-row">
			<div class="buttons-bar">
			  <input type="button" value="Cancel" onclick="cancelForm();" />
			  <input type="submit" value="Delete" />
			</div>
		</div>
    </form>
</div>

