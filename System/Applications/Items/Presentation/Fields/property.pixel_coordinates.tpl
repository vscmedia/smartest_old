x: <input type="text" name="item[{$property.id}][x]" value="{$value.x}" style="width:40px" />
y: <input type="text" name="item[{$property.id}][y]" value="{$value.y}" style="width:40px" />
orient from:
<select name="item[{$property.id}][orient]" style="width:160px">
  <option value="TL"{if $value.orient == 'TL'} selected="selected"{/if}>Top left</option>
  <option value="BL"{if $value.orient == 'BL'} selected="selected"{/if}>Bottom left</option>
  <option value="TR"{if $value.orient == 'TR'} selected="selected"{/if}>Top right</option>
  <option value="BR"{if $value.orient == 'BR'} selected="selected"{/if}>Bottom right</option>
</select>