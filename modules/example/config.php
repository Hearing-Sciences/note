<?php

$_NOTE_MODULE['TITLE'] = 'Example NOTE Survey';
$_NOTE_MODULE['PAGES'] = [];
$_NOTE_MODULE['COPYRIGHT'] = 'A copyright message could be placed here.';
$_NOTE_MODULE['DEBUG'] = True;
$_NOTE_MODULE['GOLIVE_DATE'] = '2025-10-01';
$_NOTE_MODULE['CSS_LIBRARIES'] = ['bootstrap5'];
$_NOTE_MODULE['JS_LIBRARIES'] = ['bootstrap5'];
$_NOTE_MODULE['PAGES'][] = 'welcome';
$_NOTE_MODULE['PAGES'][] = 'two_inputs';
foreach(range(1, 5) as $trial_id) {
    $_NOTE_MODULE['PAGES'][] = [
        'PAGE' => 'trial',
        'TRIAL_ID' => $trial_id, 
		'FIELDS' => [
					'trial_id' => 'INTEGER', 
					'random_word' => 'TEXT',
					],
		'TABLE' => 'trials'
    ];
}
$_NOTE_MODULE['PAGES'][] = 'goodbye';
$_NOTE_MODULE['FOOTER_COPYRIGHT'] = '';
$_NOTE_MODULE['SPECIAL_FIELDS'] = [
	'IGNORE' => [
      	'uid' => 'TEXT',
		'id' => 'INTEGER',
		'timestamp' => 'DATETIME'
	],
	'COMMON' => [
	],
];
