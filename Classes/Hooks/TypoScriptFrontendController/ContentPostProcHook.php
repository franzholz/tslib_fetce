<?php

namespace JambageCom\TslibFetce\Hooks\TypoScriptFrontendController;

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

use Psr\Log\LoggerInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;

use JambageCom\Div2007\Utility\HtmlUtility;

/**
 * Deprecated. Only used until TYPO3 11.5
 *
 * Class that hooks into TypoScriptFrontendController to do XHTML cleaning and prefixLocalAnchors functionality
 */
class ContentPostProcHook
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * XHTML-clean the code, if flag config.xhtml_cleaning is set
     * to "all", same goes for config.prefixLocalAnchors
     *
     * @param array $parameters
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parentObject
     */
    public function contentPostProcAll(&$parameters, $parentObject): void
    {
        // Fix local anchors in links, if flag set
        if ($this->doLocalAnchorFix($parentObject) == 'all') {
            $this->prefixLocalAnchorsWithScript($parentObject);
        }
        // XHTML-clean the code, if flag set
        if ($this->doXHTML_cleaning($parentObject) == 'all') {
            $XHTML_clean = GeneralUtility::makeInstance(HtmlUtility::class);
            $parentObject->content = $XHTML_clean->XHTML_clean($parentObject->content);
        }
    }


    /**
     * XHTML-clean the code, if flag config.xhtml_cleaning is set
     * to "cached", same goes for config.prefixLocalAnchors
     *
     * @param array $parameters
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parentObject
     */
    public function contentPostProcCached(&$parameters, $parentObject): void
    {
        // Fix local anchors in links, if flag set
        if ($this->doLocalAnchorFix($parentObject) == 'cached') {
            $this->prefixLocalAnchorsWithScript($parentObject);
        }
        // XHTML-clean the code, if flag set
        if ($this->doXHTML_cleaning($parentObject) == 'cached') {
            $XHTML_clean = GeneralUtility::makeInstance(HtmlUtility::class);
            $parentObject->content = $XHTML_clean->XHTML_clean($parentObject->content);
        }
    }


    /**
     * XHTML-clean the code, if flag config.xhtml_cleaning is set
     * to "output", same goes for config.prefixLocalAnchors
     *
     * @param array $parameters
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parentObject
     */
    public function contentPostProcOutput(&$parameters, $parentObject): void
    {
        // Fix local anchors in links, if flag set
        if ($this->doLocalAnchorFix($parentObject) == 'output') {
            $this->prefixLocalAnchorsWithScript($parentObject);
        }
        // XHTML-clean the code, if flag set
        if ($this->doXHTML_cleaning($parentObject) == 'output') {
            $XHTML_clean = GeneralUtility::makeInstance(HtmlUtility::class);
            $parentObject->content = $XHTML_clean->XHTML_clean($parentObject->content);
        }
    }


    /**
     * Returns the mode of XHTML cleaning
     *
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parentObject
     * @return string Keyword: "all", "cached", "none" or "output"
     */
    protected function doXHTML_cleaning($parentObject)
    {
        if (
            isset($parentObject->config['config']['xmlprologue']) &&
            $parentObject->config['config']['xmlprologue'] == 'none'
        ) {
            return 'none';
        }
        return $parentObject->config['config']['xhtml_cleaning'] ?? 'none';
    }


    /**
     * Returns the mode of Local Anchor prefixing
     *
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parentObject
     * @return string Keyword: "all", "cached" or "output"
     */
    public function doLocalAnchorFix($parentObject)
    {
        return $parentObject->config['config']['prefixLocalAnchors'] ?? null;
    }


    /**
     * Substitutes all occurrences of <a href="#"... in $this->content with <a href="[path-to-url]#"...
     *
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parentObject
     * @return void Works directly on $this->content
     */
    protected function prefixLocalAnchorsWithScript($parentObject)
    {
        if (!$parentObject->getContext()->getPropertyFromAspect('backend.user', 'isLoggedIn', false)) {
            if (!is_object($parentObject->cObj)) {
                $parentObject->newCObj();
            }
            $scriptPath = $parentObject->cObj->getUrlToCurrentLocation();
        } else {
            // To break less existing sites, we allow the REQUEST_URI to be used for the prefix
            $scriptPath = GeneralUtility::getIndpEnv('REQUEST_URI');
            // Disable the cache so that these URI will not be the ones to be cached
            $parentObject->no_cache = true;
        }
        $originalContent = $parentObject->content;
        $parentObject->content = preg_replace('/(<(?:a|area).*?href=")(#[^"]*")/i', '${1}' . htmlspecialchars($scriptPath) . '${2}', $originalContent);
        // There was an error in the call to preg_replace, so keep the original content (behavior prior to PHP 5.2)
        if (preg_last_error() > 0) {
            $this->logger->error(
                'preg_replace returned error-code: ' . preg_last_error() . ' in function prefixLocalAnchorsWithScript. Replacement not done!'
            );
            $parentObject->content = $originalContent;
        }
    }
}
