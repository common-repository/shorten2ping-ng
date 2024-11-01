<?php
/*
Plugin Name: Shorten2PingNG
Plugin URI: http://www.ipublicis.com
Description: Sends <strong>status</strong> updates to Ping.fm or Twitter everytime you publish a post, using own domain or others for shortened permalinks. Like it? <a href="http://smsh.me/7kit" target="_blank" title="Paypal Website"><strong>Donate</strong></a> | <a href="http://www.amazon.co.uk/wishlist/2NQ1MIIVJ1DFS" target="_blank" title="Amazon Wish List">Amazon Wishlist</a> | Silk icons by <a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">FAMFAMFAM</a>
Author: Lopo Lencastre de Almeida - iPublicis.com
Version: 1.3.1
Author URI: http://www.ipublicis.com
Donate link: http://smsh.me/7kit
*/

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License version 3 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/*
  Changelog:
  
  From now on, see the readme for changelog.
  
*/


// setting some internal information
$shorten2ping_dirname = plugin_basename(dirname(__FILE__));
$shorten2ping_url = WP_PLUGIN_URL . '/' . $shorten2ping_dirname;
$donate = '<a href="http://smsh.me/7kit">donation</a>';

//load translation file if any for the current language
load_plugin_textdomain('shorten2ping', PLUGINDIR . '/' . $shorten2ping_dirname . '/locale');


// simple function to use in your theme if you want to show the short url for the current post

function short_permalink($linktext="") {

      global $post;
      
      $short_permalink = get_post_meta($post->ID, 'short_url', 'true');   

      if ($linktext == 'linktext') {
          
          $linktext = $short_permalink;
          
          } elseif (empty($linktext)) {
                            $linktext = __('Short URL','shorten2ping');
          }
          
      $post_title = strip_tags($post->post_title);
      
      // Using rel="shorturl" as proposed at http://wiki.snaplog.com/short_url
      if (!empty($short_permalink)) echo "<a href=\"$short_permalink\" rel=\"shorturl\" title=\"$post_title\">" . $linktext . "</a>";
    }
    
function short_url_head() {

    global $post;
    
    $short_permalink = get_post_meta($post->ID, 'short_url', 'true');    
    
    if (is_single($post->ID) && !empty($short_permalink)) {
    
          echo "<!-- Shorturl added by shorten2ping -->\n";
          echo "<link rel=\"shorturl\" href=\"$short_permalink\" />\n";    
    }

}


function fb_thumb_in_head() {
    
     global $post;
        
	   $fb_thumbnail = get_post_meta($post->ID, 'fb_img', true);

	
       	 if (is_single($post->ID)  && !empty($fb_thumbnail)) {

                  echo "<!-- Img for Facebook thumbnail added by Shorten2Ping -->\n";
                  echo "<link rel=\"image_src\" href=\"$fb_thumbnail\" />\n";          
          
          } else {
          
        	  //Get images attached to the post
            $args = array(
        	   'post_type' => 'attachment',
        	   'post_mime_type' => 'image',
        	   'numberposts' => 1,
        	   'post_status' => null,
        	   'post_parent' => $post->ID
            );
        
            $attachments = get_posts($args);          
     	          
              if ($attachments) {
          	
                    foreach ($attachments as $attachment) { 
                	
                  	$fb_thumbnail = wp_get_attachment_url( $attachment->ID );
                
                      
                     }

                  echo "<!-- Img for Facebook thumbnail added by Shorten2Ping -->\n";
                  echo "<link rel=\"image_src\" href=\"$fb_thumbnail\" />\n";            
            
               }
          
         }
    
}


