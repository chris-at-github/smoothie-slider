<?php
/**
 * @package			Smoothie
 * @subpackage	smoothieslider
 * @copyright		Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license			GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');

abstract class modSmoothieSliderHelper {
	public static function getSlides(&$params) {

		$doc = JFactory::getDocument();
		$doc->addStyleSheet('/modules/mod_smoothieslider/css/smoothieslider.css');
		$doc->addScript('/modules/mod_smoothieslider/js/unslider.min.js');

		$script = '
			jQuery(function() {
				jQuery(\'.smoothie-slider\').unslider();
			});
		';
		$doc->addScriptDeclaration($script);

		// dbo | articles model
		$db			= JFactory::getDbo();
		$model	= JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$application				= JFactory::getApplication();
		$applicationParams	= $application->getParams();
		$model->setState('params', $applicationParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		$model->setState('filter.published', 1);

		// Access filter
		$access			= !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category | language filter
		$model->setState('filter.category_id', $params->get('catid', array()));
		$model->setState('filter.language', $application->getLanguageFilter());
		$model->setState('filter.featured', 'show');

		// Set ordering
		$mapping		= array(
			'm_dsc'		=> 'a.modified DESC, a.created',
			'mc_dsc'	=> 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
			'c_dsc'		=> 'a.created',
			'p_dsc'		=> 'a.publish_up',
		);
		$ordering		= JArrayHelper::getValue($mapping, $params->get('ordering', 'a.publish_up'));
		$direction	= 'ASC';

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $direction);

		$items = $model->getItems();

		foreach($items as &$item) {
			$item->slug			= $item->id . ':' . $item->alias;
			$item->catslug	= $item->catid . ':' . $item->category_alias;

			if($access || in_array($item->access, $authorised)) {
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));

			} else {
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}

			/* if param->readMore */
			$readmore = JArrayHelper::toObject($a = array(
				'url'		=> $item->link,
				'title'	=> $item->title
			));

			if(empty($item->urls) === false) {
				$urls			= json_decode($item->urls);

				if(empty($urls->urla) === false) {
					if(preg_match('/^(http:\/\/)?([\d]+)$/is', $urls->urla, $match) && empty($match[2]) === false) {
						$readmore->url = JRoute::_('index.php?Itemid=' . $match[2]);

					} else {
						$readmore->url = $urls->urla;
					}
				}
			}

			$item->readmore = $readmore;
		}

		return $items;
	}
}
?>