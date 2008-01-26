<?php /* Smarty version 2.6.18, created on 2007-12-14 12:43:34
         compiled from /var/www/html/Presentation/Assets/savannah_menus.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'link', '/var/www/html/Presentation/Assets/savannah_menus.tpl', 20, false),)), $this); ?>
<div class="menus-page-width">
  
    
  <div class="menu-container" id="menu-holder-1" style="display:none">
    <div class="menu" id="menu-1" onmouseout="mouseOutMenu(1)">
      
      <ul>
        <li><a href="">Company News</a></li>
        <li><a href="">Regional News</a></li>
        <li><a href="">Podcasts</a></li>
      </ul>
    </div>
  </div>

  <div class="menu-container" id="menu-holder-2" style="display:none">
    <div class="menu" id="menu-2" onmouseout="mouseOutMenu(2)">
      <ul style="display:none"></ul>
      <ul>
        <li><?php echo smarty_function_link(array('to' => "page:company-profile",'with' => 'Company Profile'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:directors-management",'with' => "Directors &amp; Management"), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:corporate-strategy",'with' => 'Corporate Strategy'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:about-tanzania",'with' => 'About Tanzania'), $this);?>
</li>
      </ul>
    </div>
  </div>

  <div class="menu-container" id="menu-holder-3" style="display:none">
    <div class="menu" id="menu-3" onmouseout="mouseOutMenu(3)">
      <ul style="display:none"></ul>
      <ul>
        <li><?php echo smarty_function_link(array('to' => "page:exploration-strategy",'with' => 'Exploration Strategy'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:exploration-team",'with' => 'Exploration Team'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:technology",'with' => 'Technology'), $this);?>
</li>
      </ul>
    </div>
  </div>

  <div class="menu-container" id="menu-holder-4" style="display:none">
    <div class="menu" id="menu-4" onmouseout="mouseOutMenu(4)">
      <ul style="display:none"></ul>
      <ul>
        <li><?php echo smarty_function_link(array('to' => "page:clarity-capital",'with' => 'Clarity Capital'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:articles",'with' => 'News'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:press-releases",'with' => 'Press Releases'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:financial-technical-reports",'with' => "Financial &ndash; Technical Reports"), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:press-coverage",'with' => 'Press Coverage'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:presentations-podcasts",'with' => "Presentations &amp; Podcasts"), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:industry-links",'with' => 'Industry Links'), $this);?>
</li>
      </ul>
    </div>
  </div>

  <div class="menu-container" id="menu-holder-5" style="display:none">
    <div class="menu" id="menu-5" onmouseout="mouseOutMenu(5)">
      <ul style="display:none"></ul>
      <ul>
        <li><?php echo smarty_function_link(array('to' => "page:our-philosophy",'with' => 'Our Philosophy'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:the-cotton-tree-foundation",'with' => 'The Cotton Tree Foundation'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:health-safety",'with' => 'Health Safety'), $this);?>
</li>
        <li><?php echo smarty_function_link(array('to' => "page:corporate-governance",'with' => 'Corporate Governance'), $this);?>
</li>
      </ul>
    </div>
  </div>
  
</div>