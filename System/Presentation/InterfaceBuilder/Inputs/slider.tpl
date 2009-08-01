{if $_include_slider_js}<script language="javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/slider.js"></script>{/if}

<div id="track_{$_slider_input_data.name}" style="width:300px; background-color:#ccc; height:10px;">
	<div id="handle_{$_slider_input_data.name}" style="width:10px; height:15px; background-color:#f60; cursor:move;"></div>
</div>

<p id="value_{$_slider_input_data.name}">{$_slider_input_data.value}{$_slider_input_data.value_unit}</p>

<input type="hidden" id="{$_slider_input_data.name}" name="{$_slider_input_data.name}" value="{$_slider_input_data.value}" />

<script type="text/javascript" language="javascript">
// <![CDATA[
		
var slider_{$_slider_input_data.name} = {literal}new Control.Slider('handle_{/literal}{$_slider_input_data.name}', 'track_{$_slider_input_data.name}', {literal}{
	onSlide: function(v) { $('value_{/literal}{$_slider_input_data.name}').innerHTML = Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum})) + '{$_slider_input_data.value_unit}'; $('{$_slider_input_data.name}').value = Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum})); {literal}},
	onChange: function(v) { $('value_{/literal}{$_slider_input_data.name}').innerHTML = Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum})) + '{$_slider_input_data.value_unit}'; $('{$_slider_input_data.name}').value = Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum})); {literal}}
});{/literal}

slider_{$_slider_input_data.name}.setValue({$_slider_input_data.js_value});

// ]]>
</script>