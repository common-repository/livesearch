=== LiveSearch ===
Contributors: jaguarcy
Donate link: http://www.cneophytou.com/#donate
Tags: livesearch, search, widget
Requires at least: 2.0
Tested up to: 2.5
Stable tag: 1.4

Includes LiveSearch in your blog. Now Widgetized.

== Description ==

Allows you to include a 'LiveSearch' search box in your blog.

As the user types their search query in the search box, a list of results will appear in a pop-up style menu, formatted entirely in html. The menu can be navigated both by keyboard and mouse, and selecting a result from that list will load the requested page. Alternatively, pressing enter without selecting a result will bring up the search results page.

Compatible with the Search Pages plugin.

== Installation ==

Copy the livesearch directory as-is in your wp-contents/plugins/ directory (the resulting path should be wp-contents/plugins/livesearch/ ).

Then optionally copy the livesearch/livesearch.css file in your wp-contents/themes/yourtheme/ directory (if you want to have a seperate css file for each theme). This file can be modified to match your theme.

Then enable the plugin from the WordPress plugin administration section, and enjoy!

== Screenshots ==

1. Sample usage
2. Administration options

== Frequently Asked Questions ==

= Search box looks different, but doesn't work =
   
Your theme needs to call both the header and the footer hooks of WordPress, else the plugin will not work. Themes should call those hooks by default, and if they don't then they're badly designed. Contact your theme creator, or add

	<?php wp_head(); ?>

just before the &lt;/head&gt; tag in your header.php file (if it is missing), and

	<?php do_action('wp_footer'); ?>

just before the &lt;/body&gt; tag in your footer.php file (again, if missing).



= Plugin was installed, but search box does not look different than before, and doesn't seem to work =

This should not be an issue if you're using the widget form of the plugin.

It might be the case that your theme does not include the searchform.php file properly. If that is the case, then when you follow the instructions and install the plugin, nothing will change visually on your site. What you need to do is find the file where the search form is inserted, and replace the entire section (enclosed in &lt;form&gt; tags).

The section starts with something like

	<form action=...

and ends with

	</form>

Delete that whole section, and in its place enter this code:

	<?php include (TEMPLATEPATH . '/searchform.php'); ?>

If you can't do that (or don't want to), then contact your theme designer and ask for the searchform.php file to be properly included. Then installing this script should work just fine. 


== IF YOU DO NOT USE THE WIDGET: ==

* You must place a copy of the file livesearch/searchform.php in each of your theme directories that you want to use livesearch with.

== Additional ==

You can have a copy of livesearch.css in each of your theme directories, which can be modified to match your theme.

Also, the plugin has been widgetized, so if you have the WordPress Widget plugin, this plugin will replace the Search widget.

I'll be happy to answer and try to solve any javascript and/or WordPress theme related problems you might encounter.
