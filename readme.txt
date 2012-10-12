=== Manual Related Posts ===
Contributors: juliobox
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJGT942XKWJ6W
Tags: related post, link post, relation, yarpp
Requires at least: 3.3.2
Tested up to: 3.4.1
Stable tag: trunk

Set related posts manually but easily with great ergonomics! Stop displaying auto/random related posts!

== Description ==
You can manually choose which posts will be linked to every posts, out auto selection, out random selection, out fake smart selection. Just click "Add related posts", search for post my title/content, click, add. Done, post linked ! (FR/EN/HU, NOJS supported)

== Installation ==

1. Upload the *"baw-manual-related-posts"* to the *"/wp-content/plugins/"* directory
1. Activate the plugin through the *"Plugins"* menu in WordPress
1. Visit a post, a new meta box is here, you can now link posts easily ;)

== Frequently Asked Questions ==

1. Is there some fiters ?
Yes, "bawmrp_li_style" can be used to overwrite the custom style on LI for front end display in thumb mode

1. Is there any shortcode ?
Yes, "bawmrp" and "manual_related_posts" are the same, you can do this in any php file :
`<?php echo do_shortcode( '[manual_related_posts]' ) ; ?>` or add directly in any post/page [manual_related_posts]

== Screenshots ==

1. The meta box
1. Click, you'll get this window (from WP core, not mine!)
1. Search for a word "Bonjour", and you'll get (with ajax) all posts from selected post type (ajax from WP core!)
1. Select it, linked!
1. NOJS view
1. Drag and Drop supported
1. Front-end list view (french demo)
1. Front-end thumb view (french demo)

== Changelog ==

= 1.4 =
* 12 oct 2012
* First, i'll thank Fran Hrzenjak (@fhrzenjak) because he gave me 2 good ideas and he sent me them, coded! I just imporved them to match my work better ;) Thank you Fran
* Added a drag and drop support to order related posts (see screenshot) (Fran idea)
* Added checkbox instead of radiobox in results, yes you can choose more than 1 per 1 posts in results now! Already added posts are disabled no worry.
* Added a "Clear all" button to clean related posts for a post.
* Removed useless posts types in findPosts box (Fran idea)
* New screenshots with WordPress 3.5 skin

= 1.3.1 =
* 02 oct 2012
* Fix a bug related to new shortcodes, worked only when "display at bottom of content" was checked. Thanks to Wido pointing me this.

= 1.3 =
* 28 sep 2012
* Added : New option, you can choose LIST or THUMB mode for displaying the related posts.
* Added : CSS class on my div "bawmrp" if you need more customization.
* Added : New filter on LI style "bawmrp_li_style", you can hack my style!
* Added : Shortcode to use it everywhere you need "bawmrp" or "manual_related_posts" (no params)

= 1.2.1 =
* 21 sep 2012
* Hungarian translation added, thanks to Tibor Takács (tyborg@tyborg.hu)

= 1.2 =
* 06 aug 2012
* Add an option to display on home page too.

= 1.1 =
* 06 jul 2012
* Front end view only displayed for singular posts (not on cat, tag, post list pages)
* Delete the head title but ...
* Add a head title for each post type!

= 1.0.3 =
* 02 jul 2012
* Change : get_post_types() behavior (not a big deal)

= 1.0.2 =
* 29 jun 2012
* Fix a huge bug if you're using meta fields in the same time of the related posts box. Thanks to @poupougnac ;)

= 1.0.1 =
* 26 jun 2012
* 3 l10n fixes

= 1.0 =
* 18 jun 2012
* First release

== Upgrade Notice ==

None