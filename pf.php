<?php

/*
Plugin Name: Print Friendly and PDF
Plugin URI: http://www.printfriendly.com
Description: PrintFriendly & PDF button for your website. Optimizes your pages and brand for print, pdf, and email.
Name and URL are included to ensure repeat visitors and new visitors when printed versions are shared.
Version: 3.4.5
Author: Print Friendly
Author URI: http://www.PrintFriendly.com

Changelog :
3.4.4 - Removed page content selection option - Wordpress Standard/Strict
3.4.2 - Fixed the issue occured due to new changes made in the page content selection options
3.4.1 - Improved page content selection options
3.4.0 - Fixed the admin javascript error and increased plugin text boxes size
3.3.10 - Implemented both Classic Google Analytics and Google Universal Analytics code.
3.3.9 - Removed the functionality that opens new window when JavaScript is disabled.
3.3.8 - Shortcode Bug fix, urlencode button href
3.3.7 - Readme.txt update
3.3.6 - Fixed JS optimization Bug
3.3.5 - Wordpress 3.8 support
3.3.4 - Provided Algorithm Options
3.3.3 - Using WP content hook for all Buttons
3.3.2 - Algorithm update
3.3.1 - SSL support issue. 
3.3.0 - Printfriendly custom commands support and PF Algo V6 release.
3.2.10 - Fixed Bug. 
3.2.9 - Added Support for Google Analytics
3.2.8 - Algorithm Update
3.2.7 - Removed Break tag from button code.
3.2.6 - Fixed Button behavior when displayed on Homepage for NON-JS version. Fixed CSS issue with Button when placed above content. Fixed box-shadow issue with button. Custom print and pdf options now available for Non-JS version (custom header, custom css, image alignment, etc.). Fixed custom header bug.
3.2.5 - Added hide images and image style options. Improved input validation. Improved output escaping. Removed printfriendly post_class. Small i8n fix. Few small HTML fixes.
3.2.4 - Add printfriendly post_class. Fixed minor JS bug. Added redundancy to uninstall script.
3.2.3 - Rolling back to version 3.2.1
3.2.2 - Add printfriendly post_class. Add printfriendly button display settings per individual category. Fixed minor JS bug. Added redundancy to uninstall script.
3.2.1 - Improve script loading.
3.2.0 - Important chrome issue fix. Ie syntax error fix.
3.1.9 - Minor css detail.
3.1.8 - Add printfriendly options to allow/not allow print, pdf, email from the Printfriendly and PDF dialog.
3.1.7 - Revert default print button show settings. Prevent easy override of print button text-decoration and border style properties.
3.1.6 - Adding PrintFriendly and PDF alignment style classes.
3.1.5 - Set button appearance in more flexible way. Remove styles that interfered with wordpress themes. Add shortcode for printfriendly button. Fix redirect to printfriendly.com link. Added custom css feature.
3.1.4 - Changed https url. Don't hide text change box when disabling css.
3.1.3 - Fixed bug with disable css option
3.1.2 - Added disable css option to admin settings.
3.1.1 - Fixed admin js caching.
3.1.0 - Fixed admin css caching.
3.0.9 - New features: Custom header, disable click-to-delete, https support (beta), PrintFriendly Pro (ad-free).
3.0.8 - Reordered PrintFriendly & PDF buttons. CSS stylesheet option is now checked by default.
3.0.7 - Added additional images for print button.
3.0.6 - Fix bug that would display button on category pages when not wanted.
3.0.5 - Include button on category pages if user has selected "All pages".
3.0.4 - Align-right and align-center support for themes that remove WordPress core css.
3.0.3 - Support for bad themes that alter template tags and prevent JavaScript from loading in footer.
3.0.2 - Fixed JS bug with Google Chrome not submitting and fixed input validation issues.
3.0.1 - Fixed minor JS bug.
3.0 - Complete overhaul of the plugin by Joost de Valk.
2.1.8 - The Print Button was showing up on printed, or PDF, pages. Junk! Print or PDF button no longer displayed on printed out page or PDF.
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

/**
 * PrintFriendly WordPress plugin. Allows easy embedding of printfriendly.com buttons.
 * @package PrintFriendly_WordPress
 * @author PrintFriendly <support@printfriendly.com>
 * @copyright Copyright (C) 2012, PrintFriendly
 */
