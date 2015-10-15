<?php
	/*
		Template Name: Single Video Pod Page
	*/

	$mypod = pods( 'video' );

	get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php
				$mypod = pods( 'speakerbio' );
				$mypod->find( 'name ASC' );
			?>
			<?php while ( $mypod->fetch() ) : ?>
				<?php
					// set our variables
					$picture = $mypod->field( 'speaker-picture' );
					$bio = $mypod->field( 'speaker-biography' );
					var_dump($picture);
				?>
				<div id="" class="slide">
					<h1><?php echo $picture; ?></h1>
					<?php echo $bio; ?>
				</div><!-- close -->
			<?php endwhile; ?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>