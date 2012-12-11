=== Manual Related Posts ===
Contributors: juliobox
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJGT942XKWJ6W
Tags: related post, link post, relation, yarpp
Requires at least: 3.4
Tested up to: 3.5b2
Stable tag: trunk

Set related posts manually but easily with great ergonomics! Stop displaying auto/random related posts!

== Description ==
You can manually choose which posts have to be be linked to every posts.
Just click "Add related posts", search for a post, click, add. Done, post linked ! (FR/EN, NOJS supported)
Check the FAQ and Support if needed ;)

== Installation ==

1. Upload the *"baw-manual-related-posts"* to the *"/wp-content/plugins/"* directory
1. Activate the plugin through the *"Plugins"* menu in WordPress
1. Visit a post, a new meta box is here, you can now link posts easily ;)

== Frequently Asked Questions ==

1. Is there some filters?
*Yes, "bawmrp_li_style" can be used to overwrite the custom style on LI for front end display in thumb mode
*The filter "bawmrp_list_li" contains an array of all entries as LI tags. You can, for example, keep all-1 and add your Ad.
*The filter "bawmrp_posts_content" contains all vars to create the list, you can hack it now.
*The filter "bawmrp_more_content" contains the excerpt or content (if you choose to displays it) with a new line (BR tag) on front, you can hack this.
*The filter "bawmrp_no_thumb" can be used to change the default "no thumb" picture to display in "thumb mode"
*The filter "bawmrp_thumb_size" is an array containing thumb size, 100x100 is default.
*The filter "hide_baw_about" (works in all my plugins) can be use to avoid the inclusion of my "about" file (displayed at bottom of settings page)

1. Is the any actions?
*Yes, "bawmrp_first_li" and "bawmrp_last_li" can be used to add Ads for example, triggered before and after LI tags. 

1. Is there any shortcode?
*Yes, "bawmrp" and "manual_related_posts" are the same, you can do this in any php file :
*`echo do_shortcode( '[manual_related_posts]' );` or add directly in any post/page [manual_related_posts]

1. How works the cache system?
*Do i really have to answer this? Ok, like all other cache system, the first time you open a page, data are stored into a cache system (DB if you do not have a real cache plugin), and when the time (1 day by default) is over, a new cache replace the old one.
*The cache is changed when you change the options or when you add a manual post.
*If you want to test without cache, set "0" days.

== Screenshots ==

1. The meta box
1. Click, you'll get this window
1. Search for a word "Bonjour", and you'll get (with ajax) all posts from selected post type
1. Select it, linked!
1. NOJS view
1. Drag and Drop supported
1. Front-end list view (french demo)
1. Front-end thumb view (french demo)

== Changelog ==

= 1.7.17 =
* 11 dev 2012
* Thumbnails were not displayed, sorry.

= 1.7.16 =
* 14 nov 2012
* Forgot the about file ...

= 1.7.15 =
* 14 nov 2012
* Try to fix a bug that i do not trigger :|

= 1.7.14 =
* 14 nov 2012
* Add a filter "hide_baw_about" (works in all my plugins) to avoid the inclusion of my About section in settings page. props MadtownLems
* Default "number of posts" is now "4" intead of "0" (no limit!). props MadtownLems
* New option, if your theme does not support posts thumbnails you can choose to grab the first image from the post. props MadtownLems
* If the user can now "edit_themes" the hint "You can use a shortcode..." won't be displayed. props MadtownLems
* Fix a bug that you can add related posts from another post type but was not displayed. Thanks to kakawajazz
* Italian translation added, thanks to Manolo Macchetta

= 1.7.13 =
* 08 nov 2012
* Add a function "bawmrp_get_all_related_posts( $post )" to get all posts objects if needed for you in a theme or plugin, cache data done too in this function
* Add my own icon

= 1.7.12 =
* 07 nov 2012
* "0" for cache time wil disable input and output cache. Onyl for DEBUG (or low visited websites)

= 1.7.11 =
* 05 nov 2012
* Bug fix when you use "0" for cache time, if you still encounter display problem, you have to manually delete all transient, the name start with "_transient_bawmrp_" sorry

= 1.7.10 =
* 03 nov 2012
* FR l10n fixes
* Fix bug for shortcode
* Fix bug for text when no posts were find

= 1.7.9 =
* 01 nov 2012
* Fix some other warnings

= 1.7.8 =
* 01 nov 2012
* FR translation typo
* PHP warning fix

= 1.7.7 =
* 01 nov 2012
* HTML now allowed in front-end titles
* Fix warnings (i hope this time)
* You can set "0" days of cache if you want to correctly sets up the plugin, i suggest you at least 1 day cache when done.
* Bonus: The best rage user of all time about previous bugs: @happyweb http://baw.li/db/rageweb.png
* Bonus for @happyweb: transports-en-commun.info is not allowed to use the plugin. You win.

= 1.7.6 =
* 31 oct 2012
* Sorry, a error_reporting(-1) was present.

= 1.7.5 =
* 31 oct 2012
* PHP warning fix

= 1.7.4 =
* 30 oct 2012
* Many code improvment and optimisations
* Redo the findPost template and ajax call to do better things :
* We now can relate non published posts in advance, but only published will be displayed on front-end
* You can now add the excerpt or content of a related post below his title.
* You can choose to display or not a custom sentence when no related posts were found (really, no related posts ?)
* Some other minor changes like a new spinner on ajax call, separated core files, new filters (see FAQ)

= 1.6.4 =
* 19 oct 2012
* Shortcode Fix

= 1.6.3 =
* 19 oct 2012
* Translations fixes
* Fix: Checkbox for "Display in content" was always checked.

= 1.6.2 =
* 15 oct 2012
* Warning fix

= 1.6.1 =
* 15 oct 2012
* Add 3 more filters/action, see FAQ

= 1.6 =
* 15 oct 2012
* Add possibility to include sticky posts
* Add possibility to include recent posts
* Add 2 more CSS classes "bawmrp_sticky" and "bawmrp_recent"
* Teaser: 2.0 under dev

= 1.5.2 =
* 15 oct 2012
* Warning fixed

= 1.5.1 =
* 15 oct 2012
* Forgot to delete an echo test.

= 1.5 =
* 15 oct 2012
* You can now, so ironic, add auto related tags if you want to fill old posts with related posts. Auto related are found by tags, cats or both.
* <li> tags in displayed posts list contains now a class "bawmrp_manual" or "bawmrp_auto"
* You can random the display of related posts
* You can select a max posts to display, then, combined with random, you can display different manual/auto related posts.
* Caching data supported (for auto posts)
* Some code improvment
* Better uninstaller

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