function shorten2ping_published_post($post)
{

// get user ID to use in multi author blogs.
    global $user_ID;
    get_currentuserinfo();

    if ( $post->post_type != 'post' ) return;  // dont ping pages
      	
    $post_id = $post->ID;
	
    $s2p_options = get_option('shorten2ping_options_'.$user_ID);	
	  
    $pingfm_user_key = $s2p_options['pingfm_key'];
      
    $post_url = get_permalink($post_id);
    $post_title = strip_tags($post->post_title);

    $short_url_exists = get_post_meta($post_id, 'short_url', true);
   
             if (empty($short_url_exists)) {
             
                  if ($s2p_options['shorten_service'] == 'bitly') {
              
                    //acortamos la url del post con bit.ly            
                      $bitly_user = $s2p_options['bitly_user'];
                      $bitly_key = $s2p_options['bitly_key'];
                      
                      $short_url = make_bitly_url($post_id,$post_url,$bitly_user,$bitly_key);
                      
                      } elseif ($s2p_options['shorten_service'] == 'trim') {
                      
                      $trim_user = $s2p_options['trim_user'];
                      $trim_pass = $s2p_options['trim_pass'];
                                            
                      $short_url = make_trim($post_id,$post_url,$trim_user,$trim_pass);                      
                      
                      } elseif ($s2p_options['shorten_service'] == 'yourls') {
                                     
                      $yourls_api = $s2p_options['yourls_api'];
                      $yourls_user = $s2p_options['yourls_user'];
                      $yourls_pass = $s2p_options['yourls_pass'];
                                            
                      $short_url = make_yourls($post_id,$post_url,$yourls_api,$yourls_user,$yourls_pass);
                      
                      } elseif ($s2p_options['shorten_service'] == 'none') {
                                     
                      $short_url = $post_url;
                      
                      }
                      
                      elseif ($s2p_options['shorten_service'] == 'supr') {
                                     
                      $supr_key = $s2p_options['supr_key'];
                      $supr_user = $s2p_options['supr_user'];
                                            
                      $short_url = make_supr($post_id,$post_url,$supr_key,$supr_user);
                      
                      }

                      elseif ($s2p_options['shorten_service'] == 'selfdomain') {
                      
                      $s2p_blog_url = get_bloginfo(url);
                                            
                      $short_url = $s2p_blog_url . '/?p=' . $post_id;
                      
                      add_post_meta($post_id, 'short_url', $short_url);
                      
                      }               
            
            } else {
            
              $short_url = $short_url_exists;
            
            }
            
            //get message from settings and process title and link
            $message = $s2p_options['message'];
            $message = str_replace('[title]', $post_title, $message);
            $message = str_replace('[link]', $short_url, $message);
                        
            if ($s2p_options['ping_service'] == 'pingfm'){

               send_pingfm($pingfm_user_key,$post_id,$message);
                            
            } elseif ($s2p_options['ping_service'] == 'twitter') {
                                
               send_twit($post_id,$s2p_options['twitter_user'], $s2p_options['twitter_pass'], $message);           
            
            } elseif ($s2p_options['ping_service'] == 'none') {
            
            return;
            
            } elseif ($s2p_options['ping_service'] == 'both') {
            
              send_pingfm($pingfm_user_key,$post_id,$message);
              send_twit($post_id,$s2p_options['twitter_user'], $s2p_options['twitter_pass'], $message);
            
            }                     

}

function s2p_bnc_stripslashes_deep($value)
{
	$value = is_array($value) ?
		array_map('s2p_bnc_stripslashes_deep', $value) :
		stripslashes($value);
	return $value;

}


function s2c_init_options() {
     

// get user ID to use in multi author blogs.
  global $user_ID;
  get_currentuserinfo();

// create options array. if options already exists add_option function does nothing.

  $s2p_options['message'] = "New post, \"[title]\" - [link]";
  $s2p_options['ping_service'] = "pingfm";
  $s2p_options['pingfm_key'] = "";
  $s2p_options['twitter_user'] = "";
  $s2p_options['twitter_pass'] = "";
  $s2p_options['shorten_service'] = "smsh";
  $s2p_options['bitly_user'] = "";
  $s2p_options['bitly_key'] = "";
  $s2p_options['trim_user'] = "";
  $s2p_options['trim_pass'] = "";
  $s2p_options['yourls_api'] = "";
  $s2p_options['yourls_user'] = "";
  $s2p_options['yourls_pass'] = "";
  $s2p_options['supr_user'] = "";
  $s2p_options['supr_key'] = "";
  
// removed old 1.0 non array settings to array migration
 
  add_option('shorten2ping_options_'.$user_ID, $s2p_options );

// check if plugin installed is previous to 1.2.x, and add new options in that case

  $existing_s2p_options = get_option('shorten2ping_options');
  
  $old_settings = count($existing_s2p_options);
  
  if (empty($existing_s2p_options)) {
  
      delete_option('shorten2ping_options');
      add_option('shorten2ping_options_'.$user_ID, $s2p_options );

  } elseif ($old_settings == 10) {

      $s2p_new_options = array ("yourls_api" => "", "yourls_user" => "", "yourls_pass" => "", "supr_user" => "", "supr_key" => "");
      $merged_options = array_merge($existing_s2p_options, $s2p_new_options);
          
      delete_option ('shorten2ping_options');
      add_option('shorten2ping_options_'.$user_ID, $merged_options );  
  }

}

