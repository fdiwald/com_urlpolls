<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Florian Diwald 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			30th Dezember, 2020
	@created		26th Dezember, 2020
	@package		URL Polls
	@subpackage		recipientsfilteranswer.php
	@author			Florian Diwald <https://github.com/fdiwald/com_urlpolls>	
	@copyright		Copyright (C) 2020. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
  ____  _____  _____  __  __  __      __       ___  _____  __  __  ____  _____  _  _  ____  _  _  ____ 
 (_  _)(  _  )(  _  )(  \/  )(  )    /__\     / __)(  _  )(  \/  )(  _ \(  _  )( \( )( ___)( \( )(_  _)
.-_)(   )(_)(  )(_)(  )    (  )(__  /(__)\   ( (__  )(_)(  )    (  )___/ )(_)(  )  (  )__)  )  (   )(  
\____) (_____)(_____)(_/\/\_)(____)(__)(__)   \___)(_____)(_/\/\_)(__)  (_____)(_)\_)(____)(_)\_) (__) 

/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Recipientsfilteranswer Form Field class for the Urlpolls component
 */
class JFormFieldRecipientsfilteranswer extends JFormFieldList
{
	/**
	 * The recipientsfilteranswer field type.
	 *
	 * @var		string
	 */
	public $type = 'recipientsfilteranswer';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('answer'));
		$query->from($db->quoteName('#__urlpolls_recipient'));
		$query->order($db->quoteName('answer') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();
		$_filter = array();

		if ($results)
		{
			// get recipientsmodel
			$model = UrlpollsHelper::getModel('recipients');
			$results = array_unique($results);
			foreach ($results as $answer)
			{
				// Translate the answer selection
				$text = $model->selectionTranslation($answer,'answer');
				// Now add the answer and its text to the options array
				$_filter[] = JHtml::_('select.option', $answer, JText::_($text));
			}
		}
		return $_filter;
	}
}
