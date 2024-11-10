<?php

namespace JambageCom\TslibFetce\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;


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

/**
 * Frontend hooks used by the tslib_fetce extension.
 *
 * Only TYPO3 12
 *
 * @author	Franz Holzinger <franz@ttproducts.de>
 */
class FrontendHooks
{
    /**
    * Checks if config-array exists already but if not, gets it
    *
    * @param array $parameters
    * @param TypoScriptFrontendController $tsfe
    * @return void
    * @todo Define visibility
    */
    public function getFeDataConfigArray(&$params, TypoScriptFrontendController $tsfe): void
    {
        debug ('B');
        debug ($params, 'getFeDataConfigArray ANFANG $params');

        $frontendTypoScript = $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript');
        debug ($frontendTypoScript, 'getFeDataConfigArray $frontendTypoScript empty +++ HIER');
        // $typoScriptSetupArray = $frontendTypoScript->getSetupArray();;
        // debug ($typoScriptSetupArray, 'getFeDataConfigArray ANFANG $typoScriptSetupArray');

        $tsfe->config['FEData']  ??= ''; // $typoScriptSetupArray['FEData']
        $tsfe->config['FEData.'] ??= '';
        debug ($tsfe->config, 'getFeDataConfigArray ENDE $tsfe->config');
        debug ('E');
    }
}
