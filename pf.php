<?php
  /*
   Plugin Name: PrintFriendly
   Plugin URI: http://www.printfriendly.com/button
   Description: Creates PrintFriendly.com button for easy printing. [<a href="options-general.php?page=printfriendly/pf.php">Settings</a>].
   Version: 1.2
   Author: Vamsee Kanakala
   Author URI: http://kanakala.net
  */

function pf_show_link($content)
{
  $button_type = get_option('pf_button_type');
  $show_list = get_option('pf_show_list');

  $plink_url = get_permalink();
  $separator = "?pfstyle=wp";
  if (strpos($plink_url,"?")!=false)
    $separator = "&pfstyle=wp";

  switch ($button_type) {
  case "text-only":
    if (is_single() || is_page()) {
      return $content.'<div id="pfButton"><script src="http://cdn.printfriendly.com/printfriendly.js" type="text/javascript"></script><a href="http://www.printfriendly.com" id="pfLink" onclick="window.print(); return false;" title="Print an optimized version of this web page" style="text-decoration: none;"><span style="color: rgb(85, 117, 12);">Print Friendly</span></a></div>';
    } else {
      if ($show_list != 0)
	return $content;
      else
	return $content.'<div id="pfButton"><a href="'.$plink_url.$separator.'" title="Print an optimized version of this web page" style="text-decoration: none;"><span style="color: rgb(85, 117, 12);">Print Friendly</span></a></div>';
    }
    break;
  case "pf-button-big.gif":
    if (is_single() || is_page()) {
      return $content.'<div id="pfButton"><script src="http://cdn.printfriendly.com/printfriendly.js" type="text/javascript"></script><a href="http://www.printfriendly.com" id="pfLink" onclick="window.print(); return false;" title="Print an optimized version of this web page"><img id="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-button-big.gif" alt="Print"/></a></div>';
    } else {
      if ($show_list != 0)
	return $content;
      else
	return $content.'<div id="pfButton"><a href="'.$plink_url.$separator.'" title="Print an optimized version of this web page"><img id="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-button-big.gif" alt="Print"/></a></div>';
    }
    break;
  case "pf-icon-small.gif":
    if (is_single() || is_page()) {
      return $content.'<div id="pfButton"><script src="http://cdn.printfriendly.com/printfriendly.js" type="text/javascript"></script><a href="http://www.printfriendly.com" id="pfLink" onclick="window.print(); return false;" title="Print an optimized version of this web page" style="text-decoration: none;"><img id="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-icon-small.gif" alt="Print"/><span style="font-size: 12px; color: rgb(85, 117, 12);">Print Friendly</span></a></div>';
    } else {
      if ($show_list != 0)
	return $content;
      else
	return $content.'<div id="pfButton"><a href="'.$plink_url.$separator.'" title="Print an optimized version of this web page" style="text-decoration: none;"><img id="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-icon-small.gif" alt="Print"/><span style="font-size: 12px; color: rgb(85, 117, 12);">Print Friendly</span></a></div>';
    }
    break;
  case "pf-icon.gif":
    if (is_single() || is_page()) {
      return $content.'<div id="pfButton"><script src="http://cdn.printfriendly.com/printfriendly.js" type="text/javascript"></script><a href="http://www.printfriendly.com" id="pfLink" onclick="window.print(); return false;" title="Print an optimized version of this web page" style="text-decoration: none;"><img id="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-icon.gif" alt="Print"/><span style="font-size: 15px; color: rgb(85, 117, 12);">Print Friendly</span></a></div>';
    } else {
      if ($show_list != 0)
	return $content;
      else
	return $content.'<div id="pfButton"><a href="'.$plink_url.$separator.'" title="Print an optimized version of this web page" style="text-decoration: none;"><img id="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-icon.gif" alt="Print"/><span style="font-size: 15px; color: rgb(85, 117, 12);">Print Friendly</span></a></div>';
    }
    break;
  default:
    if (is_single() || is_page()) {
      return $content.'<div id="pfButton"><script src="http://cdn.printfriendly.com/printfriendly.js" type="text/javascript"></script><a href="http://www.printfriendly.com" id="pfLink" onclick="window.print(); return false;" title="Print an optimized version of this web page"><img id="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-button.gif" alt="Print"/></a></div>';
    } else {
      if ($show_list != 0)
	return $content;
      else
	return $content.'<div id="pfButton"><a href="'.$plink_url.$separator.'" title="Print an optimized version of this web page"><img id="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-button.gif" alt="Print"/></a></div>';
    }
  }  
}

