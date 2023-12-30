<?php

defined('TYPO3') || die('Access denied.');

call_user_func(function ($extensionKey): void {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][] = \JambageCom\TslibFetce\Controller\TypoScriptFrontendTceController::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['configArrayPostProc'][] = (\JambageCom\TslibFetce\Hooks\FrontendHooks::class) . '->getFeDataConfigArray';

    // Register legacy content objects
    // only TYPO3 11.5:
    $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['FORM'] = \JambageCom\TslibFetce\ContentObject\FormContentObject::class;

    // Register hooks for xhtml_cleaning and prefixLocalAnchors
    // only TYPO3 11.5:
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = (\JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class) . '->contentPostProcAll';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached'][] = (\JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class) . '->contentPostProcCached';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = (\JambageCom\TslibFetce\Hooks\TypoScriptFrontendController\ContentPostProcHook::class) . '->contentPostProcOutput';

    // Add configuration for the logging API
    $GLOBALS['TYPO3_CONF_VARS']['LOG']['JambageCom']['TslibFetce']['EventListener']['AfterCacheableContentIsGenerated']['writerConfiguration'] = [
        // configuration for ERROR level log entries
        \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
            // add a FileWriter
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                // configuration for the writer
                'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/tslib_fetce.log'
            ]
        ]
    ];

    $GLOBALS['TYPO3_CONF_VARS']['LOG']['JambageCom']['TslibFetce']['Controller']['TypoScriptFrontendDataController']['writerConfiguration'] = [
        // configuration for ERROR level log entries
        \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
            // add a FileWriter
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                // configuration for the writer
                'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/tslib_fetce.log'
            ]
        ]
    ];

    $GLOBALS['TYPO3_CONF_VARS']['LOG']['JambageCom']['TslibFetce']['Hooks\\TypoScriptFrontendController']['ContentPostProcHook']['writerConfiguration'] = [
        // configuration for ERROR level log entries
        \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
            // add a FileWriter
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                // configuration for the writer
                'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/tslib_fetce.log'
            ]
        ]
    ];
}, 'tslib_fetce');
