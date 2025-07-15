<?php

namespace JambageCom\TslibFetce\Controller;

/***************************************************************
*  Copyright notice
*
*  (c) 1999-2021 Kasper Skårhøj (kasperYYYY@typo3.com)
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
use Psr\Http\Message\ServerRequestInterface;

use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use JambageCom\TslibFetce\Controller\TypoScriptFrontendDataController;

/**
 * Frontend hooks used by the tslib_fetce extension.
 *
 * @author	Franz Holzinger <franz@ttproducts.de>
 *
 */
class TypoScriptFrontendTceController
{
    /**
    * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
    */
    protected $frontendController = null;

    /**
     * Always set via setRequest() after instantiation
     */
    protected ?ServerRequestInterface $request = null;

    /**
    * hook to be executed by TypoScriptFrontendController
    *
    * @param	$frontendController: The current \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
    * @return string "fe_tce" if TCE FE data have been processed "" if none.
    */
    public function checkDataSubmission($frontendController)
    {
        $this->frontendController = $frontendController;
        $result = '';
        // Checks if any FORM submissions
        $formtype_db = isset($_POST['formtype_db']) || isset($_POST['formtype_db_x']);

        if ($formtype_db) {
            $refInfo = parse_url(GeneralUtility::getIndpEnv('HTTP_REFERER'));

            if (
                GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY') == $refInfo['host'] ||
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['doNotCheckReferer']
            ) {
                if (
                    empty($_POST['locationData']) ||
                    $this->locDataCheck($_POST['locationData'])
                ) {
                    if ($formtype_db && is_array($_POST['data'])) {
                        $this->fe_tce();
                        if (
                            defined('TYPO3_DLOG') && TYPO3_DLOG ||
                            isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])
                        ) {
                            GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__)->log(LogLevel::INFO, '"Check Data Submission": Return value: fe_tce', '');
                        }
                        $result = 'fe_tce';
                    }
                }
            } else {
                debug('tslib_fetce', '"Check Data Submission": HTTP_HOST and REFERER HOST do not match when processing submitted formdata!'); // keep this
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
    public function locDataCheck($locationData)
    {
        $locData = explode(':', $locationData);
        if (
            !$locData['1'] ||
            $this->frontendController->sys_page->checkRecord($locData['1'], $locData['2'], 1)
        ) {
            // $locData[1] -check means that a record is checked only if the locationData has a value for a record else than the page.
            if (count($this->frontendController->sys_page->getPage($locData['0']))) {
                return 1;
            } else {
                if (
                    defined('TYPO3_DLOG') && TYPO3_DLOG ||
                    isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])
                ) {
                    GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__)->log(LogLevel::INFO, 'LocationData Error: The page pointed to by location data (' . $locationData . ') is not accessible.', '');
                }
            }
        } else {
            if (
                defined('TYPO3_DLOG') && TYPO3_DLOG ||
                isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])
            ) {
                GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__)->log(LogLevel::INFO, 'LocationData Error: Location data (' . $locationData . ') record pointed to is not accessible.', '');
            }
        }
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
    * Processes submitted user data (revival of since TYPO3 6.2 obsolete "Frontend TCE")
    *
    * @return	void
    * @see tslib_feTCE
    */
    protected function fe_tce()
    {
        $fe_tce = GeneralUtility::makeInstance(TypoScriptFrontendDataController::class);
        $fe_tce->setRequest($this->request);
        $fe_tce->start(
            $this->getRequest()->getParsedBody()['data'],
            $this->frontendController->config['FEData.'] ?? []
        );
        $fe_tce->includeScripts();

        $fe_tce->executeFunctions();
        $request = $fe_tce->getRequest();
        if ($request instanceof ServerRequestInterface) {
            $this->setRequest($request);
        }
    }
}
