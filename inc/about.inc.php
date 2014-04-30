<?php // 290414
defined( 'ABSPATH' ) ||	die( 'Please do not access this file directly. Thanks!' );
if ( ! apply_filters( 'hide_baw_ads', false ) ){
?>
<div style="width:276px;position:fixed;top:40px;right:5px">

	<div class="postbox" style="">
		<h3 class="hndle" style="padding:5px;"><span><?php _e( 'Need a faster website?' ); ?></span></h3>
		<div class="inside">
			<p><a href="http://wp-rocket.me" target="_blank">WP Rocket Premium Cache Plugin<br /><img title="WP Rocket" src="https://dl.dropboxusercontent.com/u/45956904/plugins/rocket-sidebar.jpg" width="250" height="250" /></a></p>
			<hr>
			<p>
				<img height="1" width="1" src="http://tracking.maxcdn.com/i/92548/3965/378" border="0" />
				<a href="http://tracking.maxcdn.com/c/92548/3965/378" target="_blank">MaxCDN Content Delivery Network<br /><img title="MaxCDN" src="http://adn.impactradius.com/display-ad/378-3965" width="250" height="250" /></a>
			</p>
		</div>
	</div>	

</div>
<?php }
if ( ! apply_filters( 'hide_baw_about', false ) ){ ?>
	<div class="postbox" style="width:325px;float:left;margin-right:15px">
		<h3 class="hndle" style="padding:5px;"><span><?php _e( 'About' ); ?></span></h3>
		<div class="inside">
			<p><img src="http://www.gravatar.com/avatar/<?php echo md5 ( 'julio'.'bosk'.'@'.'gmail'.'.'.'com' ); ?>" style="float:left;margin-right:10px;"/>
				<strong>Julio Potier</strong>, born in '79.<br />
				Hello, I'm a Web Security Consultant and WordPress Expert. I create plugins every day, i clean Web sites from hackers every day. I'm workaholic!<br /><br />
				<a href="http://www.boiteaweb.fr" target="_blank" title="BoiteAWeb.fr - WordPress Security Blog"><img src="https://dl.dropbox.com/u/45956904/plugins/bawlogo.png" /></a><br />
			</p>
		</div>
	</div>
	<div class="postbox" style="width:325px;float:left;margin-right:15px">
		<h3 class="hndle" style="padding:5px;"><span><?php _e( 'Help' ); ?></span></h3>
		<div class="inside">
			Need help? Use the <a href="http://wordpress.org/support/plugin/<?php echo basename( dirname( __DIR__ ) ); ?>" class="button button-secondary button-small">Support Forum</a><br />
			Please <a href="http://wordpress.org/plugins/<?php echo basename( dirname( __DIR__ ) ); ?>" class="button button-secondary button-small">rate the plugin</a><br />
			Check all my plugins on my <a href="http://profiles.wordpress.org/juliobox/"  class="button button-secondary button-small" title="on WordPress.org">WordPress Profile</a>
			</p>
		</div>
	</div>
<?php } ?>