<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
         xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<!--This feature is not yet complete. This feature can be overridden by placing a file named sitemap.xml in the Public/ dir-->
{foreach from=$pages item="page"}
  <url> 
    <loc>http://{$site.domain}/{$page.info.url}</loc>
    <lastmod>{$page.info.modified.mysql_day}</lastmod>
    <priority></priority>
  </url>
{/foreach}
</urlset>