<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Florian Diwald 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			30th Dezember, 2020
	@created		26th Dezember, 2020
	@package		URL Polls
	@subpackage		default.php
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

JHtml::_('behavior.tooltip');

?>
<div id="j-main-container">
	<div class="span9">
		<?php echo JHtml::_('bootstrap.startAccordion', 'dashboard_left', array('active' => 'main')); ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'dashboard_left', 'cPanel', 'main'); ?>
				<?php echo $this->loadTemplate('main');?>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</div>
	<div class="span3">
		<?php echo JHtml::_('bootstrap.startAccordion', 'dashboard_right', array('active' => 'vdm')); ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'dashboard_right', 'Florian Diwald', 'vdm'); ?>
				<?php echo $this->loadTemplate('vdm');?>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</div>
</div>