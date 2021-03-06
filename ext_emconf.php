<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Frontend TCE',
    'description' => 'It brings the deprecated TYPO3 4.x class tslib_feTCE and the processScript FEData setup with new features partly back into TYPO3 9+. This is needed by extensions based on the former TYPO3 FORM like tt_board and tt_guest. Only form MAIL and form wizards additionally require the extension compatibility6.',
    'category' => 'plugin',
    'version' => '0.5.3',
    'state' => 'stable',
    'clearcacheonload' => 1,
    'author' => 'Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => 'jambage.com',
    'constraints' => array(
        'depends' => array(
            'php' => '7.0.0-7.4.99',
            'typo3' => '9.5.0-10.5.99',
            'div2007' => '1.10.4-0.0.0',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);

