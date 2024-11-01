=== XML News Sitemap ===
Contributors: Justin Norton
Donate link: https://www.jnorton.co.uk/wordpress-google-news-xml-sitemap-plugin
Tags: Google News Sitemap, News sitemap, Google News, Google News XML Sitemap, Sitemap for Google News
Requires at least: 3.1
Tested up to: 4.9.x
Stable tag: 1.2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin provides a highly-configurable Google News XML Sitemap for WordPress.

== Description ==

This plugin provides a Google News XML Sitemap for WordPress that lists the most recent 1000 articles published in the past two days that are marked to be included in an auto-generated Google Sitemap News XML file in accordance with the standards detailed on the Creating a Google News sitemap page here: https://support.google.com/news/publisher/answer/74288?hl=en-GB

Created by Web Developer: Justin Norton - https://www.jnorton.co.uk

Sites using this plugin:
http://startups.co.uk/sitemap-news.xml
http://is4profit.com/sitemap-news.xml

== Installation ==

1. Unzip the xml-news-sitemap.zip file.
2. Upload the xml-news-sitemap folder (not just the files in it!) to your wp-content/plugins folder. If you're using FTP, use 'binary' mode.

== Changelog ==

= 1.2.5 =
* Changing priority of sitemap render function add_filter('template_redirect', array(&$this, 'renderXML'),0,0);

= 1.2.4 =
* Tested against WP 4.9.x

= 1.2.3 =
* Bug fix for add_filter('plugin_action_links_'.plugin_basename( __FILE__ ), array(&$this, 'add_action_links')); in gns_xml.php - line 49.

= 1.2.2 =
* Bug fix for missing unserialize for $settings['gns_xml_genres'] in /lib/template/gns_xml_output.php - line 90.

= 1.2.1 =
* Bug fixing last commit.
* Providing better defaults for genres.

= 1.2.0 =
* Changed input type for Genre to textfield due to Google changing guidance on Genre labels. Use a comma separated list of Genre values for this field - thanks to @w-sky for highlighting this issue. 
* Tested against WP 4.8.2

= 1.1.6 =
* Fixed non-english character entities in post titles.
* Fixed saving configuration options arrays.

= 1.1.5 =
* Fixed PHP warning on Add Post screen.

= 1.1.4 =
* Tweaking default settings.

= 1.1.3 =
* Bug fixing beta tests.

= 1.1.2 =
* First public release!