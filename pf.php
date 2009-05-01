<?php
  /*
   Plugin Name: PrintFriendly
   Plugin URI: http://www.printfriendly.com/button
   Description: Creates PrintFriendly.com button for easy printing. [<a href="options-general.php?page=printfriendly/pf.php">Settings</a>].
   Version: 0.2
   Author: Vamsee Kanakala
   Author URI: http://kanakala.net
  */

function pf_show_link($content)
{
  if (is_single() || is_page()) {
    $button_type = get_option('pf_button_type');

    if ($button_type != 'text-only')
      return $content.'<div><script src="http://www.printfriendly.com/javascripts/printfriendly.js" type="text/javascript"></script><a href="http://www.printfriendly.com" onclick="window.print(); return false;" title="Print an optimized version of this web page"><img id="printfriendly" style="border:none;" src="http://www.printfriendly.com/images/'.$button_type.'" alt="Print"/></a></div>';
    else
      return $content.'<script src="http://www.printfriendly.com/javascripts/printfriendly.js" type="text/javascript"></script><a href="http://www.printfriendly.com" id="printfriendly" onclick="window.print(); return false;" title="Print an optimized version of this web page">Print</a>';
  } else {
    return $content;
  }
}

remove_action('the_content', 'pf_show_link');
add_action('the_content', 'pf_show_link', 98);

add_action('admin_menu', 'pf_menu');

function pf_menu() {
  add_options_page('PrintFriendly Options', 'PrintFriendly', 8, __FILE__, 'pf_options');
}

function pf_options() {
  $option_name = 'pf_button_type';
  if (isset($_POST['pf_button_type'])) {
    if (get_option($option_name))
      update_option($option_name, $_POST['pf_button_type']);
    else
      add_option($option_name, 'printfriendly.gif');
?>
    <div class="updated"><p><strong><?php _e('Option saved.'); ?></strong></p></div>
<?php
  }
  $option_value = get_option($option_name);
?>
<div class="wrap">
   <h2>PrintFriendly Options</h2>
   <h3>Choose your button</h3>
   <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <?php wp_nonce_field('update-options'); ?>
    <table cellspacing="20" cellpadding="20">
       <tr valign="top">
       <td><input name="pf_button_type" type="radio" value="printfriendly.gif"
                                                     <?php if ($option_value == 'printfriendly.gif') _e('checked="checked"') ?>/></td>
       <td><img src="http://www.printfriendly.com/images/printfriendly.gif" width="75" height="16" alt="Select This Button Style" /></td>
       </tr>
       <tr valign="top">
       <td><input name="pf_button_type" type="radio" value="printfriendly-med-txt.gif" 
						 <?php if ($option_value == 'printfriendly-med-txt.gif') _e('checked="checked"') ?>/></td>
       <td><img src="http://www.printfriendly.com/images/printfriendly-med-txt.gif" alt="Select this button style" width="76" height="28" /></td>
       </tr>
       <tr valign="top">
       <td><input name="pf_button_type" type="radio" value="printfriendly-nobg.gif" 
						   <?php if ($option_value == 'printfriendly-nobg.gif') _e('checked="checked"') ?>/></td>
       <td><img src="http://www.printfriendly.com/images/printfriendly-nobg.gif" width="75" height="16" alt="Select this button style" /></td>
       </tr>
       <tr>
       <td><input name="pf_button_type" type="radio" value="printfriendly-med.gif" 
						   <?php if ($option_value == 'printfriendly-med.gif') _e('checked="checked"') ?>/></td>
       <td><img src="http://www.printfriendly.com/images/printfriendly-med.gif" width="25" height="29" alt="Select this button style" /></td>
       </tr>
       <tr>
       <td><input name="pf_button_type" type="radio" value="text-only" 
						   <?php if ($option_value == 'text-only') _e('checked="checked"') ?>/></td>
       <td><a href="#" onclick="return false;" style="text-decoration: none;">Print</a> (text only)</td>
       </tr>
    </table>
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
   </form>
</div>
<?php
}
?>
