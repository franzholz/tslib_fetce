<?php

namespace JambageCom\TslibFetce\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */


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


use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Form-data processing class.
 * Used by the FE_DATA object found in TSref. Quite old fashioned and used only by a few extensions, like good old 'tt_guest' and 'tt_board'
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage tslib_fetce
 */
class TypoScriptFrontendDataController
{
    public $extScripts = array();
    public $extScriptsConf = array();
    public $extUserFuncs = array();
    public $extUserFuncsConf = array();
    public $newData = array();
    public $extraList = 'pid';

    /**
    * @var \TYPO3\CMS\Extbase\Service\CacheService
    */
    protected $cacheService;

    /**
    * Starting the processing of user input.
    * Traverses the input data and fills in the array, $this->extScripts with references to files which are then included by includeScripts() (called AFTER start() in tslib_fe) or
    * the $this->extUserFuncs with the user functions
    * These scripts will then put the content into the database.
    *
    * @param	array		Input data coming from typ. $_POST['data'] vars
    * @param	array		TypoScript configuration for the FEDATA object, $this->config['FEData.']
    * @return	void
    * @see TYPO3 4.5 tslib_fe::fe_tce(), includeScripts()
    */
    public function start ($data, $FEData)
    {
        foreach ($data as $table => $id_arr) {
            if (
                is_array($id_arr) &&
                isset($FEData[$table . '.']) &&
                is_array($FEData[$table . '.'])
            ) {
                $sep = $FEData[$table . '.']['separator'] ? $FEData[$table . '.']['separator'] : LF;
                foreach ($id_arr as $id => $field_arr) {
                    $this->newData[$table][$id] = array();
                    if (strstr($id, 'NEW')) {   // NEW
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
                        $doublePostCheckFields = '';
                        if (
                            isset($FEData[$table . '.']['doublePostCheck.']) &&
                            isset($FEData[$table . '.']['doublePostCheck.']['fields'])
                        ) {
                            $doublePostCheckFields = $FEData[$table . '.']['doublePostCheck.']['fields'];
                        }

                        if (
                            is_array($this->newData[$table][$id]) &&
                            $dPC_field
                        ) {
                            $doublePostCheckKey =
                                $this->calcDoublePostKey(
                                    $this->newData[$table][$id],
                                    $doublePostCheckFields
                                );
                            if (
                                $this->checkDoublePostExist(
                                    $table,
                                    $dPC_field,
                                    $doublePostCheckKey
                                )
                            ) {
                                unset($this->newData[$table][$id]);	// Unsetting the whole thing, because it's not going to be saved.
                                if (
                                    defined('TYPO3_DLOG') && TYPO3_DLOG ||
                                    isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])
                                ) {
                                    GeneralUtility::devLog(
                                        '"FEData": Submitted record to table ' .  $table . ' was doublePosted (key: ' . $doublePostCheckKey . '). Nothing saved.',
                                        TSLIB_FETCE_EXT
                                    );
                                }
                            } else {
                                $this->newData[$table][$id][$dPC_field] = $doublePostCheckKey;	// Setting key value
                                $this->extraList .= ',' . $dPC_field;
                            }
                        }
                    } else {    // EDIT
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

                $processScript = $FEData[$table . '.']['processScript'];
                $processScriptConf = $FEData[$table . '.']['processScript.'];

                if ($processScript) {
                    if (substr($processScript, -4) == '.php') {

                        $incFile =
                            $GLOBALS['TSFE']->tmpl->getFileName($processScript);

                        if ($incFile) {
                            $this->extScripts[$table] = $incFile;
                            $this->extScriptsConf[$table] = $processScriptConf;
                        }
                    } else if (strpos($processScript, '->')) {
                        $this->extUserFuncs[$table] = $processScript;
                        $this->extUserFuncsConf[$table] = $processScriptConf;
                    }
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
    public function checkDoublePostExist ($table, $doublePostField, $key)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer::class));

        $result =
            $queryBuilder
                ->count('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq($doublePostField, $queryBuilder->createNamedParameter($key, \PDO::PARAM_INT))
                    )
                ->execute()
                ->fetchColumn(0);

        return $result;
    }


    /**
    * Creates the double-post hash value from the input array
    *
    * @param	array		The array with key/values to hash
    * @param    string      The fields which are used to compare for a double post
    * @return	integer		And unsigned 32bit integer hash
    * @access private
    */
    public function calcDoublePostKey (array $parameter, $doublePostCheckFields)
    {
        if ($doublePostCheckFields != '') {
            $fieldArray = GeneralUtility::trimExplode(',', $doublePostCheckFields);
            $checkArray = array();
            foreach ($fieldArray as $field) {
                if (isset($parameter[$field])) {
                    $checkArray[$field] = $parameter[$field];
                }
            }
        } else {
            $checkArray = $parameter;
        }
        ksort($checkArray);      // Sorting by key
        $result = hexdec(substr(md5(serialize($checkArray)), 0, 8));	// Making key
        return $result;
    }


    /**
    * Includes the submit scripts found in ->extScripts (filled in by the start() function)
    *
    * @return	void
    * @see tslib_fe::fe_tce(), includeScripts()
    */
    public function includeScripts ()
    {
        foreach ($this->extScripts as $incFile_table => $incFile) {
            if (@is_file($incFile)) {
                include($incFile);	// Always start the incFiles with a check of the object fe_tce.  is_object($this);
            } else {
                GeneralUtility::sysLog('"' . $incFile . '" file not found!', TSLIB_FETCE_EXT);
            }
        }
    }


    /**
    * Executes the submit user functions found in ->extUserFuncs (filled in by the start() function)
    *
    * @return   void
    * @see tslib_fe::fe_tce(), executeFunctions()
    */
    public function executeFunctions ()
    {
        // Instantiate \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer to execute the user function
        /** @var $cObj ContentObjectRenderer */
        $cObj = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
        foreach ($this->extUserFuncs as $table => $userFunc) {
            $setup = $this->extUserFuncsConf[$table];
            $parts = explode('->', $userFunc);
            $error = true;
            if (count($parts) == 2) {
                $className = '\\' . $parts[0];
                $functionName = $parts[1];

                if (method_exists($className, $functionName)) {
                    $result =
                        call_user_func($className . '::' . $functionName, $this, $setup);
                    $error = false;
                }
            }

            if ($error) {
                GeneralUtility::sysLog('"' . $userFunc . '" user function cannot be found.', TSLIB_FETCE_EXT);
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
    * @param	array		record array with key/value pairs being field/values (already escaped)
    * @return	int       uid of successfully inserted row
    */
    public function execNEWinsert ($table, $dataArray)
    {
        $result = false;

        $extraList = $this->extraList;
        if ($GLOBALS['TCA'][$table]['ctrl']['tstamp']) {
            $field = $GLOBALS['TCA'][$table]['ctrl']['tstamp'];
            $dataArray[$field] = $GLOBALS['EXEC_TIME'];
            $extraList .= ',' . $field;
        }
        if ($GLOBALS['TCA'][$table]['ctrl']['crdate']) {
            $field = $GLOBALS['TCA'][$table]['ctrl']['crdate'];
            $dataArray[$field] = $GLOBALS['EXEC_TIME'];
            $extraList .= ',' . $field;
        }
        if ($GLOBALS['TCA'][$table]['ctrl']['cruser_id']) {
            $field = $GLOBALS['TCA'][$table]['ctrl']['cruser_id'];
            $dataArray[$field] = 0;
            $extraList .= ',' . $field;
        }

        unset($dataArray['uid']);	// uid can never be set
        $insertFields = array();

        foreach($dataArray as $f => $v) {
            if (
                GeneralUtility::inList($extraList, $f) ||
                isset($GLOBALS['TCA'][$table]['columns'][$f])
            ) {
                $insertFields[$f] = $v;
            }
        }

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $databaseConnectionForTable = $connectionPool->getConnectionForTable($table);

        $count =
            $databaseConnectionForTable->insert(
                $table,
                $insertFields
            );

        if ($count) {
            $result = (int) $databaseConnectionForTable->lastInsertId($table);
        }

        return $result;
    }


    /**
    * Clear cache for page id.
    * If the page id is the current page, then set_no_cache() is called (so page caching is disabled)
    *
    * @param	integer		The page id for which to clear the cache
    * @return	void
    * @see tslib_fe::set_no_cache()
    */
    public function clear_cacheCmd ($cacheCmd)
    {
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
    public function getConf ($table)
    {
        $result = array();
        if (isset($this->extScriptsConf[$table])) {
            $result = $this->extScriptsConf[$table];
        }
        return $result;
    }
}

