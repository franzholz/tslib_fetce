<?php

defined('TYPO3') || die('Access denied.');

call_user_func(function ($extensionKey): void {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'constants',
        '@import \'EXT:tslib_fetce/Configuration/TypoScript/Form/constants.typoscript\'',
        'defaultContentRendering'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'setup',
        '@import \'EXT:' . $extensionKey . '/Configuration/TypoScript/Form/setup.typoscript\'',
        'defaultContentRendering'
    );

    // Add Default TypoScript for CType "search" after default content rendering

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'constants',
        '@import \'EXT:' . $extensionKey . '/Configuration/TypoScript/Search/constants.typoscript\'',
        'defaultContentRendering'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'setup',
        '@import \'EXT:' . $extensionKey . '/Configuration/TypoScript/Search/setup.typoscript\'',
        'defaultContentRendering'
    );
}, 'tslib_fetce');
