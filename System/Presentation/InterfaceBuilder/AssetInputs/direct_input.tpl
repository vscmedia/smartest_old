{if $input_panel_set}
{load_interface file=$input_panel}
{else}
<div class="error">This file type needs to specify an input panel before you can directly input it.</div>
{/if}