<?php
	/*
		Template Name: Speaker Bios Pod Page
	*/

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
				?>
				<div id="" class="slide large-6 columns">
					<h1><?php echo pods_image($picture); ?></h1>
					<?php echo $bio; ?>
					<?php echo pods_video('https://www.youtube.com/watch?v=fkR_Llt3tyc'); ?>
				</div><!-- close -->
			<?php endwhile; ?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>