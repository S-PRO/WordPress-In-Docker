<?php

add_action( 'wp_loaded', 'wpcf7_control_init' );

function wpcf7_control_init() {
	if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] )
	&& 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'] ) {
		return;
	}

	if ( isset( $_POST['_wpcf7'] ) ) {
		$contact_form = wpcf7_contact_form( (int) $_POST['_wpcf7'] );

		if ( $contact_form ) {
			$contact_form->submit();
		}
	}
}

add_filter( 'widget_text', 'wpcf7_widget_text_filter', 9 );

function wpcf7_widget_text_filter( $content ) {
	$pattern = '/\[[\r\n\t ]*contact-form(-7)?[\r\n\t ].*?\]/';

	if ( ! preg_match( $pattern, $content ) ) {
		return $content;
	}

	$content = do_shortcode( $content );

	return $content;
}

add_action( 'wp_enqueue_scripts', 'wpcf7_do_enqueue_scripts' );

function wpcf7_do_enqueue_scripts() {
	if ( wpcf7_load_js() ) {
		wpcf7_enqueue_scripts();
	}

	if ( wpcf7_load_css() ) {
		wpcf7_enqueue_styles();
	}
}

function wpcf7_enqueue_scripts() {
	$in_footer = true;

	if ( 'header' === wpcf7_load_js() ) {
		$in_footer = false;
	}

	wp_enqueue_script( 'contact-form-7',
		wpcf7_plugin_url( 'includes/js/scripts.js' ),
		array( 'jquery' ), WPCF7_VERSION, $in_footer );

	$wpcf7 = array(
		'apiSettings' => array(
			'root' => esc_url_raw( get_rest_url() ),
			'namespace' => 'contact-form-7/v1',
		),
		'recaptcha' => array(
			'messages' => array(
				'empty' =>
					__( 'Please verify that you are not a robot.', 'contact-form-7' ),
			),
		),
	);

	if ( defined( 'WP_CACHE' ) && WP_CACHE ) {
		$wpcf7['cached'] = 1;
	}

	if ( wpcf7_support_html5_fallback() ) {
		$wpcf7['jqueryUi'] = 1;
	}

	wp_localize_script( 'contact-form-7', 'wpcf7', $wpcf7 );

	do_action( 'wpcf7_enqueue_scripts' );
}

function wpcf7_script_is() {
	return wp_script_is( 'contact-form-7' );
}

function wpcf7_enqueue_styles() {
	wp_enqueue_style( 'contact-form-7',
		wpcf7_plugin_url( 'includes/css/styles.css' ),
		array(), WPCF7_VERSION, 'all' );

	if ( wpcf7_is_rtl() ) {
		wp_enqueue_style( 'contact-form-7-rtl',
			wpcf7_plugin_url( 'includes/css/styles-rtl.css' ),
			array(), WPCF7_VERSION, 'all' );
	}

	do_action( 'wpcf7_enqueue_styles' );
}

function wpcf7_style_is() {
	return wp_style_is( 'contact-form-7' );
}

/* HTML5 Fallback */

add_action( 'wp_enqueue_scripts', 'wpcf7_html5_fallback', 20 );

function wpcf7_html5_fallback() {
	if ( ! wpcf7_support_html5_fallback() ) {
		return;
	}

	if ( wpcf7_script_is() ) {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-spinner' );
	}

	if ( wpcf7_style_is() ) {
		wp_enqueue_style( 'jquery-ui-smoothness',
			wpcf7_plugin_url(
				'includes/js/jquery-ui/themes/smoothness/jquery-ui.min.css' ),
			array(), '1.11.4', 'screen' );
	}
}
