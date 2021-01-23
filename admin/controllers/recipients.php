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
 * Recipients Controller
 */
class UrlpollsControllerRecipients extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_URLPOLLS_RECIPIENTS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Recipient', $prefix = 'UrlpollsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('recipient.export', 'com_urlpolls') && $user->authorise('core.export', 'com_urlpolls'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Recipients');
			// get the data to export
			$data = $model->getExportData($pks);
			if (UrlpollsHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				UrlpollsHelper::xls($data,'Recipients_'.$date->format('jS_F_Y'),'Recipients exported ('.$date->format('jS F, Y').')','recipients');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_URLPOLLS_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_urlpolls&view=recipients', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('recipient.import', 'com_urlpolls') && $user->authorise('core.import', 'com_urlpolls'))
		{
			// Get the import model
			$model = $this->getModel('Recipients');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (UrlpollsHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('recipient_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'recipients');
				$session->set('dataType_VDM_IMPORTINTO', 'recipient');
				// Redirect to import view.
				$message = JText::_('COM_URLPOLLS_IMPORT_SELECT_FILE_FOR_RECIPIENTS');
				$this->setRedirect(JRoute::_('index.php?option=com_urlpolls&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_URLPOLLS_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_urlpolls&view=recipients', false), $message, 'error');
		return;
	}
}
