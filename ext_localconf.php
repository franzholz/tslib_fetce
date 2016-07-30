<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController'] = array(
	'className' => 'JambageCom\\TslibFetce\\Hooks\\TypoScriptFrontendTceController',
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['configArrayPostProc'][] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/FrontendHooks.php:JambageCom\\TslibFetce\\Hooks\\FrontendHooks->getFeDataConfigArray';

?>