function shorten2ping_options_subpanel()
{

// get user ID to use in multi author blogs.
  global $donate, $user_ID;

  get_currentuserinfo();
  
  $s2p_options = get_option('shorten2ping_options_'.$user_ID);  

	if (get_magic_quotes_gpc()) {
		$_POST = array_map('s2p_bnc_stripslashes_deep', $_POST);
	    $_GET = array_map('s2p_bnc_stripslashes_deep', $_GET);
	    $_COOKIE = array_map('s2p_bnc_stripslashes_deep', $_COOKIE);
	    $_REQUEST = array_map('s2p_bnc_stripslashes_deep', $_REQUEST);
	}


  	if (isset($_POST['info_update'])) 
	{
	
	    foreach( $s2p_options as $key => $value ) {
			if(isset($_POST[$key])) {
				if ($_POST[$key] == $_POST['message']) {
                    $s2p_options[$key] = stripslashes($_POST['message']);
                } else { 
                    $s2p_options[$key] = $_POST[$key];
				} 
			} else {	
				$s2p_options[$key] = '';     
			}
	    }

	    update_option( 'shorten2ping_options_'.$user_ID, $s2p_options );
        echo '<div id="message" class="updated fade"><p><strong>' . __('Settings saved.') . '</strong></p></div>';

	} 

	?>
<div class="wrap">
	 <div id="icon-options-general" class="icon32"><br /></div>
	 
	 <h2><?php _e('Shorten2Ping Options','shorten2ping') ?></h2>
 
		<p><?php _e('Shorten2Ping allows you to update status at Ping.fm or Twitter (only) whenever a new blog entry is published.  To start using it, simply enter the required information below, and press update information button. You only need to fill data for the services you want to use (i.e. if you want to use Ping.fm and not Twitter, you dont need to fill Twitter information).','shorten2ping') ?>
    </p><p>
		<?php _e('You can also customize the message for the status notification by using the "message" field below.  You can use [title] to represent the title of the blog entry, and [link] to represent the permalink.','shorten2ping') ?>
    </p>

  <div id="tabs">
  
  <ul>
    <li><a href="#tabs-1"><?php _e('General','shorten2ping'); ?></a> |</li>
    <li><a href="#tabs-2"><?php _e('Notification','shorten2ping'); ?></a> |</li>
    <li><a href="#tabs-3"><?php _e('Shorteners','shorten2ping'); ?></a></li>
  </ul>
  
  <form method="post" name="options" action="">
    <div id="tabs-1">    
      <br />
          
      <table width="100%" cellspacing="0" class="widefat">
        <thead>
          <tr>
            <th width="140"><?php _e('Setting','shorten2ping'); ?></th>
            <th width="450">&nbsp;</th>
            <th><?php _e('Description','shorten2ping'); ?></th>
          </tr>
        </thead>
        
			<tr><th><?php _e('Status Message','shorten2ping') ?></th>
      <td><input type="text" name="message" class="widefat" value="<?php echo(htmlentities(utf8_decode($s2p_options['message']))); ?>" /></td>
      <td class="description"><?php _e('Max. 200 characters for Ping.fm (including real title and shortened url), 140 if you wish to send it to Twitter.','shorten2ping') ?>
      </td>
      </tr>        
        
      <tr>
	    <th><?php _e('Shorten Permalinks With:','shorten2ping') ?></th>
      <td><select name="shorten_service">
	         <option value='smsh' <?php if ($s2p_options['shorten_service'] == 'smsh') echo 'selected="selected"'; ?> ><?php _e('Sm00sh','shorten2ping') ?></option>
	         <option value='bitly' <?php if ($s2p_options['shorten_service'] == 'bitly') echo 'selected="selected"'; ?> ><?php _e('Bit.ly','shorten2ping') ?></option>
	         <option value='trim' <?php if ($s2p_options['shorten_service'] == 'trim') echo 'selected="selected"'; ?> ><?php _e('Tr.im','shorten2ping') ?></option>
	         <option value='yourls' <?php if ($s2p_options['shorten_service'] == 'yourls') echo 'selected="selected"'; ?> ><?php _e('YOURLS','shorten2ping') ?></option>
	         <option value='supr' <?php if ($s2p_options['shorten_service'] == 'supr') echo 'selected="selected"'; ?> ><?php _e('Su.pr','shorten2ping') ?></option>
	         <option value='selfdomain' <?php if ($s2p_options['shorten_service'] == 'selfdomain') echo 'selected="selected"'; ?> ><?php _e('Self domain','shorten2ping') ?></option>
	         <option value='none' <?php if ($s2p_options['shorten_service'] == 'none') echo 'selected="selected"'; ?> ><?php _e('None','shorten2ping') ?></option>
	         </select></td>
      <td class="description"><?php _e('Choose to make short URLs using Sm00sh (default) or other, or turn off this feature.','shorten2ping') ?></td>
      </tr>             

      <tr>
	    <th><?php _e('Send Notification To:','shorten2ping') ?></th>
      <td><select name="ping_service">
	         <option value='pingfm' <?php if ($s2p_options['ping_service'] == 'pingfm') echo 'selected="selected"'; ?> ><?php _e('Ping.fm','shorten2ping') ?></option>
	         <option value='twitter' <?php if ($s2p_options['ping_service'] == 'twitter') echo 'selected="selected"'; ?> ><?php _e('Twitter','shorten2ping') ?></option>
	         <option value='both' <?php if ($s2p_options['ping_service'] == 'both') echo 'selected="selected"'; ?>><?php _e('Both','shorten2ping') ?></option>
	         <option value='none' <?php if ($s2p_options['ping_service'] == 'none') echo 'selected="selected"'; ?>><?php _e('None','shorten2ping') ?></option>
           </select></td>
      <td class="description"><?php _e('Choose to send notification to Ping.fm (default), Twitter, both services, or turn off this feature.','shorten2ping') ?></td>
      </tr>
      
      </table>
    
    </div>
    
    <div id="tabs-2">

      <br />
    
      <table width="100%" cellspacing="0" class="widefat">
        <thead>
          <tr>
            <th width="140"><?php _e('Setting','shorten2ping'); ?></th>
            <th width="450">&nbsp;</th>
            <th><?php _e('Description','shorten2ping'); ?></th>
          </tr>
        </thead>
        
  		<tr><th><?php _e('Ping.fm','shorten2ping') ?></th>
      <td><?php _e('API Key','shorten2ping') ?> <input type="text" class="widefat" name="pingfm_key" value="<?php echo($s2p_options['pingfm_key']); ?>" /></td>
      <td class="description"><?php _e('Put your Ping.fm <a href="http://ping.fm/key/">API key</a> here.','shorten2ping') ?></td>
      </tr>

      
			<tr><th><?php _e('Twitter','shorten2ping') ?></th><td><?php _e('Username','shorten2ping') ?> <input type="text" name="twitter_user" class="widefat" value="<?php echo($s2p_options['twitter_user']); ?>" />
      <?php _e('Password','shorten2ping') ?> <input type="password" name="twitter_pass" class="widefat" value="<?php echo($s2p_options['twitter_pass']); ?>" />
      </td><td class="description"><?php _e('Unfortunately <a href="http://twitter.com">Twitter</a> doesn\'t have API keys for users, so you must put here your user login and password if you want to use this service.','shorten2ping') ?>
      </td>
      </tr>

      </table>
      
    </div>
    
    <div id="tabs-3">
    
      <br />
      
      <table width="100%" cellspacing="0" class="widefat">
        <thead>
          <tr>
            <th width="140"><?php _e('Setting','shorten2ping'); ?></th>
            <th width="450">&nbsp;</th>
            <th><?php _e('Description','shorten2ping'); ?></th>
          </tr>
        </thead>

      <tr><th><?php _e('Bit.ly','shorten2ping') ?></th>
      <td><?php _e('API Login','shorten2ping') ?> <input type="text" class="widefat" name="bitly_user" value="<?php echo($s2p_options['bitly_user']); ?>" />
			<?php _e('API Key','shorten2ping') ?> <input type="text" class="widefat" name="bitly_key" value="<?php echo($s2p_options['bitly_key']); ?>"  />
      </td>
      <td class="description"><?php _e('Put here your API login and <a href="http://bit.ly/account/">Bit.ly</a> API key.','shorten2ping') ?>
      </td>
      </tr>
			
      <tr><th><?php _e('Tr.im','shorten2ping') ?></th><td><?php _e('Username','shorten2ping') ?> <input type="text" class="widefat" name="trim_user" value="<?php echo($s2p_options['trim_user']); ?>"  />
      <?php _e('Password','shorten2ping') ?> <input type="password" class="widefat" name="trim_pass" value="<?php echo($s2p_options['trim_pass']); ?>"  /></td>
      <td class="description"><?php _e('Unfortunately <a href="http://tr.im">Tr.im</a> doesn\'t have API keys for users, so you must put here your user login and password if you want to use this service.','shorten2ping') ?></td>
      </tr>
      
			<tr><th><?php _e('YOURLS','shorten2ping') ?></th>
      <td><?php _e('Username','shorten2ping') ?> <input type="text" class="widefat" name="yourls_user" value="<?php echo($s2p_options['yourls_user']); ?>"  />
      <?php _e('Password','shorten2ping') ?> <input type="password" class="widefat" name="yourls_pass" value="<?php echo($s2p_options['yourls_pass']); ?>" />
      </td><td class="description"><?php _e('Put here your username and password for <a href="http://yourls.org/">YOURLS</a>.','shorten2ping') ?>
      </td></tr>
      
      <tr><th>&nbsp;</th>
      <td><?php _e('YOURLS API URL','shorten2ping') ?> <input type="text" name="yourls_api" class="widefat" value="<?php echo($s2p_options['yourls_api']); ?>" />
      </td><td class="description"><?php _e('Example: http://example.com/yourls-api.php','shorten2ping') ?>
      </td>
      </tr>
      <tr><th><?php _e('Su.pr','shorten2ping') ?></th>
      <td><?php _e('API Login','shorten2ping') ?> <input type="text" name="supr_user" class="widefat" value="<?php echo($s2p_options['supr_user']); ?>" />
			<?php _e('API Key','shorten2ping') ?> <input type="text" name="supr_key" class="widefat" value="<?php echo($s2p_options['supr_key']); ?>" />
      </td><td class="description"><?php _e('Put here your API login and <a href="http://su.pr/settings/">Su.pr</a> API key.','shorten2ping') ?>
      </td>
      </tr>
      
      </table>

    </div>

   		<div class="submit"><input type="submit" class="button-primary" name="info_update" value="<?php _e('Save settings','shorten2ping') ?>" /></div>

     </form>
     
    <p>
		<?php _e("If you find this plugin useful, please consider to make a donation to Shorten2Ping's author (any amount will be appreciated).",'shorten2ping') ?>
    </p>

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post"><div class="paypal-donations"><input type="hidden" name="cmd" value="_donations" /><input type="hidden" name="business" value="&#x64;&#x6f;&#x6e;&#x61;&#x74;&#x65;&#x40;&#x73;&#x61;&#x6d;&#x75;&#x65;&#x6c;&#x61;&#x67;&#x75;&#x69;&#x6c;&#x65;&#x72;&#x61;&#x2e;&#x63;om" /><input type="hidden" name="item_name" value="Shorten2Ping WordPress Plugin" /><input type="hidden" name="item_number" value="shorten2ping" /><input type="hidden" name="currency_code" value="EUR" /><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online." /><img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /></div></form>   
        
    </div>

</div>
	<?php
}