remove_action('the_content', 'pf_show_link');
add_action('the_content', 'pf_show_link', 98);

add_action('admin_menu', 'pf_menu');

function pf_head() {
  if (is_single() || is_page()) {
  ?>
    <?php if ($_GET["pfstyle"] == "wp") { ?>
      <script type="text/javascript">var pfstyle = 'bk';</script>
    <?php } ?>
      <link rel="stylesheet" href="http://cdn.printfriendly.com/printfriendly.css" type="text/css" />
    <?php
  }
}

add_action('wp_head', 'pf_head');

function pf_menu() {
  add_options_page('PrintFriendly Options', 'PrintFriendly', 8, __FILE__, 'pf_options');
}

function pf_options() {
  $option_name = 'pf_button_type';
  $option2_name = 'pf_show_list';

  if (get_option($option_name)) {
    if (isset($_POST[$option_name])) {
      update_option($option_name, $_POST[$option_name]);
      if (isset($_POST[$option2_name]))
	update_option($option2_name, 1);
      else
	update_option($option2_name, 0);
    ?>
      <div class="updated"><p><strong><?php _e('Option saved.'); ?></strong></p></div>
    <?php
    }
  } else {
    add_option($option_name, 'pf-button.gif');
    add_option($option2_name, 0);
  }
  $option_value = get_option($option_name);
  $option2_value = get_option($option2_name);
?>
<div class="wrap">
   <h2>PrintFriendly Options</h2>
   <h3>Choose your button</h3>
   <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <?php wp_nonce_field('update-options'); ?>
    <table cellspacing="20" cellpadding="20">
       <tr valign="top">
       <td><input name="pf_button_type" type="radio" value="pf-button.gif"
                                                     <?php if ($option_value == 'pf-button.gif') _e('checked="checked"') ?>/></td>
       <td><img src="http://cdn.printfriendly.com/pf-button.gif" alt="Select This Button Style" /></td>
       </tr>
       <tr valign="top">
       <td><input name="pf_button_type" type="radio" value="pf-button-big.gif" 
						 <?php if ($option_value == 'pf-button-big.gif') _e('checked="checked"') ?>/></td>
       <td><img src="http://cdn.printfriendly.com/pf-button-big.gif" alt="Select this button style" /></td>
       </tr>
       <tr valign="top">
       <td><input name="pf_button_type" type="radio" value="pf-icon-small.gif" 
						   <?php if ($option_value == 'pf-icon-small.gif') _e('checked="checked"') ?>/></td>
       <td>
          <img src="http://cdn.printfriendly.com/pf-icon-small.gif" alt="Select this button style" />
          <span style="font-size: 12px; color: rgb(85, 117, 12);">Print Friendly</span>
       </td>
       </tr>
       <tr>
       <td><input name="pf_button_type" type="radio" value="pf-icon.gif" 
						   <?php if ($option_value == 'pf-icon.gif') _e('checked="checked"') ?>/></td>
       <td>
          <img src="http://cdn.printfriendly.com/pf-icon.gif" alt="Select this button style" />
          <span style="font-size: 15px; color: rgb(85, 117, 12);">Print Friendly</span>          
       </td>
       </tr>
       <tr>
       <td><input name="pf_button_type" type="radio" value="text-only" <?php if ($option_value == 'text-only') _e('checked="checked"') ?>/></td>
       <td>
           <a href="#" onclick="return false;" style="text-decoration: none;">
           <span style="color: rgb(85, 117, 12);">Print Friendly</span></a> (text-only)
       </td>
       </tr>
    </table>
    <h3>Display Choices</h3>
    <table cellspacing="20" cellpadding="20">
       <tr valign="top">
       <td>Show buttons only on individual posts and pages (not on frontpage/listings)</td>
       <td><input name="pf_show_list" type="checkbox" value=<?php echo $option2_value ?>
								 <?php if ($option2_value == 1) _e('checked="checked"') ?>/></td>
       </tr>
    </table>																 
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
   </form>
</div>
<?php
}
?>
