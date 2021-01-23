<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Florian Diwald 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			30th Dezember, 2020
	@created		26th Dezember, 2020
	@package		URL Polls
	@subpackage		recipients_fullwidth.php
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

// set the defaults
$items = $displayData->vvvrecipients;
$user = JFactory::getUser();
$id = $displayData->item->id;
// set the edit URL
$edit = "index.php?option=com_urlpolls&view=recipients&task=recipient.edit";
// set a return value
$return = ($id) ? "index.php?option=com_urlpolls&view=poll&layout=edit&id=" . $id : "";
// check for a return value
$jinput = JFactory::getApplication()->input;
if ($_return = $jinput->get('return', null, 'base64'))
{
	$return .= "&return=" . $_return;
}
// check if return value was set
if (UrlpollsHelper::checkString($return))
{
	// set the referral values
	$ref = ($id) ? "&ref=poll&refid=" . $id . "&return=" . urlencode(base64_encode($return)) : "&return=" . urlencode(base64_encode($return));
}
else
{
	$ref = ($id) ? "&ref=poll&refid=" . $id : "";
}

?>
<div class="form-vertical">
<?php if (UrlpollsHelper::checkArray($items)): ?>
<table class="footable table data recipients" data-sorting="true" data-paging="true" data-paging-size="20" data-filtering="true">
<thead>
	<tr>
		<th data-breakpoints="xs sm">
			<?php echo JText::_('COM_URLPOLLS_RECIPIENT_POLLID_LABEL'); ?>
		</th>
		<th>
			<?php echo JText::_('COM_URLPOLLS_RECIPIENT_PERSONID_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm">
			<?php echo JText::_('COM_URLPOLLS_RECIPIENT_ANSWER_LABEL'); ?>
		</th>
		<th width="10" data-breakpoints="xs sm md">
			<?php echo JText::_('COM_URLPOLLS_RECIPIENT_STATUS'); ?>
		</th>
		<th width="5" data-type="number" data-breakpoints="xs sm md">
			<?php echo JText::_('COM_URLPOLLS_RECIPIENT_ID'); ?>
		</th>
	</tr>
</thead>
<tbody>
<?php foreach ($items as $i => $item): ?>
	<?php
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0;
		$userChkOut = JFactory::getUser($item->checked_out);
		$canDo = UrlpollsHelper::getActions('recipient',$item,'recipients');
	?>
	<tr>
		<td>
			<?php echo $displayData->escape($item->pollid_pollname); ?>
		</td>
		<td>
			<?php if ($user->authorise('core.edit', 'com_urlpolls.person.' . (int)$item->personid)): ?>
				<a href="index.php?option=com_urlpolls&view=persons&task=person.edit&id=<?php echo $item->personid; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->personid_personname); ?></a>
			<?php else: ?>
				<?php echo $displayData->escape($item->personid_personname); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php if ($canDo->get('core.edit')): ?>
				<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?><?php echo $ref; ?>"><?php echo JText::_($item->answer); ?></a>
				<?php if ($item->checked_out): ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'recipients.', $canCheckin); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo JText::_($item->answer); ?>
			<?php endif; ?>
		</td>
		<?php if ($item->published == 1): ?>
			<td class="center"  data-value="1">
				<span class="status-metro status-published" title="<?php echo JText::_('COM_URLPOLLS_PUBLISHED');  ?>">
					<?php echo JText::_('COM_URLPOLLS_PUBLISHED'); ?>
				</span>
			</td>
		<?php elseif ($item->published == 0): ?>
			<td class="center"  data-value="2">
				<span class="status-metro status-inactive" title="<?php echo JText::_('COM_URLPOLLS_INACTIVE');  ?>">
					<?php echo JText::_('COM_URLPOLLS_INACTIVE'); ?>
				</span>
			</td>
		<?php elseif ($item->published == 2): ?>
			<td class="center"  data-value="3">
				<span class="status-metro status-archived" title="<?php echo JText::_('COM_URLPOLLS_ARCHIVED');  ?>">
					<?php echo JText::_('COM_URLPOLLS_ARCHIVED'); ?>
				</span>
			</td>
		<?php elseif ($item->published == -2): ?>
			<td class="center"  data-value="4">
				<span class="status-metro status-trashed" title="<?php echo JText::_('COM_URLPOLLS_TRASHED');  ?>">
					<?php echo JText::_('COM_URLPOLLS_TRASHED'); ?>
				</span>
			</td>
		<?php endif; ?>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php endif; ?>
</div>
