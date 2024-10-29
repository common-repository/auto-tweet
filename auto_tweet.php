<?php
/*
Plugin Name: Auto Tweet
Plugin URI: http://feastofcrumbs.com/blog/wordpress-plugins/auto-tweet/
Description: Submit posts or excerpts to Twitter automatically.
Version: 2.1
Author: Joefish
Author URI: http://feastofcrumbs.com/
*/

// Props to Paul Stamatiou ( http://paulstamatiou.com/ ), Alex King ( http://alexking.org/ ), Fil Fortes ( http://fortes.com/ ) and Mark Jaquith ( http://txfx.com/ ) for ideas
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// ********************************************************************** 

function auto_tweet_install () {
	global $wpdb;
  add_option('auto_tweet_options', array('username' => 'username', 'password' => 'password', 'i_replacement' => "\"", 'b_replacement' => '*', 'checkdefault' => 'N'));
  $columns = $wpdb->get_col("DESC $wpdb->posts");
	foreach ($columns as $column)
		if ($column == 'tweeted')
			return true;
	$wpdb->query("ALTER TABLE $wpdb->posts ADD COLUMN tweeted enum('Y','N') NOT NULL default 'N'");
}

function auto_tweet_admin_menu() {
  add_options_page('Auto Tweet options', 'Auto Tweet', 10, __FILE__, 'auto_tweet_options_page');
}

function auto_tweet_options_page() { ?>
<?php if ($_POST) {
  $ireplace = str_replace("&quot;","\"",$_POST['auto_tweet_ireplace_box']);
  $breplace = str_replace("&quot;","\"",$_POST['auto_tweet_breplace_box']);
  if ($_POST['auto_tweet_password_box'] == "********") {
    $comparison = get_option("auto_tweet_options");
    $password = $comparison['password'];
  } else {
    $password = $_POST['auto_tweet_password_box'];
  }
  if ($_POST['checkdefault']) {
    $checkdefault = 'Y';
  } else {
    $checkdefault = 'N';
  }
  update_option('auto_tweet_options', array("username" => $_POST['auto_tweet_username_box'], "password" => $password, "i_replacement" => $ireplace, "b_replacement" => $breplace, "checkdefault" => $checkdefault)); ?>
<div id="message" class="updated fade"><p>Options updated.</p></div>
<?php }
  $options = get_option("auto_tweet_options");
?>

<div class="wrap">
  <h2>Auto Tweet options</h2>
    <p>Auto Tweet adds a check box to the Write Post page. If that box is checked when you publish a post, the contents of that post (up to 140 characters) will also be sent to Twitter as a tweet.</p>
    <form action="" method="post" id="auto_tweet_form">
      <h3>Twitter user</h3>
      <p>Before you can use Auto Tweet, you must specify a valid Twitter username and password in the fields below.</p>
      <p><input id="auto_tweet_username_box" name="auto_tweet_username_box" type="text" size="30" value="<?php if ($options['username'] !== 'username') echo $options['username']; ?>" style="font-family: 'Courier New', Courier, Mono; font-size: 1.25em;" /> <label for="auto_tweet_username_box">Username</label><br />
      <input id="auto_tweet_password_box" name="auto_tweet_password_box" type="text" size="30" value="<?php if ($options['password'] !== 'password') echo "********"; ?>" style="font-family: 'Courier New', Courier, Mono; font-size: 1.25em;" /> <label for="auto_tweet_password_box">Password</label></p>
      <h3>Text transformation</h3>
      <p>Twitter does not render HTML. If you use &lt;i&gt;, &lt;em&gt;, &lt;b&gt; and &lt;strong&gt; tags in your tweets, they will be transformed according to what you specify here. With the default options, what appears in WordPress as <code>This is <i>italics</i> and this is <b>bold</b></code> becomes <code>This is "italics" and this is *bold*</code> on Twitter. Submitting blank fields for these options will cause Auto Tweet to strip italics and bold tags when other HTML is stripped.</p>
      <p><input id="auto_tweet_ireplace_box" name="auto_tweet_ireplace_box" type="text" size="30" value="<?php echo str_replace("\"","&quot;",stripslashes($options['i_replacement'])); ?>" style="font-family: 'Courier New', Courier, Mono; font-size: 1.25em;" /> <label for="auto_tweet_ireplace_box">Italics replacement</label><br />
      <input id="auto_tweet_breplace_box" name="auto_tweet_breplace_box" type="text" size="30" value="<?php echo str_replace("\"","&quot;",stripslashes($options['b_replacement'])); ?>" style="font-family: 'Courier New', Courier, Mono; font-size: 1.25em;" /> <label for="auto_tweet_breplace_box">Bold replacement</label>
      </p>
      <h3>Checkbox</h3>
      <p>Should the Auto Tweet checkbox on the Write Post form be checked by default?</p>
      <p><input id="checkdefault" name="checkdefault" type="checkbox"<?php if ($options['checkdefault'] == "Y") echo ' checked="checked"'; ?> /> <label for="checkdefault">Yes, checked by default.</label></p>
      <p class="submit" style="text-align: left;"><input type="submit" name="submit" value="Update Auto Tweet &raquo;" /></p>
    </form>