if ( ! class_exists( 'PrintFriendly_WordPress' ) ) {

  /**
   * Class containing all the plugins functionality.
   * @package PrintFriendly_WordPress
   */
  class PrintFriendly_WordPress {
    /**
     * The hook, used for text domain as well as hooks on pages and in get requests for admin.
     * @var string
     */
    var $hook = 'printfriendly';

    /**
     * The option name, used throughout to refer to the plugins option and option group.
     * @var string
     */
    var $option_name = 'printfriendly_option';

    /**
     * The plugins options, loaded on init containing all the plugins settings.
     * @var array
     */
    var $options = array();

    /**
     * Database version, used to allow for easy upgrades to / additions in plugin options between plugin versions.
     * @var int
     */
    var $db_version = 9;

    /**
     * Settings page, used within the plugin to reliably load the plugins admin JS and CSS files only on the admin page.
     * @var string
     */
    var $settings_page = '';

    /**
     * Constructor
     *
     * @since 3.0
     */
    function __construct() {
      // delete_option( $this->option_name );

      // Retrieve the plugin options
      $this->options = get_option( $this->option_name );

      // If the options array is empty, set defaults
      if ( ! is_array( $this->options ) )
        $this->set_defaults();

      /**
       * Set page content selection option "Wordpress Standard/Strict" to "WP Template"        
       */
      if( isset( $this->options['pf_algo'] ) && $this->options['pf_algo'] == 'ws' ){
        $this->options['pf_algo'] = 'wp';
        update_option( $this->option_name, $this->options );
      }

      // If the version number doesn't match, upgrade
      if ( $this->db_version > $this->options['db_version'] )
        $this->upgrade();

      add_action( 'wp_head', array( &$this, 'front_head' ) );
      // automaticaly add the link
      if( !$this->is_manual() ) {
        add_filter( 'the_content', array( &$this, 'show_link' ) );
        add_filter( 'the_excerpt', array( &$this, 'show_link' ) );
      }
    
      add_action('the_content', array(&$this, 'add_pf_content_class_around_content_hook'));

      if ( is_admin() ) {
        // Hook into init for registration of the option and the language files
        add_action( 'admin_init', array( &$this, 'init' ) );

        // Register the settings page
        add_action( 'admin_menu', array( &$this, 'add_config_page' ) );

        // Register the contextual help
        add_filter( 'contextual_help', array( &$this, 'contextual_help' ), 10, 2 );

        // Enqueue the needed scripts and styles
        add_action( 'admin_enqueue_scripts',array( &$this, 'admin_enqueue_scripts' ) );

        // Register a link to the settings page on the plugins overview page
        add_filter( 'plugin_action_links', array( &$this, 'filter_plugin_actions' ), 10, 2 );
    }
    }

  /**
  * Adds wraps content in pf-content class to help Printfriendly algo determine the content
  * 
  * @since 3.2.8
  *
  **/
  function add_pf_content_class_around_content_hook($content = false) {

    if( isset($this->options['pf_algo']) &&
	$content && 
	$this->options['pf_algo'] == 'wp' &&
       !$this->print_only_override($content) ) {
        add_action( 'wp_footer', array( &$this, 'print_script_footer' ));
        return '<div class="pf-content">'.$content.'</div>';
    } else {
        return $content;
    }
  }

  /**
  *  Override to check if print-only command is being used 
  *
  *  @since 3.3.0
  **/
  function print_only_override($content) {
    $pattern = '/class=[\"]print-only|class=[\']print-only|print-only/';
    $pf_pattern = '/class=[\"]pf-content|class=[\']pf-content|pf-content/';
    return (preg_match($pattern, $content) || preg_match($pf_pattern, $content)) ;
  }
  
    /**
     * PHP 4 Compatible Constructor
     *
     * @since 3.0
     */
    function PrintFriendly_WordPress() {
      $this->__construct();
    }

    /**
     * Prints the PrintFriendly button CSS, in the header.
     *
     * @since 3.0
     */
    function front_head() {

      if ( isset( $this->options['enable_css'] ) && $this->options['enable_css'] != 'on' )
        return;


?>
        <style type="text/css" media="screen">
          div.printfriendly {
            margin: <?php echo $this->options['margin_top'].'px '.$this->options['margin_right'].'px '.$this->options['margin_bottom'].'px '.$this->options['margin_left'].'px'; ?>;
          }
          div.printfriendly a, div.printfriendly a:link, div.printfriendly a:visited {
            text-decoration: none;
            font-size: <?php echo $this->options['text_size']; ?>px;
            color: <?php echo $this->options['text_color']; ?>;
            vertical-align: bottom;
            border: none;
          }

          .printfriendly a:hover {
            cursor: pointer;
          }

          .printfriendly a img  {
            border: none;
            padding:0;
            margin-right: 6px;
            display:inline-block;
            box-shadow: none;
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
          }
          .printfriendly a span{
            vertical-align: bottom;
          }
          .pf-alignleft {
            float: left;
          }
          .pf-alignright {
            float: right;
          }
          div.pf-aligncenter {
            display: block;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
          }
        </style>
        <style type="text/css" media="print">
          .printfriendly {
            display: none;
          }
        </style>
<?php
    }



    /**
     * Prints the PrintFriendly JavaScript, in the footer, and loads it asynchronously.
     *
     * @since 3.0
     */
    function print_script_footer() {
      if (isset($this->options['javascript']) && $this->options['javascript'] == 'no')
        return;

      else {
        $tagline = $this->options['tagline'];
        $image_url = $this->options['image_url'];
        if( $this->options['logo'] == 'favicon' ) {
          $tagline = '';
          $image_url = '';
        }

        // Currently we use v3 for both: normal and password protected sites


?>
      <script type="text/javascript">

          var pfHeaderImgUrl = '<?php echo esc_js(esc_url_raw($image_url)); ?>';
          var pfHeaderTagline = '<?php echo esc_js($tagline); ?>';
          var pfdisableClickToDel = '<?php echo esc_js($this->options['click_to_delete']); ?>';
          var pfHideImages = '<?php echo esc_js($this->options['hide-images']); ?>';
          var pfImageDisplayStyle = '<?php echo esc_js($this->options['image-style']); ?>';
          var pfDisableEmail = '<?php echo esc_js($this->options['email']); ?>';
          var pfDisablePDF = '<?php echo esc_js($this->options['pdf']); ?>';
          var pfDisablePrint = '<?php echo esc_js($this->options['print']); ?>';
          var pfCustomCSS = '<?php echo esc_js($this->options['custom_css_url']); ?>';
      (function() {
            var e = document.createElement('script'); e.type="text/javascript";
        if('https:' == document.location.protocol) {
        js='https://pf-cdn.printfriendly.com/ssl/main.js';
        }
        else{
        js='http://cdn.printfriendly.com/printfriendly.js';
        }
            e.src = js;
            document.getElementsByTagName('head')[0].appendChild(e);
        })();
      </script>
<?php
      }
    }

    /**
     * Primary frontend function, used either as a filter for the_content, or directly using pf_show_link
     *
     * @since 3.0
     * @param string $content the content of the post, when the function is used as a filter
     * @return string $button or $content with the button added to the content when appropriate, just the content when button shouldn't be added or just button when called manually.
     */
    function show_link( $content = false ) {
      $is_manual = $this->is_manual();

      if( !$content && !$is_manual )
        return "";

      $button = $this->getButton();
      if ( $is_manual )
      {
        // Hook the script call now, so it only get's loaded when needed, and need is determined by the user calling pf_button
        add_action( 'wp_footer', array( &$this, 'print_script_footer' ) );
        return $button;
      }
    
      else
      {
        if ( (is_page() && ( isset($this->options['show_on_pages']) && 'on' === $this->options['show_on_pages'] ) )
          || (is_home() && ( ( isset($this->options['show_on_homepage']) && 'on' === $this->options['show_on_homepage'] ) && $this->category_included() ) )
          || (is_tax() && ( ( isset($this->options['show_on_taxonomies']) && 'on' === $this->options['show_on_taxonomies'] ) && $this->category_included() ) )
          || (is_category() && ( ( isset($this->options['show_on_categories']) && 'on' === $this->options['show_on_categories'] ) && $this->category_included() ) )
          || (is_single() && ( ( isset($this->options['show_on_posts']) && 'on' === $this->options['show_on_posts'] ) && $this->category_included() ) ) )
        {
          // Hook the script call now, so it only get's loaded when needed, and need is determined by the user calling pf_button
          add_action( 'wp_footer', array( &$this, 'print_script_footer' ) );

          if ( $this->options['content_placement'] == 'before' )
            return $button.$content;
          else
            return $content.$button;
        }
        else
        {
          return $content;
        }

      }

    }
  

  /**
  * @since 3.3.8
  * @returns Printfriendly Button HTML
  */
  
  function getButton($add_footer_script = false) {
    if($add_footer_script) {
      add_action( 'wp_footer', array( &$this, 'print_script_footer' ) );
    }
     $js_enabled = $this->js_enabled();
     $analytics_code = "";
     $onclick = '';
     
      if ( $this->google_analytics_enabled() ) {
          $title_var = "NULL";
          $analytics_code = "if(typeof(_gaq) != 'undefined') { _gaq.push(['_trackEvent','PRINTFRIENDLY', 'print', '".$title_var."']);
          }else if(typeof(ga) != 'undefined') {  ga('send', 'event','PRINTFRIENDLY', 'print', '".$title_var."'); }";
        if( $js_enabled ) {
          $onclick = 'onclick="window.print();'. $analytics_code .' return false;"';
        } else {
          $onclick = '';
        }       
      } else if ( $js_enabled ) {
        $onclick = 'onclick="window.print(); return false;"';
      }

    if( $js_enabled ){
      $href = '#';
    } else {
      $href = 'http://www.printfriendly.com/print?url='.urlencode(get_permalink());
    }

       if (!$js_enabled) {
          if($this->google_analytics_enabled()) {
              $onclick = $onclick.' onclick="'.$analytics_code.'"';
          }
          $href = "http://www.printfriendly.com/print?headerImageUrl=".urlencode($this->options['image_url'])."&headerTagline=".urlencode($this->options['tagline'])."&pfCustomCSS=".urlencode($this->options['custom_css_url'])."&imageDisplayStyle=".urlencode($this->options['image-style'])."&disableClickToDel=".urlencode($this->options['click_to_delete'])."&disablePDF=".urlencode($this->options['pdf'])."&disablePrint=".urlencode($this->options['print'])."&disableEmail=".urlencode($this->options['email'])."&hideImages=".urlencode($this->options['hide-images'])."&url=".urlencode(get_permalink())."&source=wp";
        }
        if ( !is_singular() && '' != $onclick && $js_enabled)  {
          $onclick = '';
          $href = add_query_arg('pfstyle','wp',get_permalink());
        }
    
        $align = '';
        if ( 'none' != $this->options['content_position'] )
          $align = ' pf-align'.$this->options['content_position'];
    $href = str_replace("&", "&amp;", $href );
        $button = apply_filters( 'printfriendly_button', '<div class="printfriendly'.$align.'"><a href="'.$href.'" rel="nofollow" '.$onclick.' class="noslimstat">'.$this->button().'</a></div>' );
    return $button;
  }
  

  /**
  * @since 3.2.9
  * @returns if google analytics enabled
  */
  function google_analytics_enabled() {
    return isset( $this->options['enable_google_analytics'] ) && $this->options['enable_google_analytics'] == 'yes';
  }
    /**
  * @since 3.2.6
  * @return boolean true if JS is enabled for the plugin
  **/
  function js_enabled() {
    return isset( $this->options['javascript'] ) && $this->options['javascript'] == 'yes';
  }

    /**
     * Filter posts by category.
     *
     * @since 3.2.2
     * @return boolean true if post belongs to category selected for button display
     */
    function category_included() {
//      return ( 'all' === $this->options['category_ids'][0] || in_category($this->options['category_ids']) );
    return true;
    }

    /**
     * Register the textdomain and the options array along with the validation function
     *
     * @since 3.0
     */
    function init() {
      // Allow for localization
      load_plugin_textdomain( $this->hook, false, basename( dirname( __FILE__ ) ) . '/languages' );

      // Register our option array
      register_setting( $this->option_name, $this->option_name, array( &$this, 'options_validate' ) );
    }

    /**
     * Validate the saved options.
     *
     * @since 3.0
     * @param array $input with unvalidated options.
     * @return array $valid_input with validated options.
     */
    function options_validate( $input ) {
      $valid_input = $input;

    /* Section 1 options */
      if ( !isset( $input['button_type'] ) || !in_array( $input['button_type'], array(
      'pf-button.gif', 'pf-button-both.gif', 'pf-button-big.gif', // buttongroup1
    'button-print-grnw20.png', 'button-print-blu20.png', 'button-print-gry20.png', // buttongroup2
    'pf-icon-small.gif', 'pf-icon-both.gif','pf-icon.gif', 'text-only', // buttongroup3
    'custom-image', // custom
    'button-print-whgn20.png', 'pf_button_sq_gry_m.png', 'pf_button_sq_gry_l.png', 'pf_button_sq_grn_m.png',
    'pf_button_sq_grn_l.png', // backward compatibility
    ) ) )
        $valid_input['button_type'] = 'pf-button.gif';

// @todo custom image url validation
      if ( !isset( $input['custom_image'] ) || empty( $input['custom_image'] ) )
        $valid_input['custom_image'] = '';

// @todo validate optional custom text
      if ( !isset( $input['custom_text'] ) ) {
      $valid_input['custom_text'] = 'Print Friendly';
      }
/*      else {

      }*/

      // Custom button selected, but no url nor text given, reset button type to default
      if( 'custom-image' === $valid_input['button_type'] && ( '' === $valid_input['custom_image'] && '' === $input['custom_text'] ) ) {
        $valid_input['button_type'] = 'pf-button.gif';
        add_settings_error( $this->option_name, 'invalid_custom_image', __( 'No valid custom image url received, please enter a valid url to use a custom image.', $this->hook ) );
      }


      $valid_input['text_size'] = (int) $input['text_size'];
      if ( !isset($valid_input['text_size']) || 0 == $valid_input['text_size'] ) {
        $valid_input['text_size'] = 14;
      } else if ( 25 < $valid_input['text_size'] || 9 > $valid_input['text_size'] ) {
        $valid_input['text_size'] = 14;
        add_settings_error( $this->option_name, 'invalid_text_size', __( 'The text size you entered is invalid, please stay between 9px and 25px', $this->hook ) );
      }

      if ( !isset( $input['text_color'] )) {
        $valid_input['text_color'] = $this->options['text_color'];
      } else if ( ! preg_match('/^#[a-f0-9]{3,6}$/i', $input['text_color'] ) ) {
        // Revert to previous setting and throw error.
        $valid_input['text_color'] = $this->options['text_color'];
        add_settings_error( $this->option_name, 'invalid_color', __( 'The color you entered is not valid, it must be a valid hexadecimal RGB font color.', $this->hook ) );
      }



    /* Section 2 options */
      if ( !isset( $input['enable_css'] ) || 'off' !== $input['enable_css'] )
        $valid_input['enable_css'] = 'on';

      if ( !isset( $input['content_position'] ) || !in_array( $input['content_position'], array( 'none', 'left', 'center', 'right' ) ) )
        $valid_input['content_position'] = 'left';

      if ( !isset( $input['content_placement'] ) || !in_array( $input['content_placement'], array( 'before', 'after' ) ) )
        $valid_input['content_placement'] = 'after';

      foreach ( array( 'margin_top', 'margin_right', 'margin_bottom', 'margin_left' ) as $opt )
        $valid_input[$opt] = (int) $input[$opt];

      unset( $opt );


    /* Section 3 options */
      foreach ( array( 'show_on_posts', 'show_on_pages', 'show_on_homepage', 'show_on_categories', 'show_on_taxonomies' ) as $opt ) {
        if ( !isset( $input[$opt] ) || 'on' !== $input[$opt] ) {
          unset( $valid_input[$opt] );
        }
      }
      unset( $opt );

      // Just in case
      if( isset( $input['show_on_template'] ) )
        unset( $valid_input['show_on_template'] );


    if( isset( $input['category_ids'] ) ) {
      /**
     * Validate received category ids:
     * - Is there only one array item and does it contain the string text 'all' ? => pass
     * - Otherwise, make sure the ids are integer values
     */
/*        $valid_input['category_ids'] = explode(',', $input['category_ids']);
        $valid_input['category_ids'] = array_map( 'trim', $valid_input['category_ids'] );
        if( ( count( $valid_input['category_ids'] ) === 1 && 'all' === $valid_input['category_ids'][0] ) === false ) {
      foreach( $valid_input['category_ids'] as $k => $v ) {
        if( $v !== '' && ( ctype_digit( (string) $v ) === true && ( intval( $v ) == $v ) ) ) {
          $valid_input['category_ids'][$k] = intval( $v );
        }
        else {
          // Invalid input - Show error message ?
          unset( $valid_input['category_ids'][$k] );
        }
      }
    }*/
    unset( $valid_input['category_ids'] );
      }

      //echo '<pre>'.print_r($input,1).'</pre>';
      //die;



    /* Section 4 options */
      if ( !isset( $input['logo'] ) || !in_array( $input['logo'], array( 'favicon', 'upload-an-image' ) ) )
        $valid_input['logo'] = 'favicon';

// @todo custom logo url validation
      if ( !isset( $input['image_url'] ) || empty( $input['image_url'] ) )
        $valid_input['image_url'] = '';

// @todo validate optional tagline text
      if ( !isset( $input['tagline'] ) ) {
      $valid_input['tagline'] = '';
      }
/*      else {

      }*/

      // Custom logo selected, but no valid url given, reset logo to default
      if( 'upload-an-image' === $valid_input['logo'] && '' === $valid_input['image_url'] ) {
        $valid_input['logo'] = 'favicon';
        add_settings_error( $this->option_name, 'invalid_custom_logo', __( 'No valid custom logo url received, please enter a valid url to use a custom logo.', $this->hook ) );
      }


      if ( !isset( $input['image-style'] ) || !in_array( $input['image-style'], array( 'right', 'left', 'none', 'block' ) ) )
        $valid_input['image-style'] = 'right';


      foreach( array( 'click_to_delete', 'hide-images', 'email', 'pdf', 'print', ) as $opt ) {
        if( !isset( $input[$opt] ) || !in_array( $input[$opt], array( '0', '1' ) ) ) {
          $valid_input[$opt] = '0';
    }
      }
      unset( $opt );


// @todo custom css url validation
      if ( !isset( $input['custom_css_url'] ) || empty( $input['custom_css_url'] ) )
        $valid_input['custom_css_url'] = '';



    /* Section 5 options */
      if ( !isset( $input['website_protocol'] ) || !in_array( $input['website_protocol'], array( 'http', 'https' ) ) )
        $valid_input['website_protocol'] = 'http';

      if ( !isset( $input['password_protected'] ) || !in_array( $input['password_protected'], array( 'no', 'yes' ) ) )
        $valid_input['password_protected'] = 'no';

      if ( !isset( $input['javascript'] ) || !in_array( $input['javascript'], array( 'no', 'yes' ) ) )
        $valid_input['javascript'] = 'yes';
     
    /*Analytics Options */
    if ( !isset( $input['enable_google_analytics'] ) || !in_array( $input['enable_google_analytics'], array( 'no', 'yes' ) ) ) {
    $valid_input['enable_google_analytics'] = "no";
    }
  
    if ( !isset( $input['pf_algo'] ) || !in_array( $input['pf_algo'], array( 'wp', 'pf' ) ) ) {
    $valid_input['pf_algo'] = "wp";
    }

    /* Database version */
      $valid_input['db_version'] = $this->db_version;

      return $valid_input;
    }

    /**
     * Register the config page for all users that have the manage_options capability
     *
     * @since 3.0
     */
    function add_config_page() {
      $this->settings_page = add_options_page( __( 'PrintFriendly Options', $this->hook ), __( 'Print Friendly & PDF', $this->hook ), 'manage_options', $this->hook, array( &$this, 'config_page' ) );

      //register  callback gets call prior your own page gets rendered
      add_action('load-'.$this->settings_page, array(&$this, 'on_load_printfriendly'));
    }

    /**
     * Shows help on the plugin page when clicking on the Help button, top right.
     *
     * @since 3.0
     */
    function contextual_help( $contextual_help, $screen_id ) {
      if ( $this->settings_page == $screen_id ) {
        $contextual_help = '<strong>'.__( "Need Help?", $this->hook ).'</strong><br/>'
          .sprintf( __( "Be sure to check out the %s!", $this->hook), '<a href="http://wordpress.org/extend/plugins/printfriendly/faq/">'.__( "Frequently Asked Questions", $this->hook ).'</a>' );
      }
      return $contextual_help;
    }

    /**
     * Enqueue the scripts for the admin settings page
     *
     * @since 3.0
     * @param string $hook_suffix hook to check against whether the current page is the PrintFriendly settings page.
     */
    function admin_enqueue_scripts( $screen_id ) {
      if ( $this->settings_page == $screen_id ) {
        $ver = '3.2.5';
        wp_register_script( 'pf-color-picker', plugins_url( 'colorpicker.js', __FILE__ ), array( 'jquery', 'media-upload' ), $ver );
        wp_register_script( 'pf-admin-js', plugins_url( 'admin.js', __FILE__ ), array( 'jquery', 'media-upload' ), $ver );

        wp_enqueue_script( 'pf-color-picker' );
        wp_enqueue_script( 'pf-admin-js' );


        wp_enqueue_style( 'printfriendly-admin-css', plugins_url( 'admin.css', __FILE__ ), array(), $ver);
      }
    }

    /**
     * Register the settings link for the plugins page
     *
     * @since 3.0
     * @param array $links the links for the plugins.
     * @param string $file filename to check against plugins filename.
     * @return array $links the links with the settings link added to it if appropriate.
     */
    function filter_plugin_actions( $links, $file ){
      // Static so we don't call plugin_basename on every plugin row.
      static $this_plugin;
      if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );

      if ( $file == $this_plugin ){
        $settings_link = '<a href="options-general.php?page='.$this->hook.'">' . __( 'Settings', $this->hook ) . '</a>';
        array_unshift( $links, $settings_link ); // before other links
      }
      return $links;
    }

    /**
     * Set default values for the plugin. If old, as in pre 1.0, settings are there, use them and then delete them.
     *
     * @since 3.0
     */
    function set_defaults() {
      // Set some defaults
      $this->options = array(
        'button_type' => 'pf-button.gif',
        'content_position' => 'left',
        'content_placement' => 'after',
        'custom_image' => 'http://cdn.printfriendly.com/pf-icon.gif',
        'custom_text' => 'Print Friendly',
        'enable_css' => 'on',
        'margin_top' => '12',
        'margin_right' => '12',
        'margin_bottom' => '12',
        'margin_left' => '12',
        'show_on_posts' => 'on',
        'show_on_pages' => 'on',
        'text_color' => '#6D9F00',
        'text_size' => 14,
        'logo' => 'favicon',
        'image_url' => '',
        'tagline' => '',
        'click_to_delete' => '0', // 0 - allow, 1 - do not allow
        'hide-images' => '0', // 0 - show images, 1 - hide images
        'image-style' => 'right', // 'right', 'left', 'none', 'block'
        'email' => '0', // 0 - allow, 1 - do not allow
        'pdf' => '0', // 0 - allow, 1 - do not allow
        'print' => '0', // 0 - allow, 1 - do not allow
        'website_protocol' => 'http',
        'password_protected' => 'no',
        'javascript' => 'yes',
        'custom_css_url' => '',
        'enable_google_analytics' => 'no',
        'pf_algo' => 'wp'
//        'category_ids' => array('all'),
      );

      // Check whether the old badly named singular options are there, if so, use the data and delete them.
      foreach ( array_keys( $this->options ) as $opt ) {
        $old_opt = get_option( 'pf_'.$opt );
        if ( $old_opt !== false ) {
          $this->options[$opt] = $old_opt;
          delete_option( 'pf_'.$opt );
        }
      }

      // This should always be set to the latest immediately when defaults are pushed in.
      $this->options['db_version'] = $this->db_version;

      update_option( $this->option_name, $this->options );
    }

    /**
     * Upgrades the stored options, used to add new defaults if needed etc.
     *
     * @since 3.0
     */
    function upgrade() {
      // update options to version 2
      if($this->options['db_version'] < 2) {

        $additional_options = array(
          'enable_css' => 'on',
          'logo' => 'favicon',
          'image_url' => '',
          'tagline' => '',
          'click_to_delete' => '0',
          'website_protocol' => 'http',
          'password_protected' => 'no',
          'javascript' => 'yes'
        );

        // use old javascript_include value to initialize javascript
        if(!isset($this->options['javascript_include']))
          $additional_options['javascript'] = 'no';

        unset($this->options['javascript_include']);
        unset($this->options['javascript_fallback']);

        // correcting badly named option
        if(isset($this->options['disable_css'])) {
          $additional_options['enable_css'] = $this->options['disable_css'];
          unset($this->options['disable_css']);
        }

        // check whether image we do not list any more was used
        if(in_array($this->options['button_type'], array('button-print-whgn20.png',  'pf_button_sq_qry_m.png',  'pf_button_sq_qry_l.png',  'pf_button_sq_grn_m.png',  'pf_button_sq_grn_l.png'))) {
          // previous version had a bug with button name
          if(in_array($this->options['button_type'], array('pf_button_sq_qry_m.png',  'pf_button_sq_qry_l.png')))
            $this->options['button_type'] = str_replace('qry', 'gry', $this->options['button_type']);

          $image_address = '//cdn.printfriendly.com/'.$this->options['button_type'];
          $this->options['button_type'] = 'custom-image';
          $this->options['custom_text'] = '';
          $this->options['custom_image'] = $image_address;
        }

        $this->options = array_merge($this->options, $additional_options);
      }

      // update options to version 3
      if($this->options['db_version'] < 3) {

        $old_show_on = $this->options['show_list'];
        // 'manual' setting
        $additional_options = array('custom_css_url' => '');

        if($old_show_on == 'all') {
          $additional_options = array(
            'show_on_pages' => 'on',
            'show_on_posts' => 'on',
            'show_on_homepage' => 'on',
            'show_on_categories' => 'on',
            'show_on_taxonomies' => 'on'
          );
        }

        if($old_show_on == 'single') {
          $additional_options = array(
            'show_on_pages' => 'on',
            'show_on_posts' => 'on'
          );
        }

        if($old_show_on == 'posts') {
          $additional_options = array(
            'show_on_posts' => 'on',
          );
        }

        unset($this->options['show_list']);

        $this->options = array_merge($this->options, $additional_options);
      }

      // update options to version 4
      if($this->options['db_version'] < 4) {

        $additional_options = array(
          'email' => '0',
          'pdf' => '0',
          'print' => '0',
        );

        $this->options = array_merge($this->options, $additional_options);
      }

      // update options to version 6
      // Replacement for db version 5 - should also be run for those already upgraded
      if($this->options['db_version'] < 6) {

/*        $additional_options = array(
          'category_ids' => array(),
        );

    if( !isset( $this->options['category_ids'] ) || ( isset( $this->options['category_ids'] ) && 0 === count( $this->options['category_ids'] ) ) ) {
          $additional_options['category_ids'][] = 'all';
    }

        $this->options = array_merge($this->options, $additional_options);
*/
        unset($this->options['category_ids']);
      }


      if($this->options['db_version'] < 7) {

        $additional_options = array(
          'hide-images' => '0',
          'image-style' => 'right',
        );

        $this->options = array_merge($this->options, $additional_options);
      }
      if($this->options['db_version'] < 8) {
    $this->options['enable_google_analytics'] = 'no';
    }

      if($this->options['db_version'] < 9) {
    $this->options['pf_algo'] = 'wp';
    }
      $this->options['db_version'] = $this->db_version;

      update_option( $this->option_name, $this->options );
    }

    /**
     * Displays radio button in the admin area
     *
     * @since 3.0
     * @param string $name the name of the radio button to generate.
     * @param boolean $br whether or not to add an HTML <br> tag, defaults to true.
     */
    function radio($name, $br = false){
      $var = '<input id="'.$name.'" class="radio" name="'.$this->option_name.'[button_type]" type="radio" value="'.$name.'" '.$this->checked( 'button_type', $name, false ).'/>';
      $button = $this->button( $name );
      if ( '' != $button )
        echo '<label for="'.$name.'">' . $var . $button . '</label>';
      else
        echo $var;

      if ( $br )
        echo '<br>';
    }

    /**
     * Displays button image in the admin area
     *
     * @since 3.0
     * @param string $name the name of the button to generate.
     */
    function button( $name = false ){
      if( !$name )
        $name = $this->options['button_type'];
    $button_css  = $this->generic_button_css();
      $text = $this->options['custom_text'];
      $img_path = 'http://cdn.printfriendly.com/';
      if($this->options['website_protocol'] == 'https')
        $img_path = 'https://pf-cdn.printfriendly.com/images/';

      switch($name){
      case "custom-image":
        if( '' == trim($this->options['custom_image']) )
          $return = '';
        else
          $return = '<img src="'.$this->options['custom_image'].'" alt="Print Friendly" />';

        $return .= $this->options['custom_text'];

        return $return;
        break;
      case "text-only":
        return '<span class="printfriendly-text2">'.$text.'</span>';
        break;

      case "pf-icon-both.gif":
        return '<span class="printfriendly-text2 printandpdf"><img style="border:none;margin-right:6px;" src="'.$img_path.'pf-print-icon.gif" width="16" height="15" alt="Print Friendly Version of this page" />Print <img style="'.$button_css.'margin:0 6px" src="'.$img_path.'pf-pdf-icon.gif" width="12" height="12" alt="Get a PDF version of this webpage" />PDF</span>';
        break;

      case "pf-icon-small.gif":
        return '<img style="'.$button_css.'margin-right:4px;" src="'.$img_path.'pf-icon-small.gif" alt="PrintFriendly and PDF" width="18" height="18"><span class="printfriendly-text2">'.$text.'</span>';
        break;
      case "pf-icon.gif":
        return '<img style="'.$button_css.'margin-right:6px;" src="'.$img_path.'pf-icon.gif" width="23" height="25" alt="PrintFriendly and PDF"><span class="printfriendly-text2">'.$text.'</span>';
        break;

      default:
        return '<img style="'.$button_css.'" src="'.$img_path.$name.'" alt="Print Friendly" />';
        break;
      }
    }

  /**
  *
  *
  **/
  
  function generic_button_css() {
    return "border:none;-webkit-box-shadow:none; box-shadow:none;";
  }


    /**
     * Convenience function to output a value custom button preview elements
     *
     * @since 3.0.9
     */
    function custom_button_preview() {
      if( '' == trim($this->options['custom_image']) )
        $button_preview = '<span id="pf-custom-button-preview"></span>';
      else
        $button_preview = '<span id="pf-custom-button-preview"><img src="'.$this->options['custom_image'].'" alt="Print Friendly" /></span>';

      $button_preview .= '<span class="printfriendly-text2">'.$this->options['custom_text'].'</span>';

      echo $button_preview;
    }

    /**
     * Convenience function to output a value for an input
     *
     * @since 3.0
     * @param string $val value to check.
     */
    function val( $val ) {
      if ( isset( $this->options[$val] ) )
        echo esc_attr( $this->options[$val] );
    }

    /**
     * Like the WordPress checked() function but it doesn't throw notices when the array key isn't set and uses the plugins options array.
     *
     * @since 3.0
     * @param mixed $val value to check.
     * @param mixed $check_against value to check against.
     * @param boolean $echo whether or not to echo the output.
     * @return string checked, when true, empty, when false.
     */
    function checked( $val, $check_against = true, $echo = true ) {
      if ( !isset( $this->options[$val] ) )
        return;

      if ( $this->options[$val] == $check_against ) {
        if ( $echo )
          echo ' checked="checked" ';
        else
          return ' checked="checked" ';
      }
    }

    /**
     * Helper for creating checkboxes.
     *
     * @since 3.1.5
     * @param string $name string used for various parts of checkbox
     *
     */
    function create_checkbox($name, $label='', $labelid='' ) {
    $label = ( !empty( $label) ? $label : __( ucfirst($name), $this->hook ) );
      echo '<label' . ( !empty( $labelid ) ? ' id=' . $labelid : '' ) . '><input type="checkbox" class="show_list" name="' . $this->option_name . '[show_on_' . $name . ']" value="on" ';
      $this->checked( 'show_on_' . $name, 'on');
      echo ' />' . $label . "</label>\r\n";
    }


    /**
     * Helper that checks if any of the content types is checked to display pf button.
     *
     * @since 3.1.5
     * @return boolean true if none of the content types is checked
     *
     */
    function is_manual() {
      return !(isset($this->options['show_on_posts']) ||
            isset($this->options['show_on_pages']) ||
            isset($this->options['show_on_homepage']) ||
            isset($this->options['show_on_categories']) ||
//            (isset($this->options['category_ids']) && count($this->options['category_ids']) > 0) ||
            isset($this->options['show_on_taxonomies']));
   }


    /**
     * Like the WordPress selected() function but it doesn't throw notices when the array key isn't set and uses the plugins options array.
     *
     * @since 3.0.9
     * @param mixed $val value to check.
     * @param mixed $check_against value to check against.
     * @return string checked, when true, empty, when false.
     */
    function selected( $val, $check_against = true) {
      if ( !isset( $this->options[$val] ) )
        return;

      return selected ($this->options[$val], $check_against);
    }

    /**
     * For use with page metabox.
     *
     * @since 3.2.2
     */
    function get_page_post_type() {
      $post_types = get_post_types( array( 'name' => 'page' ), 'object' );
      //echo '<pre>'.print_r($post_types,1).'</pre>';
      //die;

      return $post_types['page'];
    }


    /**
     * Helper that checks if wp versions is above 3.0.
     *
     * @since 3.2.2
     * @return boolean true wp version is above 3.0
     *
     */
    function wp_version_gt30() {
      global $wp_version;
      return version_compare($wp_version, '3.0', '>=');
    }


    /**
     * Create box for picking individual categories.
     *
     * @since 3.2.2
     */
    function create_category_metabox() {
    $obj = new stdClass();
    $obj->ID = 0;
      do_meta_boxes('settings_page_' . $this->hook, 'normal', $obj);
    }


    /**
     * Load metaboxes advanced button display settings.
     *
     * @since 3.2.2
     */
    function on_load_printfriendly() {
      global $wp_version;
      if($this->wp_version_gt30()) {
        require_once('includes/meta-boxes.php');
        //require_once('includes/nav-menu.php');
        wp_enqueue_script('post');

        add_meta_box('categorydiv', __('Only display when post is in:', $this->hook), 'post_categories_meta_box', 'settings_page_'. $this->hook, 'normal', 'core');
      }
    }

    /**
     * Output the config page
     *
     * @since 3.0
     */
    function config_page() {


      // Since WP 3.2 outputs these errors by default, only display them when we're on versions older than 3.2 that do support the settings errors.
      global $wp_version;
      if(version_compare($wp_version, '3.2', '<' ) && $this->wp_version_gt30() )
        settings_errors();

      // Show the content of the options array when debug is enabled
      if ( WP_DEBUG ) {
    echo "<p>" . __( 'Currently in Debug Mode. Following information is visible in debug mode only:', $this->hook ) . "</p>";
        echo '<pre>' . __( 'Options:', $this->hook ) . '<br><br>' . print_r( $this->options, 1 ) . '</pre>';
    }
?>
      <div id="pf_settings" class="wrap">

        <div class="icon32" id="printfriendly"></div>
        <h2><?php _e( 'Print Friendly & PDF Settings', $this->hook ); ?></h2>

        <form action="options.php" method="post">
          <?php settings_fields( $this->option_name ); ?>

          <h3><?php _e( "Pick Your Button Style", $this->hook ); ?></h3>

          <fieldset id="button-style">
            <div id="buttongroup1">
              <?php $this->radio('pf-button.gif'); ?>
              <?php $this->radio('pf-button-both.gif'); ?>
              <?php $this->radio('pf-button-big.gif'); ?>
            </div>
            <div id="buttongroup2">
              <?php $this->radio('button-print-grnw20.png'); ?>
              <?php $this->radio('button-print-blu20.png'); ?>
              <?php $this->radio('button-print-gry20.png'); ?>
            </div>
            <div id="buttongroup3">
              <?php $this->radio('pf-icon-small.gif'); ?>
              <?php $this->radio('pf-icon-both.gif'); ?>
              <?php $this->radio('pf-icon.gif'); ?>
              <?php $this->radio('text-only'); ?>
            </div>

            <div id="custom">
              <label for="custom-image">
                <?php echo '<input id="custom-image" class="radio" name="'.$this->option_name.'[button_type]" type="radio" value="custom-image" '.$this->checked( 'button_type', 'custom-image', false ).'/>'; ?>
                <?php _e( "Custom Button", $this->hook ); ?>
              </label>
              <div id="custom-img">
                <?php _e( "Enter Image URL", $this->hook ); ?><br>
                <input id="custom_image" type="text" class="clear regular-text" size="30" name="<?php echo $this->option_name; ?>[custom_image]" value="<?php $this->val( 'custom_image' ); ?>" />
                <div class="description"><?php _e( "Ex: http://www.example.com/<br>Ex: /wp/wp-content/uploads/example.png", $this->hook ); ?>
                </div>
              </div>
              <div id="pf-custom-button-error"></div>
              <div id="custom-txt" >
                <div id="txt-enter">
                  <?php _e( "Text", $this->hook ); ?><br>
                  <input type="text" size="30" class="clear regular-text" name="<?php echo $this->option_name; ?>[custom_text]" id="custom_text" value="<?php $this->val( 'custom_text' ); ?>">
                </div>
                <div id="txt-color" class="clear">
                  <?php _e( "Text Color", $this->hook ); ?>
                  <input type="hidden" name="<?php echo $this->option_name; ?>[text_color]" id="text_color" value="<?php $this->val( 'text_color' ); ?>"/><br>
                  <div id="colorSelector">
                    <div style="background-color: <?php echo $this->options['text_color']; ?>;"></div>
                  </div>
                </div>
                <div id="txt-size">
                  <?php _e( "Text Size", $this->hook ); ?><br>
                  <input type="number" id="text_size" min="9" max="25" class="small-text" name="<?php echo $this->option_name; ?>[text_size]" value="<?php $this->val( 'text_size' ); ?>"/>
                </div>
              </div>
            <div id="custom-button-preview">
              <?php $this->custom_button_preview(); ?>
            </div>
          </fieldset>
          <br class="clear">

    <!--Section 2 Button Positioning-->
          <div id="button-positioning">
            <h3><?php _e( "Button Positioning", $this->hook ); ?>
      <span id="css"><input type="checkbox" name="<?php echo $this->option_name; ?>[enable_css]" value="<?php $this->val('enable_css');?>" <?php $this->checked('enable_css', 'off'); ?> />Do not use CSS for button styles</span>
            </h3>
            <div id="button-positioning-options">
              <div id="alignment">
                <label<?php /*for="pf_content_position"*/ ?>>
                  <select id="pf_content_position" name="<?php echo $this->option_name; ?>[content_position]" >
                    <option value="left" <?php selected( $this->options['content_position'], 'left' ); ?>><?php _e( "Left Align", $this->hook ); ?></option>
                    <option value="right" <?php selected( $this->options['content_position'], 'right' ); ?>><?php _e( "Right Align", $this->hook ); ?></option>
                    <option value="center" <?php selected( $this->options['content_position'], 'center' ); ?>><?php _e( "Center", $this->hook ); ?></option>
                    <option value="none" <?php selected( $this->options['content_position'], 'none' ); ?>><?php _e( "None", $this->hook ); ?></option>
                  </select>
                </label>
              </div>
              <div class="content_placement">
                <label<?php /* for="pf_content_placement"*/ ?>>
                  <select id="pf_content_placement" name="<?php echo $this->option_name; ?>[content_placement]" >
                    <option value="before" <?php selected( $this->options['content_placement'], 'before' ); ?>><?php _e( "Above Content", $this->hook ); ?></option>
                    <option value="after" <?php selected( $this->options['content_placement'], 'after' ); ?>><?php _e( "Below Content", $this->hook ); ?></option>
                  </select>
                </label>
              </div>
              <div id="margin">
                <label for="pf-margin_left">
                  <input type="number" name="<?php echo $this->option_name; ?>[margin_left]" id="pf-margin_left" value="<?php $this->val( 'margin_left' ); ?>" maxlength="3"/>
                  <?php _e( "Margin Left", $this->hook ); ?>
                </label>
                <label for="pf-margin_right">
                  <input type="number" name="<?php echo $this->option_name; ?>[margin_right]" id="pf-margin_right" value="<?php $this->val( 'margin_right' ); ?>"/> <?php _e( "Margin Right", $this->hook ); ?>
                </label>
                <label for="pf-margin_top">
                  <input type="number" name="<?php echo $this->option_name; ?>[margin_top]" id="pf-margin_top" value="<?php $this->val( 'margin_top' ); ?>" maxlength="3"/> <?php _e( "Margin Top", $this->hook ); ?>
                </label>
                <label for="pf-margin_bottom">
                  <input type="number" name="<?php echo $this->option_name; ?>[margin_bottom]" id="pf-margin_bottom" value="<?php $this->val( 'margin_bottom' ); ?>" maxlength="3"/> <?php _e( "Margin Bottom", $this->hook ); ?>
                </label>
              </div>
            </div>
          </div>
          <br class="clear">

    <!--Section 3 Button Placement-->
          <div id="button-placement">
            <h3><?php _e( "Display button on:", $this->hook ); ?></h3>
            <div id="pages">
              <?php $this->create_checkbox('posts', __( 'Posts', $this->hook )); ?>
              <?php $this->create_checkbox('pages', __( 'Pages', $this->hook )); ?>
              <?php $this->create_checkbox('homepage', __( 'Homepage', $this->hook )); ?>
              <?php $this->create_checkbox('categories', __( 'Category Pages', $this->hook )); ?>
              <?php $this->create_checkbox('taxonomies', __( 'Taxonomy Pages', $this->hook )); ?>
              <label for="show_on_template"><input type="checkbox" class="show_template" name="show_on_template" id="show_on_template" /><?php echo _e( 'Add direct to template', $this->hook ); ?></label>
              <textarea id="pf-shortcode" class="code" rows="2" cols="40">&lt;?php if(function_exists('pf_show_link')){echo pf_show_link();} ?&gt;</textarea>
              <label<?php /* for="pf-shortcode2"*/ ?>><?php _e( "or use shortcode inside your page/article", $this->hook ); ?></label>
              <textarea<?php /*  id="pf-shortcode2"*/ ?> class="code" rows="2" cols="40">[printfriendly]</textarea>
              <?php /* <input type="hidden" name="<? php echo $this->option_name; ?>[category_ids]" id="category_ids" value="<?php echo implode(',', $this->options['category_ids']); ? >" /> */ ?>
            </div>
          </div>
          <?php /*if($this->wp_version_gt30()) { ? >
          <div id="pf-categories">
            <h4><?php printf( __( '<a %s>Additional filter</a>', $this->hook), ' href="javascript:void(0)" id="toggle-categories"' ); ?></h4>
            <div id="pf-categories-metabox">
              <?php $this->create_category_metabox(); ?>
            </div>
           </div>
          <? php } */ ?>

          <br class="clear">

    <!--Section 4 Button Print Options-->
          <div id="print-options">
            <h3><?php _e( "Print PDF Options", $this->hook ); ?></h3>
            <label id="pf-favicon" for="favicon"<?php /*for="pf-logo"*/ ?>>
              <?php _e( "Page header", $this->hook ); ?>
              <select id="pf-logo" name="<?php echo $this->option_name; ?>[logo]" >
                <option value="favicon" <?php selected( $this->options['logo'], 'favicon' ); ?>><?php _e( "My Website Icon", $this->hook ); ?></option>
                <option value="upload-an-image" <?php selected( $this->options['logo'], 'upload-an-image' ); ?>><?php _e( "Upload an Image", $this->hook ); ?></option>
              </select>
            </label>
<?php /*            <div class="custom-logo">
        <label for="upload-an-image"><?php _e( "Enter url", $this->hook ); ?></label><input id="upload-an-image" type="text" class="regular-text" name="<?php echo $this->option_name; ?>[image_url]" value="<?php $this->val( 'image_url' ); ?>" />
        <label for="image-tagline"><?php _e( "Text (optional)", $this->hook ); ?></label><input id="image-tagline" type="text" class="regular-text" name="<?php echo $this->option_name; ?>[tagline]" value="<?php $this->val( 'tagline' ); ?>" />
      </div> */ ?>
            <div class="custom-logo"><label for="Enter_URL">Enter url</label><input id="upload-an-image" type="text" class="regular-text" name="<?php echo $this->option_name; ?>[image_url]" value="<?php $this->val( 'image_url' ); ?>" /><label for="Text__optional_">Text (optional)</label><input id="image-tagline" type="text" class="regular-text" name="<?php echo $this->option_name; ?>[tagline]" value="<?php $this->val( 'tagline' ); ?>" /></div>
            <div id="pf-image-error"></div>
            <div id="pf-image-preview"></div>
            <label for="click_to_delete">
              <?php _e( "Click-to-delete", $this->hook ); ?>
              <select name="<?php echo $this->option_name; ?>[click_to_delete]" id="click-to-delete">
                <option value="0" <?php selected( $this->options['click_to_delete'], '0' ); ?>><?php _e( "Allow", $this->hook ); ?></option>
                <option value="1" <?php selected( $this->options['click_to_delete'], '1' ); ?>><?php _e( "Not Allow", $this->hook ); ?></option>
              </select>
            </label>
            <label for="hide-images">
              <?php _e( "Images", $this->hook ); ?>
              <select name="<?php echo $this->option_name; ?>[hide-images]" id="hide-images">
                <option value="0" <?php selected( $this->options['hide-images'], '0' ); ?>><?php _e( "Include", $this->hook ); ?></option>
                <option value="1" <?php selected( $this->options['hide-images'], '1' ); ?>><?php _e( "Exclude", $this->hook ); ?></option>
              </select>
            </label>
            <label for="image-style">
              <?php _e( "Image style", $this->hook ); ?>
              <select name="<?php echo $this->option_name; ?>[image-style]" id="image-style">
                <option value="right" <?php selected( $this->options['image-style'], 'right' ); ?>><?php _e( "Align Right", $this->hook ); ?></option>
                <option value="left" <?php selected( $this->options['image-style'], 'left' ); ?>><?php _e( "Align Left", $this->hook ); ?></option>
                <option value="none" <?php selected( $this->options['image-style'], 'none' ); ?>><?php _e( "Align None", $this->hook ); ?></option>
                <option value="block" <?php selected( $this->options['image-style'], 'block' ); ?>><?php _e( "Center/Block", $this->hook ); ?></option>
              </select>
            </label>
            <label for="email">
              <?php _e( "Email", $this->hook ); ?>
              <select name="<?php echo $this->option_name; ?>[email]" id="email">
                <option value="0" <?php selected( $this->options['email'], '0' ); ?>><?php _e( "Allow", $this->hook ); ?></option>
                <option value="1" <?php selected( $this->options['email'], '1' ); ?>><?php _e( "Not Allow", $this->hook ); ?></option>
              </select>
            </label>
            <label for="pdf">
              <span class="alignleft"><?php _e( "PDF", $this->hook ); ?></span>
              <select name="<?php echo $this->option_name; ?>[pdf]" id="pdf" class="alignleft clear">
                <option value="0" <?php selected( $this->options['pdf'], '0' ); ?>><?php _e( "Allow", $this->hook ); ?></option>
                <option value="1" <?php selected( $this->options['pdf'], '1' ); ?>><?php _e( "Not Allow", $this->hook ); ?></option>
              </select>
              <p class="alignleft">
                  <h4 class="alignleft notice"><abbr class="required"><?php _e( "Developer Note: ", $this->hook ); ?></abbr><?php _e( "On localhost the images can not be included in the PDF. Once the website is live/public images will be included in the PDF.", $this->hook ); ?></h4>
              </p>
            </label>
            <label for="print" class="clear">
              <?php _e( "Print", $this->hook ); ?>
              <select name="<?php echo $this->option_name; ?>[print]" id="print">
                <option value="0" <?php selected( $this->options['print'], '0' ); ?>><?php _e( "Allow", $this->hook ); ?></option>
                <option value="1" <?php selected( $this->options['print'], '1' ); ?>><?php _e( "Not Allow", $this->hook ); ?></option>
              </select>
            </label>
            <label for="custom_css_url">
              <?php _e( "Custom css url", $this->hook ); ?>
              <input id="custom_css_url" type="text" class="regular-text" name="<?php echo $this->option_name; ?>[custom_css_url]" value="<?php $this->val( 'custom_css_url' ); ?>" />
              <span class="description pf-help-link"><a target="_howto" href="http://support.printfriendly.com/customer/portal/articles/895256-custom-css-styles"><?php _e( '?', $this->hook ); ?></a></span>
            </label>
          </div>

   <!--Section 5 WebMaster-->
        <h3><?php _e( "Webmaster Settings", $this->hook ); ?></h3>

        <label for="website_protocol"><?php _e( 'Website Protocol', $this->hook ); ?><br>
          <select id="website_protocol" name="<?php echo $this->option_name; ?>[website_protocol]" >
            <option value="http" <?php selected( $this->options['website_protocol'], 'http' ); ?>><?php _e( "http (common)", $this->hook ); ?></option>
            <option value="https" <?php selected( $this->options['website_protocol'], 'https' ); ?>><?php _e( "https (secure)", $this->hook ); ?></option>
          </select>
          <span id="https-beta-registration" class="description">HTTPS is in Beta. Please <a href="#" onclick="window.open('http://www.printfriendly.com/https-registration.html', 'newwindow', 'width=600, height=550'); return false;">Register for updates</a>.
          </span>
        </label>
        <label for="password_protected"><?php _e( 'Password Protected Content', $this->hook ); ?>
          <select id="password_protected" name="<?php echo $this->option_name; ?>[password_protected]">
            <option value="no" <?php selected( $this->options['password_protected'], 'no' ); ?>><?php _e( "No", $this->hook ); ?></option>
            <option value="yes" <?php selected( $this->options['password_protected'], 'yes' ); ?>><?php _e( "Yes", $this->hook ); ?></option>
          </select>
        </label>
        <label for="javascript"><?php _e( 'Use JavaScript', $this->hook ); ?><br>
          <select id="javascript" name="<?php echo $this->option_name; ?>[javascript]>">
            <option value="yes" <?php $this->selected( 'javascript', 'yes' ); ?>> <?php _e( "Yes", $this->hook ); ?></option>
            <option value="no" <?php $this->selected( 'javascript', 'no' ); ?>> <?php _e( "No", $this->hook ); ?></option>
          </select>
          <span class="description javascript no-italics">
            <?php _e( "Preview appears on the page in a Lightbox.", $this->hook ); ?>
          </span>
          <span class="description no-javascript no-italics">
            <?php _e( "Preview opens a new browser tab.", $this->hook ); ?>
          </span>
        </label>
        <label for="pf-analytics-tracking"><?php _e( 'Track in Google Analytics', $this->hook ); ?><br>
          <select id="pf-analytics-tracking" name="<?php echo $this->option_name; ?>[enable_google_analytics]">
            <option value="yes" <?php $this->selected( 'enable_google_analytics', 'yes' ); ?>> <?php _e( "Yes", $this->hook ); ?></option>
            <option value="no" <?php $this->selected( 'enable_google_analytics', 'no' ); ?>> <?php _e( "No", $this->hook ); ?></option>
          </select>
        </label>
    
        <label for="pf-algo-usage"><?php _e( 'My Page Content Selected By:', $this->hook ); ?>  <span class="description no-italics" ><?php _e( 'Change this setting if your content is not showing in the preview.', $this->hook ); ?></span><br>
          <select id="pf-algo-usage" name="<?php echo $this->option_name; ?>[pf_algo]">
            <option value="wp" <?php $this->selected( 'pf_algo', 'wp' ); ?>> <?php _e( 'WP Template', $this->hook ); ?></option>
            <option value="pf" <?php $this->selected( 'pf_algo', 'pf' ); ?>> <?php _e( "Content Algorithm", $this->hook ); ?></option>
          </select>
        </label>

        <p class="submit">
          <input type="submit" class="button-primary" value="<?php esc_attr_e( "Save Options", $this->hook ); ?>"/>
          <input type="reset" class="button-secondary" value="<?php esc_attr_e( "Cancel", $this->hook ); ?>"/>
        </p>
        <div id="after-submit">
          <p><?php _e( "Need professional options for your corporate, education, or agency developed website? Check out", $this->hook ); ?> <a href="http://www.printfriendly.com/pro">PrintFriendly Pro</a>.</p>
          <p>
            <?php _e( "Like PrintFriendly?", $this->hook ); ?> <a href="http://wordpress.org/extend/plugins/printfriendly/"><?php _e( "Give us a rating", $this->hook ); ?></a>. <?php _e( "Need help or have suggestions?", $this->hook ); ?> <a href="mailto:support@printfriendly.com?subject=Support%20for%20PrintFriendly%20WordPress%20plugin">support@PrintFriendly.com</a>.</p>
          </div>

        </form>
      </div>
<?php
    }
  }
  $printfriendly = new PrintFriendly_WordPress();
}

// Add shortcode for printfriendly button
add_shortcode( 'printfriendly', 'pf_show_link' );

/**
 * Convenience function for use in templates.
 *
 * @since 3.0
 * @return string returns a button to be printed.
 */
function pf_show_link() {
  global $printfriendly;
  return $printfriendly->getButton(true);
}
