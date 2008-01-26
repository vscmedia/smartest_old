<div id="main-nav">

            <ul>
              <li><a href="/" onmouseover="hideAllMenus()" style="width:70px"{if $this.fields.category=="home"} class="selected"{/if}>Home</a></li>
              <li><a href="{url to="page:about-us"}" onmouseover="mouseOverMenu(2)" style="width:85px"{if $this.fields.category=="about"} class="selected"{/if}>About Us</a></li>
              <li><a href="{url to="page:technology"}" onmouseover="mouseOverMenu(3)" style="width:100px"{if $this.fields.category=="technology"} class="selected"{/if}>Technology</a></li>
              <li><a href="{url to="page:product-applications"}" onmouseover="mouseOverMenu(4)" style="width:161px"{if $this.fields.category=="applications"} class="selected"{/if}>Product Applications</a></li>
              <li><a href="{url to="page:products"}" onmouseover="mouseOverMenu(5)" style="width:145px"{if $this.fields.category=="sales_licensing"} class="selected"{/if}>Sales &amp; Licensing</a></li>
              <li><a href="{url to="page:investors-media"}" onmouseover="mouseOverMenu(6)" style="width:150px"{if $this.fields.category=="investors_media"} class="selected"{/if}>Investors &amp; Media</a></li>
              <li><a href="{url to="page:corporate-responsibility"}" onmouseover="mouseOverMenu(7)" style="width:187px"{if $this.fields.category=="corporate_responsibility"} class="selected"{/if}>Corporate Responsibility</a></li>
            </ul>

          </div>