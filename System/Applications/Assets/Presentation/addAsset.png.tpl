<h3>Add a Png Image Asset</h3>
<form action="{$domain}{$section}/saveNewPngImageAsset" method="post" name="newPngImage" enctype="multipart/form-data">

<input type="hidden" name="template_type" value="{$content.newAssetTypeId}" />
<input type="hidden" name="returnTo[method]" value="{$content.returnToMethod}" />
<input type="hidden" name="returnTo[section]" value="{$content.returnToSection}" />
<div style="margin-top:8px;margin-bottom:8px" id="pngUploader">
Name this Asset: <input type="text" name="image_stringid" /><br/>
Upload file: <input type="file" name="image_uploaded" /></div>
<div class="buttons-bar"><input type="submit" value="Save"  onclick="return validateImage();"></div>
</form>
<script language="javascript">
{literal}
function validateImage(){
var img=document.newPngImage.image_uploaded.value;
if (document.newPngImage.image_uploaded.value == "") {
    alert( "image field is blank" );
    document.newPngImage.image_uploaded.focus();
    return false ;
  }
var regex=/\.png$/i;
var regex2=/\./i;
if ((!regex.test(img)) && (regex2.test(img)))
{
alert("Only .png images are permitted");
document.newPngImage.image_uploaded.focus();
return false;
}
else return true;
}
{/literal}
</script>