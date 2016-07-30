<?php

namespace JambageCom\TslibFetce\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
*  Copyright notice
*
*  (c) 1999-2014 Kasper Skårhøj (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Frontend hooks used by the tslib_fetce extension.
 *
 * @author	Franz Holzinger <franz@ttproducts.de>
 * $Id$
 */
class TypoScriptFrontendTceController extends \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController {

	/**
	 * Handle data submission
	 *
	 * @return void
	 */
	public function handleDataSubmission() {
		// Check Submission of data.
		// This is done at this point, because we need the config values
		switch ($this->checkDataSubmission()) {
			case 'email':
				$this->sendFormmail();
				break;
			case 'fe_tce':
				$this->fe_tce();
			break;
		}
	}

	/**
	 * Checks if any email-submissions
	 *
	 * @return string "email" if a formmail has been sent, "" if none.
	 */
	protected function checkDataSubmission() {
		$ret = '';
		$formtype_db = isset($_POST['formtype_db']) || isset($_POST['formtype_db_x']);
		$formtype_mail = isset($_POST['formtype_mail']) || isset($_POST['formtype_mail_x']);
		if ($formtype_db || $formtype_mail) {
			$refInfo = parse_url(GeneralUtility::getIndpEnv('HTTP_REFERER'));
			if (GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY') == $refInfo['host'] || $this->TYPO3_CONF_VARS['SYS']['doNotCheckReferer']) {
				if ($this->locDataCheck($_POST['locationData'])) {
					if ($formtype_mail) {
						$ret = 'email';
					} elseif ($formtype_db && is_array($_POST['data'])) {
						$ret = 'fe_tce';
					}
					$GLOBALS['TT']->setTSlogMessage('"Check Data Submission": Return value: ' . $ret, 0);
					return $ret;
				}
			} else {
				$GLOBALS['TT']->setTSlogMessage('"Check Data Submission": HTTP_HOST and REFERER HOST did not match when processing submitted formdata!', 3);
			}
		}
		// Hook for processing data submission to extensions:
		if (is_array($this->TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'])) {
			foreach ($this->TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'] as $_classRef) {
				$_procObj = GeneralUtility::getUserObj($_classRef);
				$_procObj->checkDataSubmission($this);
			}
		}
		return $ret;
	}

	/**
	 * Processes submitted user data (obsolete "Frontend TCE")
	 *
	 * @return	void
	 * @see tslib_feTCE
	 */
	function fe_tce() {
		$fe_tce = GeneralUtility::makeInstance('tslib_feTCE');
		$fe_tce->start(GeneralUtility::_POST('data'), $this->config['FEData.']);
		$fe_tce->includeScripts();
	}
}

?>