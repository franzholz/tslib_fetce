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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Class for the built TypoScript based Front End
 *
 * This class has a lot of functions and internal variable which are use from index_ts.php.
 * The class is instantiated as $GLOBALS['TSFE'] in index_ts.php.
 * The use of this class should be inspired by the order of function calls as found in index_ts.php.
 *
 * Revised for TYPO3 3.6 June/2003 by Kasper Skårhøj
 * XHTML compliant
 *
 * @author Kasper Skårhøj <kasperYYYY@typo3.com>
 */


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;



/**
 * Form-data processing class.
 * Used by the FE_DATA object found in TSref. Quite old fashioned and used only by a few extensions, like good old 'tt_guest' and 'tt_board'
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tslib_fetce
 */
class TypoScriptFrontendDataController {

    public $extScripts = array();
    public $extScriptsConf = array();
    public $newData = array();
    public $extraList = 'pid';

    /**
    * @var \TYPO3\CMS\Extbase\Service\CacheService
    */
    protected $cacheService;

    /**
    * Starting the processing of user input.
    * Traverses the input data and fills in the array, $this->extScripts with references to files which are then included by includeScripts() (called AFTER start() in tslib_fe)
    * These scripts will then put the content into the database.
    *
    * @param	array		Input data coming from typ. $_POST['data'] vars
    * @param	array		TypoScript configuration for the FEDATA object, $this->config['FEData.']
    * @return	void
    * @see TYPO3 4.5 tslib_fe::fe_tce(), includeScripts()
    */
    public function start ($data, $FEData) {
        foreach ($data as $table => $id_arr) {
            if (
                is_array($id_arr) &&
                isset($FEData[$table . '.']) &&
                is_array($FEData[$table . '.'])
            ) {
                $sep = $FEData[$table . '.']['separator'] ? $FEData[$table . '.']['separator'] : LF;
                foreach ($id_arr as $id => $field_arr) {
                    $this->newData[$table][$id] = array();
                    if (strstr($id, 'NEW')) {		// NEW
                            // Defaults:
                        if ($FEData[$table . '.']['default.']) {
                            $this->newData[$table][$id] = $FEData[$table . '.']['default.'];
                        }
                        if ($FEData[$table . '.']['autoInsertPID']) {
                            $this->newData[$table][$id]['pid'] = intval($GLOBALS['TSFE']->page['uid']);
                        }
                            // Insert external data:
                        if (is_array($field_arr)) {
                            foreach ($field_arr as $field => $value) {
                                if ($FEData[$table . '.']['allowNew.'][$field]) {
                                    if (is_array($value)) {
                                        $this->newData[$table][$id][$field] = implode($sep,$value);
                                    } else {
                                        $this->newData[$table][$id][$field] = $value;
                                    }
                                }
                            }
                        }
                            // Double post check
                        $dPC_field = $FEData[$table . '.']['doublePostCheck'];
                        if (is_array($this->newData[$table][$id]) && $dPC_field) {
                            $doublePostCheckKey = $this->calcDoublePostKey($this->newData[$table][$id]);
                            if ($this->checkDoublePostExist($table, $dPC_field, $doublePostCheckKey)) {
                                unset($this->newData[$table][$id]);	// Unsetting the whole thing, because it's not going to be saved.
                                $GLOBALS['TT']->setTSlogMessage('"FEData": Submitted record to table $table was doublePosted (key: $doublePostCheckKey). Nothing saved.', 2);
                            } else {
                                $this->newData[$table][$id][$dPC_field] = $doublePostCheckKey;	// Setting key value
                                $this->extraList .= ',' . $dPC_field;
                            }
                        }
                    } else {		// EDIT
                            // Insert external data:
                        if (is_array($field_arr)) {
                            foreach ($field_arr as $field => $value) {
                                if ($FEData[$table . '.']['allowEdit.'][$field]) {
                                    if (is_array($value)) {
                                        $this->newData[$table][$id][$field] = implode($sep, $value);
                                    } else {
                                        $this->newData[$table][$id][$field] = $value;
                                    }
                                }
                            }
                        }
                            // Internal Override
                        if (is_array($FEData[$table . '.']['overrideEdit.'])) {
                            foreach ($FEData[$table . '.']['overrideEdit.'] as $field => $value) {
                                $this->newData[$table][$id][$field] = $value;
                            }
                        }
                    }

                    if ($FEData[$table . '.']['userIdColumn']) {
                        $this->newData[$table][$id][$FEData[$table . '.']['userIdColumn']] = intval($GLOBALS['TSFE']->fe_user->user['uid']);
                    }
                }
                $incFile = $GLOBALS['TSFE']->tmpl->getFileName($FEData[$table . '.']['processScript']);

                if ($incFile) {
                    $this->extScripts[$table] = $incFile;
                    $this->extScriptsConf[$table] = $FEData[$table.'.']['processScript.'];
                }
            }
        }
    }


