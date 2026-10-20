<?php
function db_create_tables($db)
{
    $db->exec('CREATE TABLE IF NOT EXISTS responses (
        id INTEGER PRIMARY KEY,
        uid TEXT,
        response TEXT,
        number INTEGER,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)');

    $db->exec('CREATE TABLE IF NOT EXISTS trials (
        id INTEGER PRIMARY KEY,
        uid TEXT,
        trial_id INTEGER,
        random_word TEXT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)');
}

function db_store_page_data($page, &$post, $uid, &$db, &$_NOTE_MODULE) 
{
$pageName = is_array($_NOTE_MODULE['PAGES'][$page]) ? $_NOTE_MODULE['PAGES'][$page]['PAGE'] : $_NOTE_MODULE['PAGES'][$page];

switch($pageName) {
	case 'two_inputs':
		$qry    = $db->prepare('INSERT INTO responses (uid, response, number, timestamp) VALUES (:uid, :response, :number, :timestamp)');
		$result = $qry->execute([
			':uid' => $uid,
			':response' => $post['response'],
			':number' => $post['number'],
			':timestamp' => date('Y-m-d H:i:s')
		]);
	break;

	case 'trial': 
		$qry    = $db->prepare('INSERT INTO trials (uid, trial_id, random_word, timestamp) VALUES (:uid, :trial_id, :random_word, :timestamp)');
		$result = $qry->execute([
			':uid' => $uid,
			':random_word' => $post['random_word'],
			':trial_id' => $post['trial_id'],
			':timestamp' => date('Y-m-d H:i:s')
		]);
	break;
}

}

function db_fetch_page_data(&$db, $page_id, &$_NOTE_MODULE, $uid)
{
	$pageName = is_array($_NOTE_MODULE['PAGES'][$page_id]) ? $_NOTE_MODULE['PAGES'][$page_id]['PAGE'] : $_NOTE_MODULE['PAGES'][$page_id];
	
	switch($pageName) {
    case 'trial':
        $words = ['STRIKE', ' PARADE', ' SAMPLE', ' BUCKET', ' FAMILY', ' INSERT', ' REASON', ' MIRROR', ' BOTHER', ' VIABLE'];
        shuffle($words);
        return [
            'word' => $words[0]
        ];
    break;
}

}
