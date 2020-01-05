<?php
defined('TYPO3_MODE') || die('Access denied.');
defined('TYPO3_version') || die('The constant TYPO3_version is undefined in tslib_fetce!');

call_user_func(function () {
    define('TSLIB_FETCE_EXT', 'tslib_fetce');

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][] = \JambageCom\TslibFetce\Controller\TypoScriptFrontendTceController::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['configArrayPostProc'][] = \JambageCom\TslibFetce\Hooks\FrontendHooks::class . '->getFeDataConfigArray';

    // Register legacy content objects
    if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('compatibility6')) {

        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['FORM'] = \JambageCom\TslibFetce\ContentObject\FormContentObject::class;

        // Register hooks for xhtml_cleaning and prefixLocalAnchors
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = \JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class . '->contentPostProcAll';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached'][] = \JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class . '->contentPostProcCached';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = \JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class . '->contentPostProcOutput';

        // Only apply fallback to plain old FORM/mailform if extension "compatibility6" is not loaded
        // Add Default TypoScript for CType "mailform" after default content rendering
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'constants', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Form/constants.txt">', 'defaultContentRendering');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Form/setup.txt">', 'defaultContentRendering');

        // Add Default TypoScript for CType "search" after default content rendering
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'constants', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Search/constants.txt">', 'defaultContentRendering');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Search/setup.txt">', 'defaultContentRendering');
    }
});


