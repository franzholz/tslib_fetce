<?php

namespace JambageCom\TslibFetce\Middleware;

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
use JambageCom\TslibFetce\Controller\TypoScriptFrontendTceController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Stores the original request for an Ajax call before processing a request for the TYPO3 Frontend.
 *
 */
class FrontendTce implements MiddlewareInterface
{
    /**
     * Hook to process the submitted form data
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tsfetce = GeneralUtility::makeInstance(TypoScriptFrontendTceController::class);
        $tsfetce->setRequest($request);
        $tsfe = $this->getTypoScriptFrontendController();
        $frontendTypoScript = $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript');
        $typoScriptSetupArray = $frontendTypoScript->getSetupArray();
        $tsfe->config['FEData'] = ($typoScriptSetupArray['FEData'] ?? '');
        $tsfe->config['FEData.'] = ($typoScriptSetupArray['FEData.'] ?? '');
        $dataProcessed = $tsfetce->checkDataSubmission($tsfe);
        return $handler->handle($request);
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
