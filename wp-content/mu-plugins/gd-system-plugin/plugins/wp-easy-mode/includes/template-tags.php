<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Display the template document title
 */
function wpem_template_title() {

	$step = wpem_get_current_step();

	$title = sprintf(
		'%s ‹ %s',
		esc_html( $step->page_title ),
		esc_html( get_bloginfo( 'name' ) )
	);

	echo htmlentities( $title );

}

/**
 * Display an ordered list of steps
 */
function wpem_template_list_steps() {

	$steps = wp_easy_mode()->admin->get_steps();

	$count = count( $steps );

	echo '<ol class="wpem-steps-list">';

	foreach ( $steps as $i => $step ) {

		$classes = array(
			'wpem-steps-list-item',
			sprintf( 'wpem-steps-list-item-%d', $step->position ),
			sprintf( 'wpem-steps-list-item-%s', $step->name ),
		);

		if ( 0 === $i ) {

			$classes[] = 'first-step';

		}

		if ( $count === ( $i + 1 ) ) {

			$classes[] = 'last-step';

		}

		if ( $step->name === wpem_get_current_step()->name ) {

			$classes[] = 'active-step';

		}

		if ( wpem_get_current_step()->position > ( $i + 1 ) ) {

			$classes[] = 'done-step';

		}

		$classes = array_map( 'trim', $classes );

		printf(
			'<li class="%s">%s</li>',
			implode( ' ', array_map( 'esc_attr', $classes ) ),
			esc_html( $step->title )
		);

	}

	echo '</ol>';

}

/**
 * Display template footer
 */
function wpem_template_footer() {

	wp_print_scripts( 'jquery-blockui' );

	wp_print_scripts( 'wpem' );

	if ( 'theme' === wpem_get_current_step()->name ) {

		wp_print_scripts( 'wpem-theme' );

	}

	$fqdn = gethostname();

	if ( false === strpos( $fqdn, 'secureserver.net' ) ) {

		return;

	}

	$host = ( false !== strpos( $fqdn, '.prod.' ) ) ? 'secureserver.net' : 'test-secureserver.net';

	?>
	<script>"undefined"==typeof _trfd&&(window._trfd=[]),_trfd.push({"tccl.baseHost":"<?php echo esc_js( $host ) ?>"}),_trfd.push({"ap":"MWPQSWv2"})</script>
	<script src="//img1.wsimg.com/tcc/tcc_l.combined.1.0.2.min.js"></script>
	<?php

}