function shorten2ping_add_plugin_option()
{
    global $shorten2ping_plugin_prefix;

    $shorten2ping_plugin_name = 'Shorten2Ping';

    if (function_exists('add_options_page')) 
    {
       $s2p_options_page = add_options_page($shorten2ping_plugin_name, $shorten2ping_plugin_name, 'publish_posts', basename(__FILE__), 'shorten2ping_options_subpanel');
    }

    add_action("admin_print_scripts-$s2p_options_page", 's2p_admin_js');
    add_action("admin_print_styles-$s2p_options_page", 's2p_admin_css');	
}

function shorten2ping_add_settings_link($links) {
	$settings_link = '<a class="edit" href="options-general.php?page=shorten2ping.php" title="'. __('Go to settings page','shorten2ping') .'">' . __('Settings','shorten2ping') . '</a>';
	array_unshift( $links, $settings_link ); // before other links
	return $links;
}

// Funtion to send 'status' to Ping.fm. Based on the one by Sold Out Activist for the pingPressFM

function send_pingfm($pingfm_user_key,$post_id,$message) {
    if (!$pingfm_user_key) return false;
                 	
    $post_data = Array(
	           'api_key' => '6f604abd220a79bcd443a4824354734d',
	           'user_app_key' => $pingfm_user_key,
	           'post_method'  => 'status',
	           'body'  => $message,
                 // 'debug' => 1
		            );

// send data to ping.fm
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, 'http://api.ping.fm/v1/'. 'user.post');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $output = curl_exec($ch);

