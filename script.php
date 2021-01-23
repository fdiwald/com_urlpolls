<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Florian Diwald 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			30th Dezember, 2020
	@created		26th Dezember, 2020
	@package		URL Polls
	@subpackage		script.php
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

JHTML::_('behavior.modal');

/**
 * Script File of Urlpolls Component
 */
class com_urlpollsInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $parent)
	{
		// Get Application object
		$app = JFactory::getApplication();

		// Get The Database object
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Person alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.person') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$person_found = $db->getNumRows();
		// Now check if there were any rows
		if ($person_found)
		{
			// Since there are load the needed  person type ids
			$person_ids = $db->loadColumn();
			// Remove Person from the content type table
			$person_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.person') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($person_condition);
			$db->setQuery($query);
			// Execute the query to remove Person items
			$person_done = $db->execute();
			if ($person_done)
			{
				// If successfully remove Person add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.person) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Person items from the contentitem tag map table
			$person_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.person') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($person_condition);
			$db->setQuery($query);
			// Execute the query to remove Person items
			$person_done = $db->execute();
			if ($person_done)
			{
				// If successfully remove Person add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.person) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Person items from the ucm content table
			$person_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_urlpolls.person') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($person_condition);
			$db->setQuery($query);
			// Execute the query to remove Person items
			$person_done = $db->execute();
			if ($person_done)
			{
				// If successfully removed Person add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.person) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Person items are cleared from DB
			foreach ($person_ids as $person_id)
			{
				// Remove Person items from the ucm base table
				$person_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $person_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($person_condition);
				$db->setQuery($query);
				// Execute the query to remove Person items
				$db->execute();

				// Remove Person items from the ucm history table
				$person_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $person_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($person_condition);
				$db->setQuery($query);
				// Execute the query to remove Person items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Poll alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.poll') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$poll_found = $db->getNumRows();
		// Now check if there were any rows
		if ($poll_found)
		{
			// Since there are load the needed  poll type ids
			$poll_ids = $db->loadColumn();
			// Remove Poll from the content type table
			$poll_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.poll') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($poll_condition);
			$db->setQuery($query);
			// Execute the query to remove Poll items
			$poll_done = $db->execute();
			if ($poll_done)
			{
				// If successfully remove Poll add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.poll) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Poll items from the contentitem tag map table
			$poll_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.poll') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($poll_condition);
			$db->setQuery($query);
			// Execute the query to remove Poll items
			$poll_done = $db->execute();
			if ($poll_done)
			{
				// If successfully remove Poll add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.poll) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Poll items from the ucm content table
			$poll_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_urlpolls.poll') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($poll_condition);
			$db->setQuery($query);
			// Execute the query to remove Poll items
			$poll_done = $db->execute();
			if ($poll_done)
			{
				// If successfully removed Poll add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.poll) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Poll items are cleared from DB
			foreach ($poll_ids as $poll_id)
			{
				// Remove Poll items from the ucm base table
				$poll_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $poll_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($poll_condition);
				$db->setQuery($query);
				// Execute the query to remove Poll items
				$db->execute();

				// Remove Poll items from the ucm history table
				$poll_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $poll_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($poll_condition);
				$db->setQuery($query);
				// Execute the query to remove Poll items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Recipient alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.recipient') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$recipient_found = $db->getNumRows();
		// Now check if there were any rows
		if ($recipient_found)
		{
			// Since there are load the needed  recipient type ids
			$recipient_ids = $db->loadColumn();
			// Remove Recipient from the content type table
			$recipient_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.recipient') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($recipient_condition);
			$db->setQuery($query);
			// Execute the query to remove Recipient items
			$recipient_done = $db->execute();
			if ($recipient_done)
			{
				// If successfully remove Recipient add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.recipient) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Recipient items from the contentitem tag map table
			$recipient_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_urlpolls.recipient') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($recipient_condition);
			$db->setQuery($query);
			// Execute the query to remove Recipient items
			$recipient_done = $db->execute();
			if ($recipient_done)
			{
				// If successfully remove Recipient add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.recipient) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Recipient items from the ucm content table
			$recipient_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_urlpolls.recipient') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($recipient_condition);
			$db->setQuery($query);
			// Execute the query to remove Recipient items
			$recipient_done = $db->execute();
			if ($recipient_done)
			{
				// If successfully removed Recipient add queued success message.
				$app->enqueueMessage(JText::_('The (com_urlpolls.recipient) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Recipient items are cleared from DB
			foreach ($recipient_ids as $recipient_id)
			{
				// Remove Recipient items from the ucm base table
				$recipient_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $recipient_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($recipient_condition);
				$db->setQuery($query);
				// Execute the query to remove Recipient items
				$db->execute();

				// Remove Recipient items from the ucm history table
				$recipient_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $recipient_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($recipient_condition);
				$db->setQuery($query);
				// Execute the query to remove Recipient items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove urlpolls assets from the assets table
		$urlpolls_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_urlpolls%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($urlpolls_condition);
		$db->setQuery($query);
		$recipient_done = $db->execute();
		if ($recipient_done)
		{
			// If successfully removed urlpolls add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:florian@diwald.net">florian@diwald.net</a>.
		<br />We at Florian Diwald are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://github.com/fdiwald/com_urlpolls" target="_blank">https://github.com/fdiwald/com_urlpolls</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		// check if the PHPExcel stuff is still around
		if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_urlpolls/helpers/PHPExcel.php'))
		{
			// We need to remove this old PHPExcel folder
			$this->removeFolder(JPATH_ADMINISTRATOR . '/components/com_urlpolls/helpers/PHPExcel');
			// We need to remove this old PHPExcel file
			JFile::delete(JPATH_ADMINISTRATOR . '/components/com_urlpolls/helpers/PHPExcel.php');
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// We check if we have dynamic folders to copy
		$this->setDynamicF0ld3rs($app, $parent);
		// set the default component settings
		if ($type === 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the person content type object.
			$person = new stdClass();
			$person->type_title = 'Urlpolls Person';
			$person->type_alias = 'com_urlpolls.person';
			$person->table = '{"special": {"dbtable": "#__urlpolls_person","key": "id","type": "Person","prefix": "urlpollsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$person->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "personname","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"personname":"personname"}}';
			$person->router = 'UrlpollsHelperRoute::getPersonRoute';
			$person->content_history_options = '{"formFile": "administrator/components/com_urlpolls/models/forms/person.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$person_Inserted = $db->insertObject('#__content_types', $person);

			// Create the poll content type object.
			$poll = new stdClass();
			$poll->type_title = 'Urlpolls Poll';
			$poll->type_alias = 'com_urlpolls.poll';
			$poll->table = '{"special": {"dbtable": "#__urlpolls_poll","key": "id","type": "Poll","prefix": "urlpollsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$poll->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "pollname","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"pollname":"pollname"}}';
			$poll->router = 'UrlpollsHelperRoute::getPollRoute';
			$poll->content_history_options = '{"formFile": "administrator/components/com_urlpolls/models/forms/poll.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$poll_Inserted = $db->insertObject('#__content_types', $poll);

			// Create the recipient content type object.
			$recipient = new stdClass();
			$recipient->type_title = 'Urlpolls Recipient';
			$recipient->type_alias = 'com_urlpolls.recipient';
			$recipient->table = '{"special": {"dbtable": "#__urlpolls_recipient","key": "id","type": "Recipient","prefix": "urlpollsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$recipient->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"pollid":"pollid","personid":"personid","answer":"answer","recipientcode":"recipientcode"}}';
			$recipient->router = 'UrlpollsHelperRoute::getRecipientRoute';
			$recipient->content_history_options = '{"formFile": "administrator/components/com_urlpolls/models/forms/recipient.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","pollid","personid","answer"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "pollid","targetTable": "#__urlpolls_poll","targetColumn": "id","displayColumn": "pollname"},{"sourceColumn": "personid","targetTable": "#__urlpolls_person","targetColumn": "id","displayColumn": "personname"}]}';

			// Set the object into the content types table.
			$recipient_Inserted = $db->insertObject('#__content_types', $recipient);


			// Install the global extenstion params.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Florian Diwald","autorEmail":"florian@diwald.net","check_in":"-1 day","save_history":"1","history_limit":"10"}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_urlpolls')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			echo '<a target="_blank" href="https://github.com/fdiwald/com_urlpolls" title="URL Polls">
				<img src="components/com_urlpolls/assets/images/vdm-component.jpg"/>
				</a>';
		}
		// do any updates needed
		if ($type === 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the person content type object.
			$person = new stdClass();
			$person->type_title = 'Urlpolls Person';
			$person->type_alias = 'com_urlpolls.person';
			$person->table = '{"special": {"dbtable": "#__urlpolls_person","key": "id","type": "Person","prefix": "urlpollsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$person->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "personname","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"personname":"personname"}}';
			$person->router = 'UrlpollsHelperRoute::getPersonRoute';
			$person->content_history_options = '{"formFile": "administrator/components/com_urlpolls/models/forms/person.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if person type is already in content_type DB.
			$person_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($person->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$person->type_id = $db->loadResult();
				$person_Updated = $db->updateObject('#__content_types', $person, 'type_id');
			}
			else
			{
				$person_Inserted = $db->insertObject('#__content_types', $person);
			}

			// Create the poll content type object.
			$poll = new stdClass();
			$poll->type_title = 'Urlpolls Poll';
			$poll->type_alias = 'com_urlpolls.poll';
			$poll->table = '{"special": {"dbtable": "#__urlpolls_poll","key": "id","type": "Poll","prefix": "urlpollsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$poll->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "pollname","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"pollname":"pollname"}}';
			$poll->router = 'UrlpollsHelperRoute::getPollRoute';
			$poll->content_history_options = '{"formFile": "administrator/components/com_urlpolls/models/forms/poll.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if poll type is already in content_type DB.
			$poll_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($poll->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$poll->type_id = $db->loadResult();
				$poll_Updated = $db->updateObject('#__content_types', $poll, 'type_id');
			}
			else
			{
				$poll_Inserted = $db->insertObject('#__content_types', $poll);
			}

			// Create the recipient content type object.
			$recipient = new stdClass();
			$recipient->type_title = 'Urlpolls Recipient';
			$recipient->type_alias = 'com_urlpolls.recipient';
			$recipient->table = '{"special": {"dbtable": "#__urlpolls_recipient","key": "id","type": "Recipient","prefix": "urlpollsTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$recipient->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"pollid":"pollid","personid":"personid","answer":"answer","recipientcode":"recipientcode"}}';
			$recipient->router = 'UrlpollsHelperRoute::getRecipientRoute';
			$recipient->content_history_options = '{"formFile": "administrator/components/com_urlpolls/models/forms/recipient.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","pollid","personid","answer"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "pollid","targetTable": "#__urlpolls_poll","targetColumn": "id","displayColumn": "pollname"},{"sourceColumn": "personid","targetTable": "#__urlpolls_person","targetColumn": "id","displayColumn": "personname"}]}';

			// Check if recipient type is already in content_type DB.
			$recipient_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($recipient->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$recipient->type_id = $db->loadResult();
				$recipient_Updated = $db->updateObject('#__content_types', $recipient, 'type_id');
			}
			else
			{
				$recipient_Inserted = $db->insertObject('#__content_types', $recipient);
			}


			echo '<a target="_blank" href="https://github.com/fdiwald/com_urlpolls" title="URL Polls">
				<img src="components/com_urlpolls/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 1.0.0 Was Successful! Let us know if anything is not working as expected.</h3>';
		}
		return true;
	}

	/**
	 * Remove folders with files
	 * 
	 * @param   string   $dir     The path to folder to remove
	 * @param   boolean  $ignore  The folders and files to ignore and not remove
	 *
	 * @return  boolean   True in all is removed
	 * 
	 */
	protected function removeFolder($dir, $ignore = false)
	{
		if (JFolder::exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir);
			$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			// remove ending /
			$dir = rtrim($dir, '/');
			// now loop the files & folders
			foreach ($it as $file)
			{
				if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
				// set file dir
				$file_dir = $file->getPathname();
				// check if this is a dir or a file
				if ($file->isDir())
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFolder::delete($file_dir);
				}
				else
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFile::delete($file_dir);
				}
			}
			// delete the root folder if not ignore found
			if (!$this->checkArray($ignore))
			{
				return JFolder::delete($dir);
			}
			return true;
		}
		return false;
	}

	/**
	 * Check if have an array with a length
	 *
	 * @input	array   The array to check
	 *
	 * @returns bool/int  number of items in array on success
	 */
	protected function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && ($nr = count((array)$array)) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return $this->checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	 * Method to set/copy dynamic folders into place (use with caution)
	 *
	 * @return void
	 */
	protected function setDynamicF0ld3rs($app, $parent)
	{
		// get the instalation path
		$installer = $parent->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = JFolder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = array('media','admin','site'); // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!JFolder::copy($src, $dest, '', true))
					{
						$app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
