<ul class="site-map" id="tree-root">
  <?sm:defun name="menurecursion" list=$site_tree:?>
    
    <?sm:capture name="foreach_name" assign="foreach_name":?>list_<?sm:if $page.info.id:?><?sm:$page.info.id:?><?sm:else:?>0<?sm:/if:?><?sm:/capture:?>
    <?sm:capture name="foreach_id" assign="foreach_id":?><?sm:if $page.info.id:?><?sm:$page.info.id:?><?sm:else:?>0<?sm:/if:?><?sm:/capture:?>
    <?sm:foreach from=$list item="page" name=$foreach_name:?>
    
    <?sm:if $page.info.type == 'NORMAL' && $page.info.is_published == 'TRUE':?><li <?sm:if $smarty.foreach.$foreach_name.last:?>class="last"<?sm:elseif $smarty.foreach.$foreach_name.first:?>class="first"<?sm:else:?>class="middle"<?sm:/if:?>>
      
      <a id="item_<?sm:$page.info.webid:?>" class="option" href="<?sm:$page.info.url:?>">		 
        <img border="0" src="<?sm:$domain:?>Resources/Icons/page.gif" /><?sm:$page.info.title:?></a>
      
      <?sm:if !empty($page.children):?>
      
      <ul class="site-map" id="<?sm:$foreach_name:?>_<?sm:$smarty.foreach.$foreach_name.iteration:?>">
        <?sm:fun name="menurecursion" list=$page.children:?>
      </ul>
      
      <?sm:/if:?>
      
    </li><?sm:/if:?>
    <?sm:/foreach:?>
  <?sm:/defun:?>
</ul>
