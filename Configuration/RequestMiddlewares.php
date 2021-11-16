<?php

return [
    'frontend' => [
        'jambagecom/tslib-fetce/preprocessing' => [
            'target' => \JambageCom\TslibFetce\Middleware\FrontendTce::class,
            'description' => 'Frontend TCE. Backwards compatibility to deprecated TYPO3 4.x class tslib_feTCE.',
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering'
            ]
        ]
    ]
];

