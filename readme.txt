=== Shorten2PingNG ===
Contributors: ipublicis
Plugin URI: http://www.ipublicis.com
Author: Lopo Lencastre de Almeida - iPublicis.com
Author URI: http://www.ipublicis.com
Donate link: http://smsh.me/7kit
Tags: ping.fm, bit.ly, ping, twitter, facebook, linkedin, plurk, tr.im, su.pr, yourls, smsh.me
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: 1.3.1

Sends <strong>status</strong> updates to Ping.fm or Twitter everytime you publish a post, using own domain or others for shortened permalinks.

== Description ==

Sends <strong>status</strong> updates to <a href="http://ping.fm">Ping.fm</a> or <a href="http://twitter.com">Twitter</a> everytime you publish a post. Using <a href="http://smsh.me/">Sm00sh</a>, <a href="http://bit.ly">Bit.ly</a>, <a href="http://tr.im">Tr.im</a>, <a href="http://yourls.org">YOURLS</a>, <a href="http://su.pr">Su.pr</a> or even your own domain for shortened permalinks (accounts on these services required). So this way you can send status to many sites at once if you have a Ping.fm account or send only to Twitter if you prefer that.

If you find it useful, please consider to make a <a href="http://smsh.me/7kit" target="_blank" title="Paypal Website"><strong>donation</strong></a> or offer something from <a href="http://www.amazon.co.uk/wishlist/2NQ1MIIVJ1DFS" target="_blank" title="Amazon Wish List">Amazon</a> to Shorten2PingNG's author (any thing will be appreciated).
Silk icons by <a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">FAMFAMFAM</a>

More info:

* Check out the other <a href="http://wordpress.org/extend/plugins/profile/ipublicis">Wordpress plugins</a> by the same author.

= Features =

* <strong>Avoids to ping again when editing</strong> previously pinged post or even when editing an old post not pinged before.
* Option to choose between <strong>Ping.fm or Twitter</strong> for new post notifications.
* Option to choose between <strong>Bit.ly, Tr.im, YOURLS, Su.pr or even you own domain</strong> for shortened permalinks.
* Option to turn off notification or shortener service. Now <strong>you can use Shorten2Ping only for notification</strong> if your domain is already short enough for you, <strong>or use only to get shortened urls for your posts.</strong>
* <strong>Stores created shortened permalink</strong> in a post meta field (used for template integration).
* <strong>Using <code>rel="shorturl"</code></strong> as proposed at http://wiki.snaplog.com/short_url, creating auto-discovery link tag for short url on single post page header.
* You can use a <strong>template tag for showing visitors the short URL</strong> (using the same rel attribute as above for the shortened permalink).
* <strong>Locale support</strong>. Now available in English, Spanish, and Italian. See translation section for more info.
* <strong>Simple</strong>, fast, and useful :)

= Requirements =

* PHP 5.x with CURL and JSON enabled (maybe works too in PHP4, but not tested and not supported by me).
* WordPress 2.7.x or higher (maybe would work on older WP, but not tested and not supported by me).
* Required your own account for the third party services that you want to use.
* Not tested and not supported on IIS servers.

= Translations =

If you want to make a translation for your language, use the shorten2ping.pot included and (if you want) send me the files to dev@ipublicis.com for including it into the plugin package, you'll be credited, of course (NOTE: No sponsored translations allowed).

Credits for present translations:

* Italian translation made by Riccardo Vianello AKA etms51
* Spanish translation made by Samuel Aguilera AKA samuelaguilera
    	
== Installation ==

* Extract the zip file and just drop the contents in the <code>wp-content/plugins/</code> directory of your WordPress installation (or install it directly from your dashboard) and then activate the Plugin from Plugins page.
* After that go to options page 'Shorten2Ping' and fill the required information for your accounts, and customize the message template if you want.
* Now you're ready, the plugin will notify Ping.fm or Twitter (at your choice) everytime you publish a new post (and Ping.fm will do the same to every site that you configured there, if you use this service).
  
== Frequently Asked Questions ==

= Will this plugin works in WordPress older than 2.7.x? =

Maybe, but use it at your own risk. I'll not support anyone using outdated WP (or outdated PHP). At the moment of writing this I&#8217;m running this plugin on <strong>WordPress 2.9 and PHP 5.x</strong> and works fine, and was running without problems on <strong>WP 2.8.x and 2.7.x</strong> too. If you run anything below <strong>WP 2.7 and PHP 5.x</strong>, please update for your own good! (and dont ask me for support).

= Why you did this plugin? =

I wanted a <strong>(single) simple</strong> plugin for using Ping.fm + Bit.ly to notify about new posts to my contacts.

= Are you planning to add more features? =

At first only a few little improvements maybe. But if someone suggest a nice feature, and if I have time enough maybe I'll add it.
But it's not my priority. I want to keep it simple.

= I want to show the short permalink to my visitors. How can I do this? =

Simply put <code><?php short_permalink(); ?></code> in your theme where you want to show short permalink. You can add the parameter 'linktext', <code><?php short_permalink('linktext'); ?></code> and it will use the short permalink as text for the link too. Or if you have advanced knowledge of WordPress
theme coding, you can get the <code>'short_url'</code> post meta directly and showing it as you like.

= Hey man!, when I turn off shortener services in Shorten2Ping and send notifications directly to Twitter, I get Bit.ly links anyway... =