// if ok, stores the ping_id
    if (preg_match('/OK/', $output)) {
	preg_match('/\<transaction\>([^\<]*)\<\/transaction\>/', $output, $match);
	$ping_result = addslashes(trim($match[1]));

//only for debugging. not needed to work.
    // add_post_meta($post_id, 'pinged', $ping_result);
			     
// if not ok, stores the error message
    } else {
	preg_match('/\<message\>([^\<]*)\<\/message\>/', $output, $match);
	$ping_result = addslashes(trim($match[1]));
		
	add_post_meta($post_id, 'pingfm_error', $ping_result);
    }
           	
}

function send_twit ($post_id,$twitter_user,$twitter_pass,$message) {
	
    $twitter_host = "http://twitter.com/statuses/update.json?status=" . urlencode(stripslashes(urldecode($message))); 

    $ch = curl_init();
	
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); //Use basic authentication
    curl_setopt($ch, CURLOPT_USERPWD, "$twitter_user:$twitter_pass");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Do not check SSL certificate (but use SSL).
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $twitter_host);

    $result = curl_exec($ch);	
    $json = json_decode($result,true);
				
    if ($json['error']) {
    
       add_post_meta($post_id, 'twitter_error', $json['error']);
    
    } 

}


// Original code by David Walsh (http://davidwalsh.name/bitly-php), improved by Jason Lengstorf (http://www.ennuidesign.com/).

      function make_bitly_url($post_id, $url, $login, $appkey, $history=1, $version='2.0.1')
          {
                //create the URL
                $bitly = 'http://api.bit.ly/shorten';
                $param = 'version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format=json&history='.$history;

                //get the url
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
                curl_setopt($ch, CURLOPT_URL, $bitly . "?" . $param);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);

                $json = json_decode($response,true);
                
                // check if all goes ok, if not, return error message
                
                    if ($json['statusCode'] == 'OK') {
                                           
                      add_post_meta($post_id, 'short_url', $json['results'][$url]['shortUrl']);
                      
                      return $json['results'][$url]['shortUrl'];                   
                    
                    } else {
                    
                      add_post_meta($post_id, 'bitly_error', $json['errorMessage']);                 
                    
                    }                

          }
          
