<?php
/**
 * @version 1.9.6
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the JEM View
 *
 * @package JEM
 *
 */
class JEMViewEventslist extends JViewLegacy
{
	/**
	 * Creates the Event Feed
	 */
	function display($cachable = false, $urlparams = false)
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		// Get some data from the model
		JRequest::setVar('limit', $app->getCfg('feed_limit'));
		$rows = $this->get('Data');

		foreach ($rows as $row) {
			// strip html from feed item title
			$title = $this->escape($row->title);
			$title = html_entity_decode($title);

			// categories (object of stdclass to array), when there is something to show
			if (!empty($row->categories)) {
				$category = array();
				foreach ($row->categories AS $category2) {
					$category[] = $category2->catname;
				}

				// ading the , to the list when there are multiple category's
				$category = $this->escape(implode(', ', $category));
				$category = html_entity_decode($category);
			} else {
				$category = '';
			}

			//Format date and time
			$displaydate = JEMOutput::formatLongDateTime($row->dates, $row->times,
				$row->enddates, $row->endtimes);

			// url link to event
			$link = JRoute::_(JEMHelperRoute::getEventRoute($row->id));

			// feed item description text
			$description = JText::_('COM_JEM_TITLE').': '.$title.'<br />';
			$description .= JText::_('COM_JEM_VENUE').': '.$row->venue.' / '.$row->city.'<br />';
			$description .= JText::_('COM_JEM_CATEGORY').': '.$category.'<br />';
			$description .= JText::_('COM_JEM_DATE').': '.$displaydate.'<br />';
			$description .= JText::_('COM_JEM_DESCRIPTION').': '.$row->fulltext;

			@$created = ($row->created ? date('r', strtotime($row->created)) : '');

			// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $description;
			$item->date 		= $created;
			$item->category 	= $category;

			// loads item info into rss array
			$doc->addItem($item);
		}
	}
}
?>