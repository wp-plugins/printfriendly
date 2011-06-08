<?php
  /*
   Plugin Name: Print Friendly and PDF
   Plugin URI: http://www.printfriendly.com
   Description: PrintFriendly & PDF optimizes your pages for print. Help your readers save paper and ink, plus enjoy your content in printed form. Website
   Name and URL are included to ensure repeat visitors and new visitors when printed versions are shared. [<a href="options-general.php?page=printfriendly/pf.php">Settings</a>]  
   Developed by <a href="http://printfriendly.com" target="_blank">PrintFriendly</a>
   Version: 2.1.7
   Author: Print Friendly
   Author URI: http://www.PrintFriendly.com

   Changelog :
	2.1.7 - Changed button from span to div to support floating.
	2.1.6 - Added rel="nofollow" to links. Changed button from <a> to <span> to fix target_new or target_blank issues.
	2.1.5 - Fix conflict with link tracking plugins. Custom image support for hosted wordpress sites.
	2.1.4 - wp head fix.
	2.1.3 - Manual option for button placement. Security updates for multi-author sites.
	2.1.2 - Improvements to Setting page layout and PrintFriendly button launching from post pages.
	2.1.1 - Fixed admin settings bug.
    2.1 - Update for mult-author websites. Improvements to Settings page.
    2.0 - Customize the style, placement, and pages your printfriendly button appears.
    1.5 - Added developer ability to disable hook and use the pf_show_link() function to better be used in a custom theme & Uninstall cleanup.
    1.4 - Changed Name.
    1.3 - Added new buttons, removed redundant code.
    1.2 - User can choose to show or not show buttons on the listing page.

  */
  
////////////////////////////// Wordpress hooks

// add the settings page
add_action('admin_menu', 'pf_menu');
function pf_menu() {
if(current_user_can('manage_options'))
	add_options_page('PrintFriendly Options', 'PrintFriendly', 'publish_posts', 'printfriendly', 'pf_options');
}

// add the settings link to the plugin page

function pf_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=printfriendly">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

// add css, js, and check for updates
if (isset($_GET['page']) && $_GET['page'] == 'printfriendly') 
{
	add_action('admin_print_scripts', 'pf_admin_scripts');
	add_action('admin_print_styles', 'pf_admin_styles');
	add_action('admin_head', 'pf_css_in_admin_head');
	if(get_option('pf_text_color')==null)
	{
		//old install or something is fishy! lets run the install!
		printfriendly_activation_handler();
	}
}

$pf_pluginbase = plugin_basename(__FILE__); 
add_filter('plugin_action_links_'.$pf_pluginbase, 'pf_settings_link');

// automaticaly add the link
if(get_option('pf_show_list')!= 'manual')
	add_action('the_content', 'pf_show_link');

// lets start our mess!
function printfriendly_activation_handler(){
	update_option('pf_margin_top',0);
	update_option('pf_margin_right',0);
	update_option('pf_margin_bottom',0);
	update_option('pf_margin_left',0);
	update_option('pf_text_color','#55750C');
	update_option('pf_text_size','14');

	if(get_option('pf_show_list')==0)
	{
		update_option('pf_show_list','all');
	}
	elseif(get_option('pf_show_list')==1)
	{
		update_option('pf_show_list','single');
	}
	elseif(get_option('pf_disable_prepend')==1)
	{
		update_option('pf_show_list','manual');
	}
	delete_option('pf_disable_prepend');
}

// lets clean our mess when we are done and properly 
function printfriendly_deactivation_handler(){
	delete_option('pf_button_type');	
	delete_option('pf_custom_text');
	
	delete_option('pf_custom_image');
	
	delete_option('pf_show_list');
	delete_option('pf_content_placement');
	delete_option('pf_content_position');
	
	delete_option('pf_margin_top');
	delete_option('pf_margin_right');
	delete_option('pf_margin_bottom');
	delete_option('pf_margin_left');
}
register_activation_hook(__FILE__, 'printfriendly_activation_handler');
register_uninstall_hook(__FILE__, 'printfriendly_deactivation_handler');

// add CSS into admin panel
function pf_css_in_admin_head() {
	$url = plugins_url().'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/admin.css';
	echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}

////////////////////////////// Admin Settings

function pf_options(){	
	include_once('pf_admin.php');
}

// setup for uploads
function pf_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('pf-color-picker', plugins_url().'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/colorpicker.js', array('jquery','media-upload','thickbox'));
	wp_register_script('pf-admin-js', plugins_url().'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/admin.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('pf-color-picker');
	wp_enqueue_script('pf-admin-js');
}
function pf_admin_styles() {
	wp_enqueue_style('thickbox');
}

////////////////////////////// Where all the magic happens