Twitter has decided to shorten ALL URLs (except the ones already shortened) sent to Twitter using Bit.ly service (without user API). But you can keep track of the statistics if you dont put your Bit.ly information in the Shorten2Ping settings.

= Hey man!, the same than above question but with Ping.fm =

From sometime ago Ping.fm has his own link shortener service too, so if you disabled Shorten2Ping option to make shorlinks, but you see http://ping.fm/xxxx when you notify a post through Ping.fm, you must disable this service in your Ping.fm account settings.

= How can I select wich image to show when sending the post to Facebook? =

Since 1.3 version, Shorten2Ping adds a meta field in the single post view (the one that Facebook will scan) telling Facebook to use the first image <strong>uploaded and inserted</strong> in the post. If you want to override this, or simply you didn't uploaded any image to the post (even if you inserted one), you can add a custom field to the post named 'fb_img' and put the <strong>full</strong> URL to the image in the value for this custom field.

= My post has an image inserted into it, but your plugin is not inserting the Facebook meta to use this image for the thumbnail, what happens? =

It only works with images that you uploaded to <strong>THAT</strong> post, so if you inserted an image from the media library that was uploaded previously to other post. You'll need to use the 'fb_img' custom field described before. I could add some code to sort this out, but the only way to make that (scanning the post content) could make the posting process slower if you use big images.
                                    
== Changelog ==

= 1.3.1 =

* First forked version from original, made by <a href="http://profiles.wordpress.org/samuelaguilera/">Samuel Aguilera</a> that integrates my patch. Changed Ping.FM App API key to our own per Samuel's request.

= 1.3 =

* Added a function to remove the new wp.me shortlink creation functions present in Wordpress.com stats plugin (if you're using Shorten2Ping you dont need support for wp.me short links).
* Improved Facebook support: Now if you upload and insert an image in your post, Shorten2Ping will add the needed tag in your post code to tell Facebook to take this image for the preview and not the first image in your page's code like used to be before. If you want to force the use of an image, you can add a new custom field to the post named 'fb_img' and put the <strong>full</strong> URL to the image in the value for this custom field.
* Added function to remove HTML tags (if any) from post titles before sending notifications.
* Updated compatibility tag to WordPress 2.9.

= 1.2.8 =

* Finally fixed html entities in title when using Ping.fm to notify new posts (not happened with Twitter). The fix consists in using $post->the_title instead of get_the_title($post_id), so not really a Shorten2Ping's bug but different output for two WP functions that should do the same.
* Improved options page. Now looks better and it's XHTML valid code :)
* Updated locale file (.pot) and es_ES translation to reflect some changes in options page.

= 1.2.4 =

* Added settings link on plugins page
* Fixed bug in Su.pr support (thanks to Brad for reporting it!)

= 1.2.2 =

* Added italian translation made by Riccardo Vianello AKA etms51 
* Improved handling of settings when updating from 1.1.3 to 1.2.x
* Added some code to autofix blank settings created by 1.2 on new installations see http://wordpress.org/support/topic/319359?replies=4 (now you dont need to do that manually, simply deactivate and activate again the plugin).

= 1.2.1 =

* Fixed bug that prevents the plugin options to work on new installations.

= 1.2 =

* Changed checking user level (number based) to checking capabilities for the options page.
* Added YOURLS shortener support.
* Added Su.pr shortener support. (User requested feature).
* Added 'linktext' parameter to short_permalink() theme tag. Now you can choose between using short link as text for the link or use the "Short URL" text (default behaviour). (User requested feature).
* Added 'Self domain' option for shortened permalinks. If your domain is short enough for you, this option will not use any shortener service, but instead will use the post ID (default WP permalinks).

= 1.1.3 =

* Added checking to avoid to ping new pages. Now only posts are notified (as intended from start of making this plugin).

= 1.1.2 =
* Removed adding and checking of post meta data 'pinged = yes'. That was not needed since changes on 1.1.1
* Added 'both' option by user request to make possible to send notifications to both services (Ping.fm and Twitter) at once.
* Fixed typo that may cause users with no admin level to access to the options page. (Thanks to David Jack Wange Olrik for reporting the issue.)

= 1.1.1 =

* Changed action hook. Now the ping function is called only when post changed status from X to publish (where X can be new, draft, pending or future). So now, if you edit an old post (that you published before installing Shorten2Ping) or the post has any else status than mentioned this plugin does nothing.
* Removed echo statement at tr.im function (used while debugging) that causes no returning to post edit page after publishing on some cases.
* Removed @ operator at json_decode functions, now if any error happens while calling json_decode it will show on screen.

= 1.1 =

* Added support for http://twitter.com and http://tr.im
* Some other minor improvements/cleanup in the code.
* Store settings in an array to reduce calls to database.
* Added options for choose shorten and notification services, and to turn off them (so you can use Shorten2Ping only for notification if your domain is already short enough for you, or use Shorten2Ping only to get shortened urls for your posts).

= 1.0.2 =

* Replaced PHPingFM by Dmitri Gaskin for a single function based on the one by Sold Out Activist for the pingPressFM.

= 1.0.1 =

* Fixed a typo and Ping.fm developer approved.

= 1.0 =

* Initial release.

Originally based on WordTwit 1.2 by Duane Storey.

PingFM function based on one by Sold Out Activistclass for the pingPressFM plugin, and Bit.ly function by David Walsh & Jason Lengstorf.
