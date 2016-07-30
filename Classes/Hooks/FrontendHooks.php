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
class FrontendHooks {


	/**
	 * Checks if config-array exists already but if not, gets it
	 *
	 * @return void
	 * @todo Define visibility
	 */
	public function getFeDataConfigArray(&$params, $pObj) {

		$pObj->config['FEData'] = $pObj->tmpl->setup['FEData'];
		$pObj->config['FEData.'] = $pObj->tmpl->setup['FEData.'];
	}
}

?>