    /**
    * Checking if a "double-post" exists already.
    * "Double-posting" is if someone refreshes a page with a form for the message board or guestbook and thus submits the element twice. Checking for double-posting prevents the second submission from being stored. This is done by saving the first record with a MD5 hash of the content - if this hash exists already, the record cannot be saved.
    *
    * @param	string		The database table to check
    * @param	string		The fieldname from the database table to search
    * @param	integer		The hash value to search for.
    * @return	integer		The number of found rows. If zero then no "double-post" was found and its all OK.
    * @access private
    */
    public function checkDoublePostExist ($table, $doublePostField, $key) {
        $result = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
            '*',
            $table,
            $doublePostField . '=' . intval($key)
        );
        return $result;
    }


    /**
    * Creates the double-post hash value from the input array
    *
    * @param	array		The array with key/values to hash
    * @return	integer		And unsigned 32bit integer hash
    * @access private
    */
    public function calcDoublePostKey ($array) {
        ksort($array);	// Sorting by key
        $doublePostCheckKey = hexdec(substr(md5(serialize($array)), 0, 8));	// Making key
        return $doublePostCheckKey;
    }


    /**
    * Includes the submit scripts found in ->extScripts (filled in by the start() function)
    *
    * @return	void
    * @see tslib_fe::fe_tce(), includeScripts()
    */
    public function includeScripts () {
        foreach ($this->extScripts as $incFile_table => $incFile) {
            if (@is_file($incFile)) {
                include($incFile);	// Always start the incFiles with a check of the object fe_tce.  is_object($this);
            } else {
                if (TYPO3_DLOG) {
                    GeneralUtility::devLog('"' . $incFile . '" file not found!', TSLIB_FETCE_EXT);
                }
            }
        }
    }


    /**
    * Method available to the submit scripts for creating insert queries.
    * Automatically adds tstamp, crdate, cruser_id field/value pairs.
    * Will allow only field names which are either found in $GLOBALS['TCA'][...][columns] OR in the $this->extraList
    * Executes an insert query!
    *
    * @param	string		The table name for which to create the insert statement
    * @param	array		array with key/value pairs being field/values (already escaped)
    * @return	void
    */
    public function execNEWinsert ($table, $dataArr) {
        $extraList = $this->extraList;
        if ($GLOBALS['TCA'][$table]['ctrl']['tstamp']) {
            $field = $GLOBALS['TCA'][$table]['ctrl']['tstamp'];
            $dataArr[$field] = $GLOBALS['EXEC_TIME'];
            $extraList .= ',' . $field;
        }
        if ($GLOBALS['TCA'][$table]['ctrl']['crdate']) {
            $field = $GLOBALS['TCA'][$table]['ctrl']['crdate'];
            $dataArr[$field] = $GLOBALS['EXEC_TIME'];
            $extraList .= ',' . $field;
        }
        if ($GLOBALS['TCA'][$table]['ctrl']['cruser_id']) {
            $field = $GLOBALS['TCA'][$table]['ctrl']['cruser_id'];
            $dataArr[$field] = 0;
            $extraList .= ',' . $field;
        }

        unset($dataArr['uid']);	// uid can never be set
        $insertFields = array();

        foreach($dataArr as $f => $v) {
            if (
                GeneralUtility::inList($extraList, $f) ||
                isset($GLOBALS['TCA'][$table]['columns'][$f])
            ) {
                $insertFields[$f] = $v;
            }
        }

        $GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $insertFields);
    }


    /**
    * Clear cache for page id.
    * If the page id is the current page, then set_no_cache() is called (so page caching is disabled)
    *
    * @param	integer		The page id for which to clear the cache
    * @return	void
    * @see tslib_fe::set_no_cache()
    */
    public function clear_cacheCmd ($cacheCmd) {
        $cacheCmd = intval($cacheCmd);

        if ($cacheCmd) {
            $this->cacheService = new \TYPO3\CMS\Extbase\Service\CacheService();
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            $this->cacheService->injectCacheManager($cacheManager);
            $this->cacheService->clearPageCache($cacheCmd);
        }
    }


    /**
    * Return TypoScript configuration for a table name
    *
    * @param	string		The table name for which to return TypoScript configuration (From TS: FEData.[table])
    * @return	array		TypoScript properties from FEData.[table] - if exists.
    *               		empty if nothing has been defined
    */
    public function getConf ($table) {
        $result = array();
        if (isset($this->extScriptsConf[$table])) {
            $result = $this->extScriptsConf[$table];
        }
        return $result;
    }
}