// Function to shorten post URL using tr.im
      function make_trim($post_id, $url, $trim_user, $trim_pass)
          {        
         
                //create the URL
                $trim = 'http://api.tr.im/api/trim_url.json';
                $param = '?url='.urlencode($url);

                //get the url
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); //Use basic authentication
                curl_setopt($ch,CURLOPT_USERPWD,$trim_user . ":" . $trim_pass);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Do not check SSL certificate (but use SSL).
                curl_setopt($ch, CURLOPT_URL, $trim . $param);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);

                $json = json_decode($response,true);
                
                // check if all goes ok, if not, return error message
                
                    if ($json['status']['result'] == 'OK') {
                                           
                        add_post_meta($post_id, 'short_url', $json['url']);
                        
                        return $json['url'];                    
                    
                    } else {
                    
                      add_post_meta($post_id, 'trim_error', $json['status']['message']);                  
                    
                    }           
                       
          }

 
          function make_yourls ($post_id,$post_url,$yourls_api,$yourls_user,$yourls_pass) {
          
                  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2PING');
                  curl_setopt($ch, CURLOPT_URL, $yourls_api);
                  curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
                  curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
                  curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
                  		'url'      => $post_url,
                  		'format'   => 'json',
                  		'action'   => 'shorturl',
                  		'username' => $yourls_user,
                  		'password' => $yourls_pass
                  	));

                  $response = curl_exec($ch);
                  curl_close($ch);

                  $json = json_decode($response,true);
                  
                  // check if all goes ok, if not, return error message
                  
                      if ($json['status'] == 'success') {
                                             
                          add_post_meta($post_id, 'short_url', $json['shorturl']);
                          
                          return $json['shorturl'];                    
                      
                      } else {
                      
                        add_post_meta($post_id, 'yourls_error', $json['message']);                  
                      
                      } 
               
          
          }
 
          function make_supr($post_id,$post_url,$supr_key,$supr_user) {
                            
	     // create API URL
		  $supr_result = 'http://su.pr/api/shorten?longUrl='.$post_url.'&login='.$supr_user.'&apiKey='.$supr_key;

             // get the surl
		  $ch=curl_init();
		  curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2PING');
		  curl_setopt($ch,CURLOPT_URL, $supr_result);
		  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 15);
		  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		  $response = curl_exec($ch);
		  curl_close($ch);

                  $json = json_decode($supr_result,true);
                  
                  // check if all goes ok, if not, return error message
                  
                      if ($json['statusCode'] == 'OK') {
                                             
                          add_post_meta($post_id, 'short_url', $json['results'][$post_url]['shortUrl']);
                          
                          return $json['results'][$post_url]['shortUrl'];                    
                      
                      } else {
                      
                        add_post_meta($post_id, 'supr_error', $json['errorMessage']);                  
                      
                      }           
          
          } 
 
 // Another function for making tr.im, but using simple method  -NOT USED, only for testing-
 
          function make_simple_trim($url,$user,$pass) {
	// create API URL
		$trim_url = 'http://api.tr.im/api/trim_simple?url='.urlencode($url).'&username='.$user.'&password='.$pass;
	// get the surl
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2PING');
		curl_setopt($ch,CURLOPT_URL, $trim_url);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

                return $trim_url;
            }


