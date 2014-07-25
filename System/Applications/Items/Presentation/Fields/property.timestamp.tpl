<div style="float:left;margin-right:10px">
  <div style="float:left;margin-right:10px">
    Time<br />
    <select name="item[{$property.id}][h]" style="width:60px">
    	<option value="00"{if $value.H == "00"} selected="selected"{/if}>00</option>
    	<option value="01"{if $value.H == "01"} selected="selected"{/if}>01</option>
    	<option value="02"{if $value.H == "02"} selected="selected"{/if}>02</option>
    	<option value="03"{if $value.H == "03"} selected="selected"{/if}>03</option>
    	<option value="04"{if $value.H == "04"} selected="selected"{/if}>04</option>
    	<option value="05"{if $value.H == "05"} selected="selected"{/if}>05</option>
    	<option value="06"{if $value.H == "06"} selected="selected"{/if}>06</option>
    	<option value="07"{if $value.H == "07"} selected="selected"{/if}>07</option>
    	<option value="08"{if $value.H == "08"} selected="selected"{/if}>08</option>
    	<option value="09"{if $value.H == "09"} selected="selected"{/if}>09</option>
    	<option value="10"{if $value.H == "10"} selected="selected"{/if}>10</option>
    	<option value="11"{if $value.H == "11"} selected="selected"{/if}>11</option>
    	<option value="12"{if $value.H == "12"} selected="selected"{/if}>12</option>
    	<option value="13"{if $value.H == "13"} selected="selected"{/if}>13</option>
    	<option value="14"{if $value.H == "14"} selected="selected"{/if}>14</option>
    	<option value="15"{if $value.H == "15"} selected="selected"{/if}>15</option>
    	<option value="16"{if $value.H == "16"} selected="selected"{/if}>16</option>
    	<option value="17"{if $value.H == "17"} selected="selected"{/if}>17</option>
    	<option value="18"{if $value.H == "18"} selected="selected"{/if}>18</option>
    	<option value="19"{if $value.H == "19"} selected="selected"{/if}>19</option>
    	<option value="20"{if $value.H == "20"} selected="selected"{/if}>20</option>
    	<option value="21"{if $value.H == "21"} selected="selected"{/if}>21</option>
    	<option value="22"{if $value.H == "22"} selected="selected"{/if}>22</option>
    	<option value="23"{if $value.H == "23"} selected="selected"{/if}>23</option>
    </select>
  </div>
  <div style="float:left;margin-right:10px">
    &nbsp;<br />
    :&nbsp;<input type="text" name="item[{$property.id}][i]" maxlength="2" value="{if $value.i}{$value.i}{else}00{/if}" style="width:45px" id="item-property-{$property.id}-minutes" />
    <script type="text/javascript">
      $('item-property-{$property.id}-minutes').observe('keyup', {literal}function(){}{/literal});
    </script>
  </div>
  <div style="float:left;margin-right:10px">
    &nbsp;<br />
    :&nbsp;<input type="text" name="item[{$property.id}][s]" maxlength="2" value="{if $value.s}{$value.s}{else}00{/if}" style="width:45px" id="item-property-{$property.id}-seconds" />
    <script type="text/javascript">
      $('item-property-{$property.id}-seconds').observe('keyup', {literal}function(){}{/literal});
    </script>
  </div>

  <br clear="all" />
  <div style="float:left;margin-right:10px">
    Month<br />
    <select name="item[{$property.id}][M]" style="width:100px">
    	<option value="00"{if $value.M == "00"} selected="selected"{/if}></option>
    	<option value="01"{if $value.M == "01"} selected="selected"{/if}>January</option>
    	<option value="02"{if $value.M == "02"} selected="selected"{/if}>February</option>
    	<option value="03"{if $value.M == "03"} selected="selected"{/if}>March</option>
    	<option value="04"{if $value.M == "04"} selected="selected"{/if}>April</option>
    	<option value="05"{if $value.M == "05"} selected="selected"{/if}>May</option>
    	<option value="06"{if $value.M == "06"} selected="selected"{/if}>June</option>
    	<option value="07"{if $value.M == "07"} selected="selected"{/if}>July</option>
    	<option value="08"{if $value.M == "08"} selected="selected"{/if}>August</option>
    	<option value="09"{if $value.M == "09"} selected="selected"{/if}>September</option>
    	<option value="10"{if $value.M == "10"} selected="selected"{/if}>October</option>
    	<option value="11"{if $value.M == "11"} selected="selected"{/if}>November</option>
    	<option value="12"{if $value.M == "12"} selected="selected"{/if}>December</option>
    </select>
  </div>
  <div style="float:left;margin-right:10px">
    Day<br />
    <select name="item[{$property.id}][D]" style="width:70px">
    	<option value="00"{if $value.D == "00"} selected="selected"{/if}></option>
    	<option value="01"{if $value.D == "01"} selected="selected"{/if}>1st</option>
    	<option value="02"{if $value.D == "02"} selected="selected"{/if}>2nd</option>
    	<option value="03"{if $value.D == "03"} selected="selected"{/if}>3rd</option>
    	<option value="04"{if $value.D == "04"} selected="selected"{/if}>4th</option>
    	<option value="05"{if $value.D == "05"} selected="selected"{/if}>5th</option>
    	<option value="06"{if $value.D == "06"} selected="selected"{/if}>6th</option>
    	<option value="07"{if $value.D == "07"} selected="selected"{/if}>7th</option>
    	<option value="08"{if $value.D == "08"} selected="selected"{/if}>8th</option>
    	<option value="09"{if $value.D == "09"} selected="selected"{/if}>9th</option>
    	<option value="10"{if $value.D == "10"} selected="selected"{/if}>10th</option>
    	<option value="11"{if $value.D == "11"} selected="selected"{/if}>11th</option>
    	<option value="12"{if $value.D == "12"} selected="selected"{/if}>12th</option>
    	<option value="13"{if $value.D == "13"} selected="selected"{/if}>13th</option>
    	<option value="14"{if $value.D == "14"} selected="selected"{/if}>14th</option>
    	<option value="15"{if $value.D == "15"} selected="selected"{/if}>15th</option>
    	<option value="16"{if $value.D == "16"} selected="selected"{/if}>16th</option>
    	<option value="17"{if $value.D == "17"} selected="selected"{/if}>17th</option>
    	<option value="18"{if $value.D == "18"} selected="selected"{/if}>18th</option>
    	<option value="19"{if $value.D == "19"} selected="selected"{/if}>19th</option>
    	<option value="20"{if $value.D == "20"} selected="selected"{/if}>20th</option>
    	<option value="21"{if $value.D == "21"} selected="selected"{/if}>21st</option>
    	<option value="22"{if $value.D == "22"} selected="selected"{/if}>22nd</option>
    	<option value="23"{if $value.D == "23"} selected="selected"{/if}>23rd</option>
    	<option value="24"{if $value.D == "24"} selected="selected"{/if}>24th</option>
    	<option value="25"{if $value.D == "25"} selected="selected"{/if}>25th</option>
    	<option value="26"{if $value.D == "26"} selected="selected"{/if}>26th</option>
    	<option value="27"{if $value.D == "27"} selected="selected"{/if}>27th</option>
    	<option value="28"{if $value.D == "28"} selected="selected"{/if}>28th</option>
    	<option value="29"{if $value.D == "29"} selected="selected"{/if}>29th</option>
    	<option value="30"{if $value.D == "30"} selected="selected"{/if}>30th</option>
    	<option value="31"{if $value.D == "31"} selected="selected"{/if}>31st</option>
    </select>
  </div>

  <div style="float:left">
    Year<br />
    <input type="text" name="item[{$property.id}][Y]" size="5" maxlength="4" value="{if $value.Y}{$value.Y}{else}{$default_year}{/if}" style="width:85px" />
  </div>{if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}
</div>