</div>
<?php }

function auto_tweet_form() {
  global $wpdb, $post_ID;
  $tweeted = $wpdb->get_results("SELECT tweeted FROM $wpdb->posts WHERE ID = '$post_ID' AND tweeted = 'Y'");
  $options = get_option('auto_tweet_options'); 
  echo '<fieldset id="auto_tweet_block" class="dbx-box" style="margin-bottom: .5em">' . "\n";
  echo '  <h3 class="dbx-handle">Auto tweet</h3>' . "\n";
  echo '  <div class="dbx-content">' . "\n";
  if ($tweeted) {
    echo '    <p>This post has already been submitted to Twitter</p>' . "\n";
  } elseif ($options['username'] == 'username' || $options['username'] == '' || $options['password'] == 'password' || $options['password'] == '') {
    echo '    <p>You have not yet entered a Twitter username or password. You must enter this information in the <a href="';
    bloginfo('wpurl');
    echo '/wp-admin/options-general.php?page=auto_tweet.php">options page</a> before you can use Auto Tweet.</p>' . "\n";
  } else {
    echo '    <p style="line-height: 1.2em; margin-top: .5em; margin-bottom: 0;"><input type="checkbox" name="auto_tweet_checkbox" id="auto_tweet_checkbox"';
    if ($options['checkdefault'] == 'Y') {
      echo ' checked="checked"';
    }
    echo ' onclick="if (this.checked) { document.getElementById(\'counter\').style.display=\'inline\'; } else { document.getElementById(\'counter\').style.display=\'none\'; }" />' . "\n";
    echo '    <label for="auto_tweet_checkbox">Submit to Twitter</label>' . "\n";
    echo '    <span id="counter" style="display: none; color: #ff0000;">&nbsp;</span></p>' . "\n";
    echo '    <script type="text/javascript">' . "\n";
    echo '      // <![CDATA[' . "\n";
    echo '      function countchar(char) {' . "\n";
    echo '        var counter = document.getElementById(\'counter\');' . "\n";
    echo '        var re1 = /<i>|<\/i>|<em>|<\/em>/gi;' . "\n";
    echo '        var re2 = /<b>|<\/b>|<strong>|<\/strong>/gi;' . "\n";
    echo '        var re3 = /(<([^>]+)>)/gi;' . "\n";
    echo '        linkcount = 0;' . "\n";
    echo '        pos = char.indexOf("<a ");' . "\n";
    echo '        while ( pos != -1 ) {' . "\n";
    echo '          linkcount++;' . "\n";
    echo '          pos = char.indexOf("<a ",pos+1);' . "\n";
    echo '        }' . "\n";
    echo '        char = char.replace(re1, "' . $options['i_replacement'] . '");' . "\n";
    echo '        char = char.replace(re2, "' . $options['b_replacement'] . '");' . "\n";
    echo '        char = char.replace(re3, "");' . "\n";
    echo '        var totalchar = char.length;' . "\n";
    echo '        if (linkcount > 0) {' . "\n";
    echo '          var i = 1;' . "\n";
    echo '          while (i <= linkcount) {' . "\n";
    echo '            totalchar = totalchar + 30;' . "\n";
    echo '            i++;' . "\n";
    echo '          }' . "\n";
    echo '        }' . "\n";
    echo '        if (totalchar > 140) {' . "\n";
    echo '          counter.innerHTML=\' (\' + totalchar + \' characters exceeds the maximum length of 140 characters. This entry will be truncated.)\';' . "\n";
    echo '        }' . "\n";
    echo '        if (totalchar <= 140) {' . "\n";
    echo '          counter.innerHTML=\'&nbsp;\';' . "\n";
    echo '        }' . "\n";
    echo '      }' . "\n";
    echo '      var content = document.getElementById("content");' . "\n";
    echo '      content.setAttribute("onkeyup", "countchar(this.value);");' . "\n";
    echo '      // ]]>' . "\n";
    echo '    </script>' . "\n";
  }
  echo '  </div>' . "\n";
  echo '</fieldset>' . "\n";
}