// Function to shorten post URL using sm00sh at smsh.me

	function make_sm00sh($url) {

	// create API URL
		$sm00sher = 'http://smsh.me/?id='.sha1(get_bloginfo('wpurl')).'&api=json&url='.urlencode($url);
	
	// get the surl
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2PING');
		curl_setopt($ch,CURLOPT_URL, $sm00sher);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

	// returns {  "title": "HTTP/1.0 200 OK",  "body": "http://smsh.me/7jk1" }
		$json = json_decode($response,true);

	// check if all goes ok, if not, return error message
		if ($json['title'] == 'HTTP/1.0 200 OK') {
                        add_post_meta($post_id, 'short_url', $json['body']);
			return $json['body'];                   
		} else {
                        add_post_meta($post_id, 'smsh_error', $json['title']."\n".$json['body']);                  
		}
	}


// tabs for options page

function s2p_admin_js() { // options js
	global $shorten2ping_url;
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('s2p_tabs_js', $shorten2ping_url . '/includes/s2p_admin.js', array('jquery-ui-tabs'));
}
function s2p_admin_css() { // options css
	global $shorten2ping_url;
	wp_enqueue_style('s2p_tabs_css', $shorten2ping_url . '/includes/s2p_admin.css');
}

// remove wordpress stats wp.me shorlink creation if present.

    if ( !function_exists('remove_wpme') ) {

      add_action( 'plugins_loaded', 'remove_wpme' );
      
      function remove_wpme() {                       
      
          if ( function_exists('shortlink_wp_head') ) {
      
          remove_action('wp_head', 'shortlink_wp_head');
          remove_action('wp', 'shortlink_header');
          remove_filter( 'get_sample_permalink_html', 'get_shortlink_html');
          
          }
      }

    }

register_activation_hook( __FILE__, 's2c_init_options' );
add_action('new_to_publish', 'shorten2ping_published_post');
add_action('draft_to_publish', 'shorten2ping_published_post');
add_action('pending_to_publish', 'shorten2ping_published_post');
add_action('future_to_publish', 'shorten2ping_published_post');
add_action('admin_menu', 'shorten2ping_add_plugin_option');
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'shorten2ping_add_settings_link', -10);
add_action('wp_head', 'short_url_head');
add_action('wp_head', 'fb_thumb_in_head');

?>
