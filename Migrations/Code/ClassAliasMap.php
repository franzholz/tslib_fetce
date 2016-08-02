<?php


if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('compatibility6')) {
	return array(
        'tslib_feTCE' => 'JambageCom\\TslibFetce\\Controller\\TypoScriptFrontendDataController',
	);
} else return array(
        'tslib_feTCE' => 'JambageCom\\TslibFetce\\Controller\\TypoScriptFrontendDataController',
		'tslib_content_Form' => \JambageCom\TslibFetce\ContentObject\ContentObject\FormContentObject::class,
		'TYPO3\\CMS\\Frontend\\ContentObject\\FormContentObject' => \JambageCom\TslibFetce\ContentObject\FormContentObject::class,
);

