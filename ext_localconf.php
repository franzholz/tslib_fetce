<?php
defined('TYPO3_MODE') || die('Access denied.');
defined('TYPO3_version') || die('The constant TYPO3_version is undefined in tslib_fetce!');

call_user_func(function () {
    if (!defined ('TSLIB_FETCE_EXT')) {
        define('TSLIB_FETCE_EXT', 'tslib_fetce');
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][] = \JambageCom\TslibFetce\Controller\TypoScriptFrontendTceController::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['configArrayPostProc'][] = (\JambageCom\TslibFetce\Hooks\FrontendHooks::class) . '->getFeDataConfigArray';

    // Register legacy content objects

    $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['FORM'] = \JambageCom\TslibFetce\ContentObject\FormContentObject::class;

    // Register hooks for xhtml_cleaning and prefixLocalAnchors
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = (\JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class) . '->contentPostProcAll';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached'][] = (\JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class) . '->contentPostProcCached';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = (\JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class) . '->contentPostProcOutput';
});

