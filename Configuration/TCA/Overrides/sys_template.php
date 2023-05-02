<?php
defined('TYPO3') || die('Access denied.');

call_user_func(function ()
{
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'tslib_fetce', 
        'constants',
        '@import \'EXT:tslib_fetce/Configuration/TypoScript/Form/constants.txt\'',
        'defaultContentRendering'
    );
    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'tslib_fetce', 
        'setup',
        '@import \'EXT:' . 'tslib_fetce' . '/Configuration/TypoScript/Form/setup.txt\'', 'defaultContentRendering');

    // Add Default TypoScript for CType "search" after default content rendering
    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'tslib_fetce',
        'constants', 
        '@import \'EXT:' . 'tslib_fetce' . '/Configuration/TypoScript/Search/constants.txt\'',
        'defaultContentRendering'
    );
    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'tslib_fetce',
        'setup',
        '@import \'EXT:' . 'tslib_fetce' . '/Configuration/TypoScript/Search/setup.txt\'',
        'defaultContentRendering'
    );
});

