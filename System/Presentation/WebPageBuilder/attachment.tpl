<!--Smartest Text Attachment: <?sm:$_textattachment._name:?>-->
<div class="attachment" style="<?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>width:<?sm:$_textattachment.div_width :?>px;<?sm:/if:?><?sm:if $_textattachment.border :?>border: 1px solid #ccc;padding:10px;<?sm:/if:?><?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>float: <?sm:else:?>text-align: <?sm:/if:?><?sm:$_textattachment.alignment:?>; margin<?sm:if $_textattachment.alignment == "right" :?>-left<?sm:else if $_textattachment.alignment == "left" :?>-right<?sm:/if:?>: 10px!important;">
<?sm:if $_textattachment.float :?>
<?sm:_asset_from_object object=$_textattachment.asset_object style="margin:5px!important;display:inline-block":?>
<?sm:else:?>
<?sm:_asset_from_object object=$_textattachment.asset_object style="margin:5px 0 5px 0!important;display:inline-block":?>
<?sm:/if:?>
<?sm:if strlen($_textattachment.caption) :?><div class="attachment-caption" style="text-align:<?sm:$_textattachment.caption_alignment :?>;display:block; margin:5px;font-size:11px;<?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>width:<?sm:$_textattachment.asset.width :?>px<?sm:/if:?>"><?sm:$_textattachment.caption:?> <?sm:$_textattachment.edit_link:?></div><?sm:else:?><?sm:$_textattachment.edit_link:?><?sm:/if:?>
</div>