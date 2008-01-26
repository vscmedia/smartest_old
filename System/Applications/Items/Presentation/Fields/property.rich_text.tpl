<div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
<div>
  <textarea name="item[{$property.id}]" class="itemproperty_textinput">{$value}</textarea>
</div>