<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Florian Diwald 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			30th Dezember, 2020
	@created		26th Dezember, 2020
	@package		URL Polls
	@subpackage		answer.php
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

use Joomla\Utilities\ArrayHelper;

/**
 * Urlpolls Answer Model
 */
class UrlpollsModelAnswer extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_urlpolls.answer';

	/**
	 * Model user data.
	 *
	 * @var        strings
	 */
	protected $user;
	protected $userId;
	protected $guest;
	protected $groups;
	protected $levels;
	protected $app;
	protected $input;
	protected $uikitComp;

	/**
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		// Get the itme main id
		$id = $this->input->getInt('id', null);
		$this->setState('answer.id', $id);

		// Load the parameters.
		$params = $this->app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	/**
	 * Method to get article data.
	 *
	 * @param   integer  $pk  The id of the article.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$this->user = JFactory::getUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->initSet = true;

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('answer.id');
		
		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				// Get a db connection.
				$db = JFactory::getDbo();

				// Create a new query object.
				$query = $db->getQuery(true);

				// Get from #__urlpolls_person as a
				$query->select($db->quoteName(
			array('a.personname'),
			array('personname')));
				$query->from($db->quoteName('#__urlpolls_person', 'a'));

				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				// Load the results as a stdClass object.
				$data = $db->loadObject();

				if (empty($data))
				{
					$app = JFactory::getApplication();
					// If no data is found redirect to default page and show warning.
					$app->enqueueMessage(JText::_('COM_URLPOLLS_NOT_FOUND_OR_ACCESS_DENIED'), 'warning');
					$app->redirect(JURI::root());
					return false;
				}

				// set data object to item.
				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseWarning(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}


/***[JCBGUI.site_view.php_model.26.$$$$]***/
	public function saveAnswer($recipientcode, $answer)
	{
		// save the persons answer
		if(!$this->checkPollStatus($recipientcode))
		{			
			return;
		}

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$fields = array($db->quoteName('answer') . '=' . $answer);
		$where = array(
			$db->quoteName('recipientcode') . '=' . $db->quote($recipientcode)
		);
		$query->update($db->quoteName('#__urlpolls_recipient'))->set($fields)->where($where);
		$db->setQuery($query);

		return $db->execute();
	}

	private function checkPollStatus($recipientcode)
	{
		// reads the poll data for the given pollcode
		// returns false on error
		$app = \Joomla\CMS\Factory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query
			->select(array(
				$db->quoteName('recipient.published', 'recipient_published'),
				$db->quoteName('poll.published', 'poll_published')))
			->from($db->quoteName('#__urlpolls_recipient', 'recipient'))
			->leftjoin($db->quoteName('#__urlpolls_poll', 'poll') . ' ON recipient.pollid = poll.id')
			->where('recipient.recipientcode= ' . $db->quote($recipientcode));
		
		$db->setQuery($query);
		$data = $db->loadAssoc();
		if(!isSet($data))
		{
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_URLPOLLS_THE_SUBMITTED_POLL_CODE_IS_UNKNOWN'), 'warning');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return false;
		}
		if($data['recipient_published'] != 1)
		{
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_URLPOLLS_YOU_ARE_CURRENTLY_NOT_ALLOWED_TO_PARTICIPATE_IN_THE_POLL'), 'warning');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return false;
		}
		if($data['poll_published'] != 1)
		{
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_URLPOLLS_THIS_POLL_IS_CURRENTLY_NOT_PUBLIC'), 'warning');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return false;
		}

		return true;
	}
/***[/JCBGUI$$$$]***/

}
