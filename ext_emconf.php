<?php

########################################################################
# Extension Manager/Repository config file for ext "tt_board".
#
# Auto generated 19-01-2011 16:01
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Frontend TCE',
	'description' => 'It brings the deprecated TYPO3 4.x class tslib_feTCE and the processScript FEData setup back into TYPO3 7.x. This is needed by tt_board and tt_guest. For TYPO3 6.2 use version 0.0.1. For form MAIL and form wizards use the extension compatibility6.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Franz Holzinger',
	'author_email' => 'franz@ttproducts.de',
	'author_company' => 'jambage.com',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.3-7.99.99',
			'typo3' => '7.2.0-8.99.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

