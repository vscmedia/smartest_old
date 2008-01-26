<div id="main-nav">
  <ul>
    <li><a href="/" {if $this.fields.category=="home"} class="selected"{/if} onmouseover="hideAllMenus()">Home</a></li>
    <li><a href="#" onmouseover="mouseOverMenu(2)"{if $this.fields.category=="about"} class="selected"{/if}>About Us</a></li>
    <li><a href="#" onmouseover="mouseOverMenu(3)"{if $this.fields.category=="operations"} class="selected"{/if}>Operations</a></li>
    <li><a href="#" onmouseover="mouseOverMenu(4)"{if $this.fields.category=="investors_media"} class="selected"{/if}>Investors &amp; Media</a></li>
    <li><a href="#" onmouseover="mouseOverMenu(5)"{if $this.fields.category=="corporate_responsibility"} class="selected"{/if}>Corporate Responsibility</a></li>
  </ul>
</div>