function pf_radio($name){
	$var = '<input name="pf_button_type" type="radio" value="'.$name.'"'; if( get_option('pf_button_type') == $name || ($name=="pf-button.gif" && get_option('pf_button_type')==null) ){$var .= ' checked="checked"'; } $var .= '/>';
	return $var.pf_button($name);
}

function pf_button($name=false){
	if($name==false){
		$name = get_option('pf_button_type');
		//null
		if($name==null){
			$name='pf-button.gif';
		}
	}
	$text = get_option('pf_custom_text');
	if( $text == null){
		$text = 'Print Friendly';
	}
	switch($name){		
		case "custom-image":
			$custom_image = get_option('pf_custom_image');
			if($custom_image==null){
				return '';
			}
			$return = '<img class="printfriendly" src="'.$custom_image.'" />';
			if( get_option('pf_custom_text') != null){
				$return .='<div class="printfriendly" style="font-size:'.get_option('pf_text_size').'px; margin-left:3px;  color: '.get_option('pf_text_color').';">'.$text.'</div>';
			}
			return $return;
		break;
		
		case "text-only":
			return '<span class="printfriendly" style="font-size: '.get_option('pf_text_size').'px; margin-left:3px; color: '.get_option('pf_text_color').';">'.$text.'</span>';
		break;
		
		case "pf-icon-both.gif":
			return '<img class="printfriendly" style="border:none; padding:0;" src="http://cdn.printfriendly.com/pf-print-icon.gif" alt="Print Friendly"/><span class="printandpdf" style="font-size:'.get_option('pf_text_size').'px; margin-left:3px; color:'.get_option('pf_text_color').';"> Print <img style="border:none;"  src="http://cdn.printfriendly.com/pf-pdf-icon.gif" alt="Get a PDF version of this webpage" /> PDF </span>';
		break;
		
		case "pf-icon-small.gif":
		case "pf-icon.gif":
			$size = get_option('pf_text_size');
			if($name=="pf-icon.gif" && $size==12){
				$size= 'font-size:'.($size+3).'px; ';
			}else{
				$size ='font-size:'.$size.'px; ';
			}
			return '<img class="printfriendly" src="http://cdn.printfriendly.com/'.$name.'" alt="Print Friendly"/><span style="'.$size.'margin-left:3px; color: '.get_option('pf_text_color').';">'.$text.'</span>';
		break;
		
		case "pf-button.gif":
		case "pf-button-big.gif":
		case "pf-button-both.gif":
			return '<img class="printfriendly" src="http://cdn.printfriendly.com/'.$name.'" alt="PrintFriendly" />';
		break;
	}
}

function pf_margin_down($dir){
	$margin = get_option('pf_margin_'.$dir);
	if($margin == null){
		return '0';
	}else{
		return $margin;
	}
}

// add button 
function pf_show_link($content=false)
{
	$pf_display = get_option('pf_show_list');
	if(!$content && $pf_display!= 'manual')
		return "";
	$plink_url = get_permalink();
	
	$separator = "?pfstyle=wp";
		if (strpos($plink_url,"?")!=false)
		$separator = "&pfstyle=wp";

	$style=' text-align:';
	$pos = get_option('pf_content_position');
		if($pos==null){$pos='left';}
	$style.=$pos.';';	
	$style.=' margin: '.pf_margin_down('top').'px '.pf_margin_down('right').'px '.pf_margin_down('bottom').'px '.pf_margin_down('left').'px;';
	
	
	$button = '<script src="http://cdn.printfriendly.com/printfriendly.js" type="text/javascript"></script><div onclick="window.print(); return false;" style="'.$style.' text-decoration: none; outline: none; color: '.get_option('pf_text_color').'; cursor:pointer;">'.pf_button().'</div>';
	
	$button_link = '<div style="'.$style.'"><a href="'.$plink_url.$separator.'" rel="nofollow" style="text-decoration: none; outline: none; color: '.get_option('pf_text_color').';">'.pf_button().'</a></div>';
	
	//This goes on article pages
	if((is_single() || is_page()) && $pf_display=='single' || (is_single() && $pf_display=='posts')) 
	{
			if (get_option('pf_content_placement')==null)
				return $content.$button;	
			
			else 
				return $button.$content; 
	}
	else if((is_single() || is_page()) && $pf_display=='manual') 		
		{
				return  $button; 
		}
		
	else if((is_single() || is_page()) && $pf_display=='all')
		{
			if (get_option('pf_content_placement')==null)
				return $content.$button;
			
			else
				return $button.$content;
		}
	
	//This goes on homepage
	else 	
		{
			if($pf_display == 'manual')
				return $button_link;
			else if ($pf_display == 'single' || $pf_display == 'posts')
				return $content;				
			else if (get_option('pf_content_placement')==null)
				return $content.$button_link;
			
			else
				return $button_link.$content;
		}

}