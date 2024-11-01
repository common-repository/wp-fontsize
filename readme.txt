=== WordPress Font Size ===
Contributors: bobbravo2
Tags: font size, ajax, frontend, font size adjust, fontsize
Requires at least: 3.0.0
Tested up to: 3.6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: trunk


Adds BODY classes to allow CSS customize the size of various fonts, and layouts of a page. Uses AJAX to store state in session.
== Description == 

This plugin does one thing: adds the HTML, JS, and CSS to the page (minified, of course) 
to add a font size adjust to the top right of the viewport. Adds a class to the body HTML element to allow your CSS to make all the layout
and font size changes your heart desires!  

**How it works**

1. Click on the fontsize buttons
1. AJAX Request to validate & save the new size
1. jQuery Changes the body class for instant results
1. Subsequent page navigation maintains the size without any Flash of Wrong Sized Text (FOWST) 

== Installation ==

**Please Note**

The default installation of this plugin will have no *visible* effect on your theme. 
You must add css to specify the size of the font adjustments, this plugin only adds the
body class = font-size-{$number} to allow your CSS to do the tweaking.

See the FAQ for configurable constants. 

**Demo CSS:**
This will change the font size of all paragraph elements. 
`.font-size-1 p
{
	font-size: 0.8em;
}
.font-size-2 p
{
	font-size: 0.9em;
}
...
other rules here
...
.font-size-7 p
{
	font-size: 1.7em;
}`
To target other elements:
`
.font-size-7 p, .font-size-7 h1, .font-size-7 h2 .(etc...) {
	font-size: 1.7em;
}
`
== Frequently Asked Questions ==
= Where is the admin screen? =
There is none. The only configuration can be done by adding the below constants to your wp-config.php file.
You'll need to author your CSS to take advantage of the stateful classes added to the <body> element.

= How do I customize the number of levels? =

Add the following constants to your wp-config.php file, adjusting the levels to your liking.
`
define('WP_FONTSIZE_MAX', 7);
define('WP_FONTSIZE_DEFAULT', 3);
define('WP_FONTSIZE_MIN', 1);
`

= How do I disable the including of the CSS? =
This disables the CSS that lays out the font size indicator in the top right of the screen. 
Use this to override the CSS with your own styles.
`
define('WP_FONTSIZE_CSS', false);
`
= How do I use the font size adjustor in my template?  =
This lets you use the plugin inside a template, by using the wp_fontsize::html() method. This will display the interface using the tag:
`
<?php wp_fontsize::html(); ?>
`
To disable the default html adjustor, make sure you add this to your wp-config.php:
`
define('WP_FONTSIZE_HTML', false);
`
== Screenshots ==

1. Clicking on the fontsize button increments or decrements the font-size-{$number} applied to the body of the page