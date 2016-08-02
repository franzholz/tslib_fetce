<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][] = 'EXT:' . $_EXTKEY . '/Classes/Controller/TypoScriptFrontendTceController.php:JambageCom\\TslibFetce\\Controller\\TypoScriptFrontendTceController';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['configArrayPostProc'][] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/FrontendHooks.php:JambageCom\\TslibFetce\\Hooks\\FrontendHooks->getFeDataConfigArray';


// Register legacy content objects
if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('compatibility6')) {

	$GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['FORM'] = \JambageCom\TslibFetce\ContentObject\FormContentObject::class;

	// Register hooks for xhtml_cleaning and prefixLocalAnchors
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = \JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class . '->contentPostProcAll';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached'][] = \JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class . '->contentPostProcCached';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = \JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class . '->contentPostProcOutput';

}



// Only apply fallback to plain old FORM/mailform if extension "compatibility6" is not loaded
if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('compatibility6')) {
    // Add Default TypoScript for CType "mailform" after default content rendering
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'constants', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Form/constants.txt">', 'defaultContentRendering');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Form/setup.txt">', 'defaultContentRendering');

	// Add Default TypoScript for CType "search" after default content rendering
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'constants', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Search/constants.txt">', 'defaultContentRendering');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Search/setup.txt">', 'defaultContentRendering');

}

