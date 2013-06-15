<div id="work-area">
  <h3>Test Twitter account</h3>
  <div class="special-box">
    This functionality is currently not compatible with v1.1 of the Twitter API, but will be updated soon. In the meantime, see tweets in a Twitter widget:
  </div>
  <div>
          <script src="http://widgets.twimg.com/j/2/widget.js"></script>
          <script>
          {literal}new TWTR.Widget({
            version: 2,
            type: 'profile',
            rpp: 5,
            interval: 6000,
            width: 405,
            height: 300,
            theme: {
              shell: {
                background: '#cccccc',
                color: '#242424'
              },
              tweets: {
                background: '#ffffff',
                color: '#474747',
                links: '#ff9900'
              }
            },
            features: {
              scrollbar: true,
              loop: false,
              live: false,
              hashtags: true,
              timestamp: true,
              avatars: true,
              behavior: 'all'
            }
          }{/literal}).render().setUser('{$acct}').start();
          </script>
        </div>
</div>