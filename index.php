<?php
spl_autoload_register(function ($class) {
    $unixClass = str_replace( "\\", '/', $class );
    include_once __DIR__.'/components/'.$unixClass.'.php';
});
use NOTE\Survey;
use NOTE\ErrorHandler;

session_start();
$noteSurvey = new Survey($_SERVER);

require_once('components/header.php');
require_once('components/banner.php');

if (!$noteSurvey->isValid) {
	require_once('blank.html');
	require_once('components/footer.php');
	exit;
}

$_NOTE = $noteSurvey->toArray(); // For backwards compatibility
require $noteSurvey->fsModulePath.'config.php';

$debug = isset($_NOTE_MODULE['DEBUG']) ? $_NOTE_MODULE['DEBUG'] : false;

try {
    require $noteSurvey->fsModulePath.'db.php';
} catch (error $e) {
    $error = new ErrorHandler($e);
    print $error->detailedMessage();
}

if (function_exists('db_create_tables')) {
    db_create_tables($noteSurvey->participant->db);
}
?>

<?php if ($debug): ?>
    <div class="container">
        <h3>Session data</h3>
        <pre>
            <?php print_r($_SESSION); ?>
        </pre>
        <h3>Before...</h3>
        <pre>
            <?php print_r($noteSurvey); ?>
        </pre>
    </div>
<?php endif; ?>
<?php
if (function_exists('db_retrieve_visitor_data')) {
    db_retrieve_visitor_data($noteSurvey->participant->dbInfo, $_NOTE_MODULE);
}

if ($noteSurvey->isPostRequest) {
    if (isset($_POST['page']) && $_POST['page'] == $noteSurvey->participant->getLastPageViewed()) {
        if (function_exists('db_store_page_data')) {
            db_store_page_data($noteSurvey->getPage(), $_POST, $noteSurvey->participant->getUid(), $noteSurvey->participant->db, $_NOTE_MODULE);

            if (array_key_exists('TASK_FINISH_FLAG', $_NOTE_MODULE) && $_NOTE_MODULE['TASK_FINISH_FLAG'] === true) {
                $noteSurvey->setPage($_NOTE_MODULE['TASK_ADAPTIVETRACK_DEBRIEFPAGECOUNT']);
            } else {
                $noteSurvey->setPage($noteSurvey->getPage() + 1);
            }
            
            $noteSurvey->participant->setLastPageViewed($noteSurvey->getPage());
            if (function_exists('db_store_visitor_data')) {
                db_store_visitor_data($noteSurvey->participant->getUid(), $noteSurvey->participant->db, $_NOTE_MODULE);
            }
        }
    }
}

if ($noteSurvey->mode === 'preview' && is_int($noteSurvey->otherId)) {
    $noteSurvey->setPage($noteSurvey->otherId);
}

if (isset($_NOTE_MODULE) && array_key_exists('PAGES', $_NOTE_MODULE)) {
	$page_name = $_NOTE_MODULE['PAGES'][$noteSurvey->getPage()];
	$last_page_id = count($_NOTE_MODULE['PAGES'])-1;	
} else {
	$page_name = false;
	$last_page_id = false;	
}
$noteSurvey->participant->refresh();
$noteSurvey->participant->closeDb();
?>

<div class="container">
    <form action="<?php echo $noteSurvey->requestUri; ?>" method="POST">
        <?php 
        // Load the appropriate page content
        try {
            require($noteSurvey->fsModulePath.sprintf('page_%s.php',$page_name));
        } catch (error $e) {
            $error = new ErrorHandler($e);
            print $error->detailedMessage();        
        }
        ?>

        <input type="hidden" name="page" value="<?php echo $noteSurvey->getPage(); ?>" />
    </form>
</div>

<?php if ($debug): ?>
    <div class="container">
        <h3>After...</h3>
        <pre>
            <?php print_r($noteSurvey); ?>
        </pre>
    </div>
<?php endif; ?>

<?php require('components/footer.php'); ?>

<?php 
    if ($noteSurvey->getPage() == $last_page_id) {
        $_SESSION = array();
        session_destroy();
    }
?>