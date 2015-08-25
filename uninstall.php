<?php

if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();
 
delete_option( 'printfriendly_option' );

// Make sure any old options which may still lurking about get deleted as well
delete_option( 'pf_button_type' );
delete_option( 'pf_custom_image' );
delete_option( 'pf_custom_text' );
delete_option( 'pf_custom_both' );
delete_option( 'pf_show_list' );
delete_option( 'pf_content_placement' );
delete_option( 'pf_content_position' );
delete_option( 'pf_margin_top' );
delete_option( 'pf_margin_right' );
delete_option( 'pf_margin_bottom' );
delete_option( 'pf_margin_left' );
delete_option( 'pf_text_color' );
delete_option( 'pf_text_size' );