![WPGraphQl Clarity](./banner.png)

=== WPGraphQL ===
Contributors: Maurice Tadros
Tags: Headless Wordpress, Genesis Custom Blocks
Requires PHP: 7.1
Stable tag: 0.4.1
License: GPL-3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

=== Description ===
NOT READY FOR PRODUCTION.

Converts a Genesis custom block into html, which a headless wordpress site can parse into components.

The tag name is the slug value. The attributes are, the name/value of the fields. Inner blocks are repeaters in the same structure.

=== Special Cases ===

A field with the slug the_title will by default render to get_the_title()


=== Supports Fields ===

Image
Text
TextArea
Toggle
Rich Text
URL
Repeater

=== TODO ===

The preview is hard coded for now. It can be overwritten, but extending the filter

Need to find a way to add better testing data.
