<?php
/**
 * @package		Joomla.Site.Modules
 * @subpackage	mod_carousel
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php if(empty($slides) === false) { ?>
<div class="smoothie-slider">
	<ul>
		<?php foreach($slides as $slide) { ?>
			<?php $images = json_decode($slide->images); ?>
			<li>
				<?php if(isset($images->image_intro) && !empty($images->image_intro)) { ?>
					<?php
						// Ausrichtung des Bildes
						$imgfloat = $params->get('float_intro');
						if(empty($images->float_intro) === false) {
							$imgfloat = $images->float_intro;
						}
						if(empty($imgfloat) === false) {
							$imgfloat = 'smoothie-slider-image-' . htmlspecialchars($imgfloat);
						}

						// Alttext | Bildtitel
						$imgalt		= htmlspecialchars($images->image_intro_alt);
//						$imgtitle = '';
//						if($images->image_intro_caption) {
//							$imgtitle = 'title="' . htmlspecialchars($images->image_intro_caption) . '"';
//						}
					?>

					<div class="smoothie-slider-image <?php echo $imgfloat; ?>">
						<img src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo $imgalt ?>"/>
					</div>

				<?php } ?>

				<div class="smoothie-slider-text-outer">
					<div class="container">
						<div class="smoothie-slider-text">
							<header class="smoothie-slider-header"><?php echo $slide->title; ?></header>
							<?php echo $slide->introtext; ?>

							<?php /* if param->displayReadmore */ ?>
							<?php if(empty($slide->readmore) === false) { ?>
								<div class="smoothie-slider-readmore">
									<?php echo JHtml::link($slide->readmore->url, $slide->readmore->title); ?>
								</div>
							<?php } ?>

						</div>
					</div>
				</div>
			</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>