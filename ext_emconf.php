<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Frontend TCE',
    'description' => 'It brings the deprecated TYPO3 4.x class tslib_feTCE and the processScript FEData setup with new features partly back into TYPO3 9+. This is needed by extensions based on the former TYPO3 FORM like tt_board and tt_guest. Only form MAIL and form wizards additionally require the extension compatibility6.',
    'category' => 'plugin',
    'version' => '0.5.0',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearcacheonload' => 1,
    'author' => 'Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => 'jambage.com',
    'constraints' => array(
        'depends' => array(
            'php' => '5.5.0-7.99.99',
            'typo3' => '8.7.0-9.5.99',
            'div2007' => '1.10.4-0.0.0',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);

