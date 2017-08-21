<?php

namespace JambageCom\TslibFetce\Controller;


/***************************************************************
*  Copyright notice
*
*  (c) 1999-2017 Kasper Skårhøj (kasperYYYY@typo3.com)
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

use TYPO3\CMS\Core\Utility\GeneralUtility;


use JambageCom\TslibFetce\Controller\TypoScriptFrontendDataController;


/**
 * Frontend hooks used by the tslib_fetce extension.
 *
 * @author	Franz Holzinger <franz@ttproducts.de>
 *
 */
class TypoScriptFrontendTceController {

    /**
    * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
    */
    protected $frontendController = null;


    /**
    * hook to be executed by TypoScriptFrontendController
    *
    * @param	$frontendController: The current \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
    * @return string "fe_tce" if TCE FE data have been processed "" if none.
    */
    public function checkDataSubmission ($frontendController) {

        $this->frontendController = $frontendController;

        $ret = '';
        // Checks if any FORM submissions
        $formtype_db = isset($_POST['formtype_db']) || isset($_POST['formtype_db_x']);

        if ($formtype_db) {
            $refInfo = parse_url(GeneralUtility::getIndpEnv('HTTP_REFERER'));

            if (
                GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY') == $refInfo['host'] ||
                $frontendController->TYPO3_CONF_VARS['SYS']['doNotCheckReferer']
            ) {
                if ($this->locDataCheck($_POST['locationData'])) {
                    if ($formtype_db && is_array($_POST['data'])) {
                        $this->fe_tce();
                        if (TYPO3_DLOG) {
                            GeneralUtility::devLog('"Check Data Submission": Return value: fe_tce', TSLIB_FETCE_EXT);
                        }
                        $result = 'fe_tce';
                    }
                }
            } else {
                if (TYPO3_DLOG) {
                    GeneralUtility::devLog('"Check Data Submission": HTTP_HOST and REFERER HOST do not match when processing submitted formdata!', TSLIB_FETCE_EXT);
                }
            }
        }

        return $result;
    }


    /**
    * Checks if a formmail submission can be sent as email or if the FE TCE can be processed
    *
    * @param string $locationData The input from $_POST['locationData']
    * @return void
    * @access private
    * @see checkDataSubmission()
    * @todo Define visibility
    */
    public function locDataCheck ($locationData) {
        $locData = explode(':', $locationData);
        if (
            !$locData[1] ||
            $frontendController->sys_page->checkRecord($locData[1], $locData[2], 1)
        ) {
            // $locData[1] -check means that a record is checked only if the locationData has a value for a record else than the page.
            if (count($this->frontendController->sys_page->getPage($locData[0]))) {
                return 1;
            } else {
                if (TYPO3_DLOG) {
                    GeneralUtility::devLog('LocationData Error: The page pointed to by location data (' . $locationData . ') is not accessible.', TSLIB_FETCE_EXT);
                }
            }
        } else {
            if (TYPO3_DLOG) {
                GeneralUtility::devLog('LocationData Error: Location data (' . $locationData . ') record pointed to is not accessible.', TSLIB_FETCE_EXT);
            }
        }
    }


    /**
    * Processes submitted user data (revival of since TYPO3 6.2 obsolete "Frontend TCE")
    *
    * @return	void
    * @see tslib_feTCE
    */
    protected function fe_tce () {
        $fe_tce = GeneralUtility::makeInstance(TypoScriptFrontendDataController::class);
        $fe_tce->start(
            GeneralUtility::_POST('data'),
            $this->frontendController->config['FEData.']
        );
        $fe_tce->includeScripts();
        $fe_tce->executeFunctions();
    }
}

