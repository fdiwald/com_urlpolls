<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Florian Diwald 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			30th Dezember, 2020
	@created		26th Dezember, 2020
	@package		URL Polls
	@subpackage		view.html.php
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

/**
 * Urlpolls View class for the Recipients
 */
class UrlpollsViewRecipients extends JViewLegacy
{
	/**
	 * Recipients view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			UrlpollsHelper::addSubmenu('recipients');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		// Load the filter form from xml.
		$this->filterForm = $this->get('FilterForm');
		// Load the active filters.
		$this->activeFilters = $this->get('ActiveFilters');
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'DESC'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = UrlpollsHelper::getActions('recipient');
		$this->canEdit = $this->canDo->get('core.edit');
		$this->canState = $this->canDo->get('core.edit.state');
		$this->canCreate = $this->canDo->get('core.create');
		$this->canDelete = $this->canDo->get('core.delete');
		$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_URLPOLLS_RECIPIENTS'), 'archive');
		JHtmlSidebar::setAction('index.php?option=com_urlpolls&view=recipients');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('recipient.add');
		}

		// Only load if there are items
		if (UrlpollsHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('recipient.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('recipients.publish');
				JToolBarHelper::unpublishList('recipients.unpublish');
				JToolBarHelper::archiveList('recipients.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('recipients.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'recipients.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('recipients.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('recipient.export'))
			{
				JToolBarHelper::custom('recipients.exportData', 'download', '', 'COM_URLPOLLS_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('recipient.import'))
		{
			JToolBarHelper::custom('recipients.importData', 'upload', '', 'COM_URLPOLLS_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = UrlpollsHelper::getHelpUrl('recipients');
		if (UrlpollsHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_URLPOLLS_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_urlpolls');
		}

		// Only load published batch if state and batch is allowed
		if ($this->canState && $this->canBatch)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_URLPOLLS_KEEP_ORIGINAL_STATE'),
				'batch[published]',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
			);
		}

		// Only load access batch if create, edit and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_URLPOLLS_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Only load Pollid Pollname batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Pollid Pollname Selection
			$this->pollidPollnameOptions = JFormHelper::loadFieldType('Poll')->options;
			// We do some sanitation for Pollid Pollname filter
			if (UrlpollsHelper::checkArray($this->pollidPollnameOptions) &&
				isset($this->pollidPollnameOptions[0]->value) &&
				!UrlpollsHelper::checkString($this->pollidPollnameOptions[0]->value))
			{
				unset($this->pollidPollnameOptions[0]);
			}
			// Pollid Pollname Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_URLPOLLS_RECIPIENT_POLLID_LABEL').' -',
				'batch[pollid]',
				JHtml::_('select.options', $this->pollidPollnameOptions, 'value', 'text')
			);
		}

		// Only load Personid Personname batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Personid Personname Selection
			$this->personidPersonnameOptions = JFormHelper::loadFieldType('Person')->options;
			// We do some sanitation for Personid Personname filter
			if (UrlpollsHelper::checkArray($this->personidPersonnameOptions) &&
				isset($this->personidPersonnameOptions[0]->value) &&
				!UrlpollsHelper::checkString($this->personidPersonnameOptions[0]->value))
			{
				unset($this->personidPersonnameOptions[0]);
			}
			// Personid Personname Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_URLPOLLS_RECIPIENT_PERSONID_LABEL').' -',
				'batch[personid]',
				JHtml::_('select.options', $this->personidPersonnameOptions, 'value', 'text')
			);
		}

		// Only load Answer batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Answer Selection
			$this->answerOptions = JFormHelper::loadFieldType('recipientsfilteranswer')->options;
			// We do some sanitation for Answer filter
			if (UrlpollsHelper::checkArray($this->answerOptions) &&
				isset($this->answerOptions[0]->value) &&
				!UrlpollsHelper::checkString($this->answerOptions[0]->value))
			{
				unset($this->answerOptions[0]);
			}
			// Answer Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_URLPOLLS_RECIPIENT_ANSWER_LABEL').' -',
				'batch[answer]',
				JHtml::_('select.options', $this->answerOptions, 'value', 'text')
			);
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_URLPOLLS_RECIPIENTS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_urlpolls/assets/css/recipients.css", (UrlpollsHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return UrlpollsHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return UrlpollsHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'g.pollname' => JText::_('COM_URLPOLLS_RECIPIENT_POLLID_LABEL'),
			'h.personname' => JText::_('COM_URLPOLLS_RECIPIENT_PERSONID_LABEL'),
			'a.answer' => JText::_('COM_URLPOLLS_RECIPIENT_ANSWER_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
