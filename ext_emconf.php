<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Frontend TCE',
    'description' => 'It brings the removed TYPO3 4.x class tslib_feTCE and the processScript FEData setup with new features partly back into TYPO3 10 and later. This is needed by extensions based on the former TYPO3 FORM like tt_board and tt_guest.',
    'category' => 'plugin',
    'version' => '0.9.6',
    'state' => 'stable',
    'author' => 'Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => 'jambage.com',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'div2007' => '2.2.0-0.0.0',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
