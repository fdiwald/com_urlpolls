<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Florian Diwald 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			30th Dezember, 2020
	@created		26th Dezember, 2020
	@package		URL Polls
	@subpackage		recipients.php
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
 * Recipients Model
 */
class UrlpollsModelRecipients extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
        {
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.access','access',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'g.pollname','pollid',
				'h.personname','personid',
				'a.answer','answer'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Check if the form was submitted
		$formSubmited = $app->input->post->get('form_submited');

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		if ($formSubmited)
		{
			$access = $app->input->post->get('access');
			$this->setState('filter.access', $access);
		}

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$pollid = $this->getUserStateFromRequest($this->context . '.filter.pollid', 'filter_pollid');
		if ($formSubmited)
		{
			$pollid = $app->input->post->get('pollid');
			$this->setState('filter.pollid', $pollid);
		}

		$personid = $this->getUserStateFromRequest($this->context . '.filter.personid', 'filter_personid');
		if ($formSubmited)
		{
			$personid = $app->input->post->get('personid');
			$this->setState('filter.personid', $personid);
		}

		$answer = $this->getUserStateFromRequest($this->context . '.filter.answer', 'filter_answer');
		if ($formSubmited)
		{
			$answer = $app->input->post->get('answer');
			$this->setState('filter.answer', $answer);
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// set selection value to a translatable value
		if (UrlpollsHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert answer
				$item->answer = $this->selectionTranslation($item->answer, 'answer');
			}
		}

        
		// return items
		return $items;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return translatable string
	 */
	public function selectionTranslation($value,$name)
	{
		// Array of answer language strings
		if ($name === 'answer')
		{
			$answerArray = array(
				0 => 'COM_URLPOLLS_RECIPIENT_UNANSWERED',
				1 => 'COM_URLPOLLS_RECIPIENT_ACCEPTED',
				2 => 'COM_URLPOLLS_RECIPIENT_REJECTED'
			);
			// Now check if value is found in this array
			if (isset($answerArray[$value]) && UrlpollsHelper::checkString($answerArray[$value]))
			{
				return $answerArray[$value];
			}
		}
		return $value;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the urlpolls_item table
		$query->from($db->quoteName('#__urlpolls_recipient', 'a'));

		// From the urlpolls_poll table.
		$query->select($db->quoteName('g.pollname','pollid_pollname'));
		$query->join('LEFT', $db->quoteName('#__urlpolls_poll', 'g') . ' ON (' . $db->quoteName('a.pollid') . ' = ' . $db->quoteName('g.id') . ')');

		// From the urlpolls_person table.
		$query->select($db->quoteName('h.personname','personid_personname'));
		$query->join('LEFT', $db->quoteName('#__urlpolls_person', 'h') . ' ON (' . $db->quoteName('a.personid') . ' = ' . $db->quoteName('h.id') . ')');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		// Filter by access level.
		$_access = $this->getState('filter.access');
		if ($_access && is_numeric($_access))
		{
			$query->where('a.access = ' . (int) $_access);
		}
		elseif (UrlpollsHelper::checkArray($_access))
		{
			// Secure the array for the query
			$_access = ArrayHelper::toInteger($_access);
			// Filter by the Access Array.
			$query->where('a.access IN (' . implode(',', $_access) . ')');
		}
		// Implement View Level Access
		if (!$user->authorise('core.options', 'com_urlpolls'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.pollid LIKE '.$search.' OR g.pollname LIKE '.$search.' OR a.personid LIKE '.$search.' OR h.personname LIKE '.$search.' OR a.answer LIKE '.$search.' OR a.recipientcode LIKE '.$search.')');
			}
		}

		// Filter by Pollid.
		$_pollid = $this->getState('filter.pollid');
		if (is_numeric($_pollid))
		{
			if (is_float($_pollid))
			{
				$query->where('a.pollid = ' . (float) $_pollid);
			}
			else
			{
				$query->where('a.pollid = ' . (int) $_pollid);
			}
		}
		elseif (UrlpollsHelper::checkString($_pollid))
		{
			$query->where('a.pollid = ' . $db->quote($db->escape($_pollid)));
		}
		elseif (UrlpollsHelper::checkArray($_pollid))
		{
			// Secure the array for the query
			$_pollid = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (UrlpollsHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_pollid);
			// Filter by the Pollid Array.
			$query->where('a.pollid IN (' . implode(',', $_pollid) . ')');
		}
		// Filter by Personid.
		$_personid = $this->getState('filter.personid');
		if (is_numeric($_personid))
		{
			if (is_float($_personid))
			{
				$query->where('a.personid = ' . (float) $_personid);
			}
			else
			{
				$query->where('a.personid = ' . (int) $_personid);
			}
		}
		elseif (UrlpollsHelper::checkString($_personid))
		{
			$query->where('a.personid = ' . $db->quote($db->escape($_personid)));
		}
		elseif (UrlpollsHelper::checkArray($_personid))
		{
			// Secure the array for the query
			$_personid = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (UrlpollsHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_personid);
			// Filter by the Personid Array.
			$query->where('a.personid IN (' . implode(',', $_personid) . ')');
		}
		// Filter by Answer.
		$_answer = $this->getState('filter.answer');
		if (is_numeric($_answer))
		{
			if (is_float($_answer))
			{
				$query->where('a.answer = ' . (float) $_answer);
			}
			else
			{
				$query->where('a.answer = ' . (int) $_answer);
			}
		}
		elseif (UrlpollsHelper::checkString($_answer))
		{
			$query->where('a.answer = ' . $db->quote($db->escape($_answer)));
		}
		elseif (UrlpollsHelper::checkArray($_answer))
		{
			// Secure the array for the query
			$_answer = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (UrlpollsHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_answer);
			// Filter by the Answer Array.
			$query->where('a.answer IN (' . implode(',', $_answer) . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get list export data.
	 *
	 * @param   array  $pks  The ids of the items to get
	 * @param   JUser  $user  The user making the request
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getExportData($pks, $user = null)
	{
		// setup the query
		if (($pks_size = UrlpollsHelper::checkArray($pks)) !== false || 'bulk' === $pks)
		{
			// Set a value to know this is export method. (USE IN CUSTOM CODE TO ALTER OUTCOME)
			$_export = true;
			// Get the user object if not set.
			if (!isset($user) || !UrlpollsHelper::checkObject($user))
			{
				$user = JFactory::getUser();
			}
			// Create a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the urlpolls_recipient table
			$query->from($db->quoteName('#__urlpolls_recipient', 'a'));
			// The bulk export path
			if ('bulk' === $pks)
			{
				$query->where('a.id > 0');
			}
			// A large array of ID's will not work out well
			elseif ($pks_size > 500)
			{
				// Use lowest ID
				$query->where('a.id >= ' . (int) min($pks));
				// Use highest ID
				$query->where('a.id <= ' . (int) max($pks));
			}
			// The normal default path
			else
			{
				$query->where('a.id IN (' . implode(',',$pks) . ')');
			}
			// Implement View Level Access
			if (!$user->authorise('core.options', 'com_urlpolls'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}

			// Order the results by ordering
			$query->order('a.ordering  ASC');

			// Load the items
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$items = $db->loadObjectList();

				// Set values to display correctly.
				if (UrlpollsHelper::checkArray($items))
				{
					foreach ($items as $nr => &$item)
					{
						// unset the values we don't want exported.
						unset($item->asset_id);
						unset($item->checked_out);
						unset($item->checked_out_time);
					}
				}
				// Add headers to items array.
				$headers = $this->getExImPortHeaders();
				if (UrlpollsHelper::checkObject($headers))
				{
					array_unshift($items,$headers);
				}
				return $items;
			}
		}
		return false;
	}

	/**
	* Method to get header.
	*
	* @return mixed  An array of data items on success, false on failure.
	*/
	public function getExImPortHeaders()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// get the columns
		$columns = $db->getTableColumns("#__urlpolls_recipient");
		if (UrlpollsHelper::checkArray($columns))
		{
			// remove the headers you don't import/export.
			unset($columns['asset_id']);
			unset($columns['checked_out']);
			unset($columns['checked_out_time']);
			$headers = new stdClass();
			foreach ($columns as $column => $type)
			{
				$headers->{$column} = $column;
			}
			return $headers;
		}
		return false;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		// Check if the value is an array
		$_access = $this->getState('filter.access');
		if (UrlpollsHelper::checkArray($_access))
		{
			$id .= ':' . implode(':', $_access);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_access)
		 || UrlpollsHelper::checkString($_access))
		{
			$id .= ':' . $_access;
		}
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		// Check if the value is an array
		$_pollid = $this->getState('filter.pollid');
		if (UrlpollsHelper::checkArray($_pollid))
		{
			$id .= ':' . implode(':', $_pollid);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_pollid)
		 || UrlpollsHelper::checkString($_pollid))
		{
			$id .= ':' . $_pollid;
		}
		// Check if the value is an array
		$_personid = $this->getState('filter.personid');
		if (UrlpollsHelper::checkArray($_personid))
		{
			$id .= ':' . implode(':', $_personid);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_personid)
		 || UrlpollsHelper::checkString($_personid))
		{
			$id .= ':' . $_personid;
		}
		// Check if the value is an array
		$_answer = $this->getState('filter.answer');
		if (UrlpollsHelper::checkArray($_answer))
		{
			$id .= ':' . implode(':', $_answer);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_answer)
		 || UrlpollsHelper::checkString($_answer))
		{
			$id .= ':' . $_answer;
		}

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time = JComponentHelper::getParams('com_urlpolls')->get('check_in');

		if ($time)
		{

			// Get a db connection.
			$db = JFactory::getDbo();
			// reset query
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__urlpolls_recipient'));
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date
				$date = JFactory::getDate()->modify($time)->toSql();
				// reset query
				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
					$db->quoteName('checked_out') . '=0'
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('checked_out') . '!=0', 
					$db->quoteName('checked_out_time') . '<\''.$date.'\''
				);

				// Check table
				$query->update($db->quoteName('#__urlpolls_recipient'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
