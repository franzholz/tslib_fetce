<?php
defined('TYPO3_MODE') || die('Access denied.');


call_user_func(function () {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        TSLIB_FETCE_EXT, 
        'constants',
        '@import \'EXT:tslib_fetce/Configuration/TypoScript/Form/constants.txt\'',
        'defaultContentRendering'
    );

//     \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Form/setup.txt">', 'defaultContentRendering');

    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        TSLIB_FETCE_EXT, 
        'setup',
        '@import \'EXT:' . TSLIB_FETCE_EXT . '/Configuration/TypoScript/Form/setup.txt\'', 'defaultContentRendering');

    // Add Default TypoScript for CType "search" after default content rendering
//     \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'constants', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Search/constants.txt">', 'defaultContentRendering');
    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        TSLIB_FETCE_EXT,
        'constants', 
        '@import \'EXT:' . TSLIB_FETCE_EXT . '/Configuration/TypoScript/Search/constants.txt\'',
        'defaultContentRendering'
    );
    
/*    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('tslib_fetce', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:tslib_fetce/Configuration/TypoScript/Search/setup.txt">', 'defaultContentRendering');*/
    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        TSLIB_FETCE_EXT,
        'setup',
        '@import \'EXT:' . TSLIB_FETCE_EXT . '/Configuration/TypoScript/Search/setup.txt\'',
        'defaultContentRendering'
    );
});

