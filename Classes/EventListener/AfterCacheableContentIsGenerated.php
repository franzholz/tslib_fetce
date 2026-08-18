<?php
declare(strict_types=1);

namespace JambageCom\TslibFetce\EventListener;

use Psr\Log\LoggerInterface;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

use JambageCom\Div2007\Utility\HtmlUtility;

class AfterCacheableContentIsGenerated implements SingletonInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $config = [];
        $tsfe = null;
        if (method_exists($event, 'getController')) {
            $tsfe = $event->getController();
            $config = $tsfe->config['config'] ?? [];
        } else {
            $request = $event->getRequest();
            $frontendTypoScript = $request->getAttribute('frontend.typoscript');
            $typoScriptSetupArray = $frontendTypoScript->getSetupArray();
            $config = $typoScriptSetupArray['config'] ?? [];
        }

        // Fix local anchors in links, if flag set
        if (!empty($this->doLocalAnchorFix($config))) {
            $this->prefixLocalAnchorsWithScript($tsfe);
        }
        // XHTML-clean the code, if flag set
        if ($this->doXHTML_cleaning($config) != 'none') {
            $XHTML_clean = GeneralUtility::makeInstance(HtmlUtility::class);
            if (is_object($tsfe)) {
                $tsfe->content = $XHTML_clean->XHTML_clean($tsfe->content);
            } else {
                $content = $event->getContent();
                $content = $XHTML_clean->XHTML_clean($content);
                $event->setContent($content);
            }
        }
    }

    /**
     * Returns the mode of Local Anchor prefixing
     *
     * @param TypoScriptFrontendController $tsfe
     * @return string Keyword: "all", "cached" or "output"
     */
    public function doLocalAnchorFix(array $config)
    {
        return ($config['prefixLocalAnchors'] ?? null);
    }

    /**
     * Substitutes all occurrences of <a href="#"... in $this->content with <a href="[path-to-url]#"...
     *
     * @param TypoScriptFrontendController $parentObject
     * @return void Works directly on $this->content
     */
    protected function prefixLocalAnchorsWithScript(TypoScriptFrontendController $tsfe)
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
     * @param array $config
     * @return string Keyword: "all", "cached", "none" or "output"
     */
    protected function doXHTML_cleaning(array $config)
    {
        $result = 'none';
        if (
            !isset($config['xmlprologue']) ||
            $config['xmlprologue'] != 'none'
        ) {
            $result = $config['xhtml_cleaning'] ?? 'none';
        }
        return $result;
    }
}
