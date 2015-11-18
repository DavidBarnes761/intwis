<?php
	/**
	 * The template for displaying home page.
	 *
	 * Template Name: Homepage
	 */

	$featuredVid = pods('video', get_option( 'site_settings_homepage-featured-video' )[0]);
	$heroBgID = get_option( 'site_settings_homepage-hero-background' )[0];
	$heroBgSrc = wp_get_attachment_image_src( $heroBgID, 'full' )[0];
	$ctaStudentsImg = wp_get_attachment_image_src( get_option( 'site_settings_cta-students-thumbnail' )[0], 'thumbnail' )[0];
	$ctaPractitionersImg = wp_get_attachment_image_src( get_option( 'site_settings_cta-practitioners-thumbnail' )[0], 'thumbnail' )[0];
	$ctaPatientsImg = wp_get_attachment_image_src( get_option( 'site_settings_cta-patients-thumbnail' )[0], 'thumbnail' )[0];
	$videos = pods( 'video', ['orderby' => 'date DESC'] );
	$partners = pods( 'partner', [] );
?>
<?php get_header(); ?>
	<div class="hero-wrapper homepage dark" style="background-image: url('<?php echo $heroBgSrc; ?>');">
		<div class="homepage-hero-section row">
			<div class="homepage-hero-content large-4 columns end">
				<?php echo get_the_content(); ?>
				<hr>
				<a href="<?php echo get_the_permalink( $featuredVid->id() ); ?>">
					<div class="featured-video">
						<img src="http://img.youtube.com/vi/<?php echo $featuredVid->field( 'video-id' ); ?>/0.jpg" />
						<img class="play-overlay" src="/wp-content/themes/integrative-wisdom/img/play-overlay.png">
					</div>
				</a>
				<hr>
				<h2>Sign up for email updates</h2>
				<form method="post" name="integrative-wisdom" action="https://s2070279235.t.eloqua.com/e/f2" onsubmit="setTimeout(function(){if(document.querySelector){var s=document.querySelector('form#form97 input[type=submit]');if(s){s.disabled=true;}}},100);return true;" id="form97" class="elq-form" ><input value="integrative-wisdom" type="hidden" name="elqFormName"  /><input value="2070279235" type="hidden" name="elqSiteId"  /><input name="elqCampaignId" type="hidden"  /><div id="formElement0" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px" ><div class="field-wrapper" ></div><div class="field-wrapper" style="float: left; width: 100%; clear: both" ><div class="_100" style="float: left; width: 96%; margin-right: 2%; margin-left: 2%" ><p style="position: relative; margin: 0px; padding: 0px" ><label for="field0" style="display: block; line-height: 150%; padding: 1px 0pt 3px; white-space: nowrap" >Email Address<span style="color: red !important; display: inline; float: none; font-weight: bold; margin: 0pt 0pt 0pt; padding: 0pt 0pt 0pt" >*</span></label><input id="field0" name="email" type="text" value="<eloqua type='emailfield' syntax='EmailAddress' />" style="width: 100%"  /></p></div></div></div><div id="formElement1" class="sc-view form-design-field sc-static-layout sc-regular-size" style="left: 0px; right: 0px; top: 0px; bottom: 0px; padding: 6px 5px 9px 9px" ><div class="field-wrapper" ></div><div class="field-wrapper" style="float: left; width: 100%; clear: both" ><div class="_100" style="float: left; width: 96%; margin-right: 2%; margin-left: 2%" ><p style="position: relative; margin: 0px; padding: 0px" ><input type="submit" value="Submit" class="submit-button" style="font-size: 100%; height: 24px; width: 100px"  /></p></div></div></div></form>
			</div>
		</div>
	</div>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="slider-title-wrapper row">
				<div class="large-12 columns">
					<h2 class="slider-title"><span>Find Wisdom for You</span></h2>
				</div>
			</div>
			<div class="homepage-ctas row">
				<div class="cta-students small-12 medium-4 large-4 columns">
					<a href="/videos/for-students/">
						<img src="<?php echo $ctaStudentsImg; ?>" alt="Videos for Students"/>
						<span class="cta-button">For Students</span>
					</a>
				</div>
				<div class="cta-practitioners small-12 medium-4 large-4 columns">
					<a href="/videos/for-practitioners/">
						<img src="<?php echo $ctaPractitionersImg; ?>" alt="Videos for Practitioners"/>
						<span class="cta-button">For Practitioners</span>
					</a>
				</div>
				<div class="cta-students small-12 medium-4 large-4 columns">
					<a href="/videos/for-patients/">
						<img src="<?php echo $ctaPatientsImg; ?>" alt="Videos for Patients"/>
						<span class="cta-button">For Patients</span>
					</a>
				</div>
			</div>
			<div class="slider-title-wrapper row">
				<div class="large-12 columns">
					<h2 class="slider-title"><span>Watch Our Latest Videos</span></h2>
				</div>
			</div>
			<div class="video-slider-wrapper row">
				<div class="video-slider large-12 columns">
					<ul class="small-block-grid-2 medium-block-grid-3 large-block-grid-4">
						<?php
							if ( $videos->total() > 0 ) {
								while ( $videos->fetch() ) :
									$speaker = $videos->field( 'video-speaker' ); ?>
									<li class="video-thumb">
										<a href="<?php echo get_permalink( $videos->id() ); ?>">
											<div class="thumbnail group">
												<img src="http://img.youtube.com/vi/<?php echo $videos->field( 'video-id' ); ?>/0.jpg" />
												<span class="thumb-speaker-name"><?php echo get_the_title( $speaker['ID'] ); ?></span>
											</div>
											<p class="thumb-video-title"><?php echo get_the_title( $videos->id() ); ?></p>
										</a>
									</li>
								<?php endwhile;
							}
						?>
					</ul>
				</div>
			</div>
			<div class="slider-title-wrapper row">
				<div class="large-12 columns">
					<h2 class="slider-title"><span>Thanks to our participating partners</span></h2>
				</div>
			</div>
			<div class="partners-wrapper row">
				<div class="partners large-12 columns">
					<ul class="small-block-grid-2 medium-block-grid-5 large-block-grid-5">
						<?php
							if ( $partners->total() > 0 ) {
								while ( $partners->fetch() ) :
									$logo = wp_get_attachment_image_src($partners->field( 'partner-logo' )['ID'], 'full', true); ?>
									<li class="partner-logo">
										<a href="<?php echo $partners->field( 'partner-url' ); ?>">
											<div style="background: url('<?php echo $logo[0]; ?>') no-repeat center center;"></div>
										</a>
									</li>
								<?php endwhile;
							}
						?>
					</ul>
				</div>
			</div>
		</main>
	</div>
	<script>
		$(document).ready(function() {
			$('.video-slider ul').slick({
				autoplay: true,
				infinite: true,
				slidesToShow: 4,
				slidesToScroll: 1,
				responsive: [
					{
						breakpoint: 1024,
						settings: {
							slidesToShow: 3,
							arrows: false
						}
					},
					{
						breakpoint: 640,
						settings: {
							slidesToShow: 1,
							arrows: false
						}
					}
				]
			});
		});
	</script>
<?php get_footer(); ?>