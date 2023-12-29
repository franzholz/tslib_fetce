<?php

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('compatibility6')) {
    return;
}

namespace {
    die('Access denied');
}

namespace {
    /**
     * @deprecated since 6.0, removed since 7.0
     */
    class tslib_content_Form extends \JambageCom\TslibFetce\ContentObject\FormContentObject
    {
    }
}

namespace TYPO3\CMS\Frontend\ContentObject {
    /**
     * @deprecated since 6.0, removed since 7.0
     */
    class FormContentObject extends \JambageCom\TslibFetce\ContentObject\FormContentObject
    {
    }
}
