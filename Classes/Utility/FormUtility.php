<?php

namespace JambageCom\TslibFetce\Utility;

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
 
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

 
/**
 * Contains some functions that were been previously found
 * inside TypoScriptFrontendController
 * but are shared between FormContentObject and TypoScriptFrontendController
 *
 */
class FormUtility
{
    /**
     * En/decodes strings with lightweight encryption and a hash containing the server encryptionKey (salt)
     * Can be used for authentication of information sent from server generated pages back to the server to establish that the server generated the page. (Like hidden fields with recipient mail addresses)
     * Encryption is mainly to avoid spam-bots to pick up information.
     *
     * @param string $string Input string to en/decode
     * @param bool $decode If set, string is decoded, not encoded.
     * @return string encoded/decoded version of $string
     */
    public static function codeString ($string, $decode = false)
    {
        if ($decode) {
            list($md5Hash, $str) = explode(':', $string, 2);
            $newHash = substr(md5($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . ':' . $str), 0, 10);
            if ($md5Hash === $newHash) {
                $str = base64_decode($str);
                $str = self::roundTripCryptString($str);
                return $str;
            } else {
                return false;
            }
        } else {
            $str = $string;
            $str = self::roundTripCryptString($str);
            $str = base64_encode($str);
            $newHash = substr(md5($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . ':' . $str), 0, 10);
            return $newHash . ':' . $str;
        }
    }

    /**
     * Encrypts a strings by XOR'ing all characters with a key derived from the
     * TYPO3 encryption key.
     *
     * Using XOR means that the string can be decrypted by simply calling the
     * function again - just like rot-13 works (but in this case for ANY byte
     * value).
     *
     * @param string $string String to crypt, may be empty
     * @return string binary crypt string, will have the same length as $string
     */
    protected static function roundTripCryptString ($string)
    {
        $out = '';
        $cleartextLength = strlen($string);
        $key = sha1($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
        $keyLength = strlen($key);
        for ($a = 0; $a < $cleartextLength; $a++) {
            $xorVal = ord($key[$a % $keyLength]);
            $out .= chr(ord($string[$a]) ^ $xorVal);
        }
        return $out;
    }

    /**
    * Creates the double-post hash value from the input array
    *
    * @param	array		The array with key/values to hash
    * @param    string      The fields which are used to compare for a double post
    * @return	integer		And unsigned 32bit integer hash
    * @access private
    */
    static public function calcDoublePostKey (array $parameter, $doublePostCheckFields)
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
    * Checking if a "double-post" exists already.
    * "Double-posting" is if someone refreshes a page with a form for the message board or guestbook and thus submits the element twice. Checking for double-posting prevents the second submission from being stored. This is done by saving the first record with a MD5 hash of the content - if this hash exists already, the record cannot be saved.
    *
    * @param	string		The database table to check
    * @param	string		The fieldname from the database table to search
    * @param	integer		The hash value to search for.
    * @return	integer		The number of found rows. If zero then no "double-post" was found and its all OK.
    * @access private
    */
    static public function checkDoublePostExist ($table, $doublePostField, $key)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer::class));

        $result =
            $queryBuilder
                ->count('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq($doublePostField, $queryBuilder->createNamedParameter($key, \PDO::PARAM_STR))
                    )
                ->execute()
                ->fetchColumn(0);

        return $result;
    }
}
