<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id$
 */

$extensionPath = t3lib_extMgm::extPath('tslib_fetce');
return array(
	'TypoScriptFrontendTceController'     => $extensionPath . 'Classes/Controller/TypoScriptFrontendTceController.php',
	'FrontendHooks'     => $extensionPath . 'Classes/Hooks/FrontendHooks.php',
	'TypoScriptFrontendTceController'     => $extensionPath . 'Classes/Hooks/TypoScriptFrontendTceController.php',
);

?>