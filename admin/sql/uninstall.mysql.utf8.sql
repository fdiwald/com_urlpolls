DROP TABLE IF EXISTS `#__urlpolls_person`;
DROP TABLE IF EXISTS `#__urlpolls_poll`;
DROP TABLE IF EXISTS `#__urlpolls_recipient`;


--
-- Always insure this column rules is reversed to Joomla defaults on uninstall. (as on 1st Dec 2020)
--
ALTER TABLE `#__assets` CHANGE `rules` `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.';