function auto_tweet_submit($post_ID) {
  global $wpdb;
  if ($_POST['auto_tweet_checkbox']) {
    $options = get_option('auto_tweet_options');
    $username = $options['username'];
    $password = $options['password'];
    $re = "/<a href=\"(.*?)\">(.*?)<\\/a>/i";
    $re2 = "/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU";
    $content = stripslashes($_POST['post_content']);
    $content = preg_replace($re, '$2 ( $1 )', $content);
    if (preg_match_all($re2, stripslashes($_POST['post_content']), $matches, PREG_SET_ORDER)) {
      foreach($matches as $match) {
        $tinyurl = file_get_contents("http://tinyurl.com/api-create.php?url=" . $match[2]);
        $content = str_replace(" " . $match[2] . " "," " . $tinyurl . " ",$content);
      }
    }
    $search = array('/<i>|<\/i>|<em>|<\/em>/','/<b>|<\/b>|<strong>|<\/strong>/','/(<([^>]+)>)/');
    $replace = array(stripslashes($options['i_replacement']),stripslashes($options['b_replacement']),"");
    $content = preg_replace($search,$replace,$content);
    if (strlen($content) > '140') $content = substr($content, 0, 137) . '...';
    $headers = array("X-Twitter-Client" => "Auto Tweet", "X-Twitter-Client-Version" => "2.1", "X-Twitter-Client-URL" => "http://feastofcrumbs.com/downloads/wp-plugins/auto-tweet/auto-tweet.xml");
    $curl_handle = curl_init();
    curl_setopt($curl_handle,CURLOPT_URL,"http://twitter.com/statuses/update.xml");
    curl_setopt($curl_handle,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl_handle,CURLOPT_POST,1);
    curl_setopt($curl_handle,CURLOPT_POSTFIELDS,"status=$content&source=autotweet");
    curl_setopt($curl_handle,CURLOPT_USERPWD,"$username:$password");
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);
    $update = $wpdb->query("UPDATE $wpdb->posts SET tweeted = 'Y' WHERE ID = '$post_ID'");
  }
}

add_action('activate_auto_tweet.php', 'auto_tweet_install');
add_action('admin_menu', 'auto_tweet_admin_menu');
add_action('simple_edit_form', 'auto_tweet_form', 1);
add_action('edit_form_advanced', 'auto_tweet_form', 1);
add_action('publish_post', 'auto_tweet_submit');

?>