<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Frontend TCE',
    'description' => 'It brings the deprecated TYPO3 4.x class tslib_feTCE and the processScript FEData setup with new features partly back into TYPO3 8.x. This is needed by tt_board and tt_guest. Form MAIL and form wizards require the extension compatibility6.',
    'category' => 'plugin',
    'shy' => 0,
    'version' => '0.3.1',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearcacheonload' => 1,
    'lockType' => '',
    'author' => 'Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => 'jambage.com',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'constraints' => array(
        'depends' => array(
            'php' => '5.5.0-7.99.99',
            'typo3' => '7.6.0-8.99.99',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);

