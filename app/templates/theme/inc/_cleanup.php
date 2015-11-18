<?php

/**
 * Cleanup WordPress markup
 *
 * @package <%= opts.themeName %>
 * @since 0.1.0
 */

if( ! function_exists( '<%= opts.functionPrefix %>_markup_cleaner' ) ) {
	function <%= opts.functionPrefix %>_markup_cleaner() {
		// Cleanup wp_head()
		add_action( 'init', '<%= opts.functionPrefix %>_cleanup_head' );
		// Remove the WordPress version from RSS feeds
		add_filter( 'the_generator', '__return_false' );
		// Remove injected recent comments sidebar widget style
		add_action( 'widgets_init', '<%= opts.functionPrefix %>_remove_recent_comments_style', 1 );
		// Remove injected gallery shortcode style
		add_filter( 'use_default_gallery_style', '__return_false' );
		// Remove automatic paragraph tags
		remove_filter( 'the_content', 'wpautop' );
		add_filter( 'the_content', 'shortcode_unautop', 100 );
		remove_filter( 'the_excerpt', 'wpautop' );
		add_filter( 'the_excerpt', 'shortcode_unautop', 100 );
	}
	add_action( 'after_setup_theme','<%= opts.functionPrefix %>_markup_cleaner' );
}

if( ! function_exists( '<%= opts.functionPrefix %>_cleanup_head' ) ) {
	function <%= opts.functionPrefix %>_cleanup_head() {
		// Display the XHTML generator that is generated on the wp_head hook
		remove_action( 'wp_head', 'wp_generator' );
		// Emoji support detection script and styles
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji ');
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji ');
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		// Query strings from static resources
		add_filter( 'style_loader_src', '<%= opts.functionPrefix %>_remove_query_strings', 15, 1 );
		add_filter( 'script_loader_src', '<%= opts.functionPrefix %>_remove_query_strings', 15, 1 );
		// Output of <link> and <script> tags
		add_filter( 'style_loader_tag', '<%= opts.functionPrefix %>_clean_style_tag' );
		add_filter( 'script_loader_tag', '<%= opts.functionPrefix %>_clean_script_tag' );
	}
}

if( ! function_exists( '<%= opts.functionPrefix %>_remove_query_strings' ) ) {
	function <%= opts.functionPrefix %>_remove_query_strings( $src ) {
		return remove_query_arg( 'ver', $src );
	}
}

if( ! function_exists( '<%= opts.functionPrefix %>_clean_style_tag' ) ) {
	function <%= opts.functionPrefix %>_clean_style_tag( $input ) {
		preg_match_all( "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches );
		// Only display media if it is meaningful
		$media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
		return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
	}
}

if( ! function_exists( '<%= opts.functionPrefix %>_clean_script_tag' ) ) {
	function <%= opts.functionPrefix %>_clean_script_tag( $input ) {
		$input = str_replace( "type='text/javascript' ", '', $input );
		return str_replace( "'", '"', $input );
	}
}

if( ! function_exists( '<%= opts.functionPrefix %>_remove_recent_comments_style' ) ) {
	function <%= opts.functionPrefix %>_remove_recent_comments_style() {
		global $wp_widget_factory;
		if( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
			remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
		}
	}
}