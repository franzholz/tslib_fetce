<?php

namespace JambageCom\TslibFetce\EventListener;

use Psr\Log\LoggerInterface;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

use JambageCom\Div2007\Utility\HtmlUtility;


class AfterCacheableContentIsGenerated implements SingletonInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $tsfe = $event->getController();
        
        // Fix local anchors in links, if flag set
        if (!empty($this->doLocalAnchorFix($tsfe))) {
            $this->prefixLocalAnchorsWithScript($event);
        }
        // XHTML-clean the code, if flag set
        if ($this->doXHTML_cleaning($tsfe) != 'none') {
            $XHTML_clean = GeneralUtility::makeInstance(HtmlUtility::class);
            $event->content = $XHTML_clean->XHTML_clean($event->content);
        }
    }


    /**
     * Returns the mode of Local Anchor prefixing
     *
     * @param TypoScriptFrontendController $tsfe
     * @return string Keyword: "all", "cached" or "output"
     */
    public function doLocalAnchorFix (TypoScriptFrontendController $tsfe)
    {
        return (isset($tsfe->config['config']['prefixLocalAnchors']) ? $tsfe->config['config']['prefixLocalAnchors'] : null);
    }

    /**
     * Substitutes all occurrences of <a href="#"... in $this->content with <a href="[path-to-url]#"...
     *
     * @param TypoScriptFrontendController $parentObject
     * @return void Works directly on $this->content
     */
    protected function prefixLocalAnchorsWithScript (TypoScriptFrontendController $tsfe)
    {
        if (!$tsfe->getContext()->getPropertyFromAspect('backend.user', 'isLoggedIn', false)) {
            if (!is_object($tsfe->cObj)) {
                $tsfe->newCObj();
            }
            $scriptPath = $tsfe->cObj->getUrlToCurrentLocation();
        } else {
            // To break less existing sites, we allow the REQUEST_URI to be used for the prefix
            $scriptPath = GeneralUtility::getIndpEnv('REQUEST_URI');
            // Disable the cache so that these URI will not be the ones to be cached
            $tsfe->no_cache = true;
        }
        $originalContent = $tsfe->content;
        $tsfe->content = preg_replace('/(<(?:a|area).*?href=")(#[^"]*")/i', '${1}' . htmlspecialchars($scriptPath) . '${2}', $originalContent);
        // There was an error in the call to preg_replace, so keep the original content (behavior prior to PHP 5.2)
        if (preg_last_error() > 0) {
            $this->logger->error('preg_replace returned error-code: ' . preg_last_error() . ' in function prefixLocalAnchorsWithScript. Replacement not done!');
            $tsfe->content = $originalContent;
        }
    }

    /**
     * Returns the mode of XHTML cleaning
     *
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe
     * @return string Keyword: "all", "cached", "none" or "output"
     */
    protected function doXHTML_cleaning (TypoScriptFrontendController $tsfe)
    {
        if (
            isset($tsfe->config['config']['xmlprologue']) &&
            $tsfe->config['config']['xmlprologue'] == 'none'
        ) {
            return 'none';
        }
        return $tsfe->config['config']['xhtml_cleaning'] ?? 'none';
    }
}

