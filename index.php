<?php
spl_autoload_register(function ($class) {
    $unixClass = str_replace( "\\", '/', $class );
    include_once __DIR__.'/components/'.$unixClass.'.php';
});
use NOTE\Survey;
use NOTE\ErrorHandler;

session_start();
$noteSurvey = new Survey($_SERVER);

$_NOTE = $noteSurvey->toArray(); // This is necessary while the code transitions from arrays to objects

include $noteSurvey->fsModulePath.'config.php';
require_once('components/header.php');
require_once('components/banner.php');

if (!$noteSurvey->isValid) {
	require_once('blank.php');
	require_once('components/footer.php');
	exit;
}

$noteSurvey->debug = isset($_NOTE_MODULE['DEBUG']) ? $_NOTE_MODULE['DEBUG'] : false;

try {
    require_once $noteSurvey->fsModulePath.'db.php';
    $noteSurvey->Log->append('The file <strong>db.php</strong> has been loaded.', 'good');
} catch (error $e) {
    $error = new ErrorHandler($e);
    print $error->detailedMessage();
    $noteSurvey->Log->append('The file <strong>db.php</strong> coult NOTE be loaded. This will lead to an error.', 'error');
}

if (function_exists('db_create_tables')) {
    $noteSurvey->Log->append('Calling the function <strong>db_create_tables</strong>...', 'notice');
    db_create_tables($noteSurvey->participant->db);
} else {
    $noteSurvey->Log->append('The function <strong>db_create_tables</strong> could not be found. Possible error?', 'warning');
}
?>

<?php if ($noteSurvey->debug): ?>
    <script>
        $(document).ready(function() {
            $('#showHideDebugData').click(function() {
                $('.debugData').toggle();
            })
        })
    </script>
    <div class="container">
	    <h3 class="text-info"><i class="fa-solid fa-microchip"></i> Debug mode is ON <button class="btn btn-primary m-2" type="button" id="showHideDebugData">Show / Hide variable info</button></h3>
    </div>

    <script>
        $(document).ready(function() {
            $('#showHideSessionData').click(function() {
                $('#sessionData').toggle();
            })
        })
    </script>
    <div class="container">
        <div id="sessionData" class="debugData border border-4 border-info m-3 p-3" style="display: none;">
            <h4 class="text-info">Session data</h4>
            <pre><?php print_r($_SESSION); ?></pre>
            <h4 class="text-info">Survey object before data updates</h4>
            <pre><?php print_r($noteSurvey); ?></pre>
        </div>
    </div>
<?php endif; ?>
<?php
if (function_exists('db_retrieve_visitor_data')) {
    $noteSurvey->Log->append('Calling the function <strong>db_retrieve_visitor_data</strong>...', 'notice');
    db_retrieve_visitor_data($noteSurvey->participant->dbInfo, $_NOTE_MODULE);
}

if ($noteSurvey->isPostRequest) {
    $noteSurvey->Log->append('A POST request has been made i.e. data has probably been submitted.', 'notice');
    if (isset($_POST['page']) && $_POST['page'] == $noteSurvey->participant->getLastPageViewed()) {
        $noteSurvey->db_check_against_fields($_POST, $_NOTE_MODULE);

        if (function_exists('db_store_page_data')) {
            $noteSurvey->Log->append('Calling the function <strong>db_store_page_data</strong>...', 'notice');
            db_store_page_data($noteSurvey->getPage(), $_POST, $noteSurvey->participant->getUid(), $noteSurvey->participant->db, $_NOTE_MODULE);

            if (array_key_exists('TASK_FINISH_FLAG', $_NOTE_MODULE) && $_NOTE_MODULE['TASK_FINISH_FLAG'] === true) {
                $noteSurvey->Log->append('TASK_FINISH_FLAG has been set. Changing page number to <strong>'.$_NOTE_MODULE['TASK_ADAPTIVETRACK_DEBRIEFPAGECOUNT'].'</strong>', 'notice');
                $noteSurvey->setPage($_NOTE_MODULE['TASK_ADAPTIVETRACK_DEBRIEFPAGECOUNT']);
            } else {
                $noteSurvey->Log->append('Incrementing the page number...', 'notice');
                $noteSurvey->setPage($noteSurvey->getPage() + 1);
            }
            
            $noteSurvey->participant->setLastPageViewed($noteSurvey->getPage());
            if (function_exists('db_store_visitor_data')) {
                $noteSurvey->Log->append('Calling the function <strong>db_store_visitor_data</strong>...', 'notice');
                db_store_visitor_data($noteSurvey->participant->getUid(), $noteSurvey->participant->db, $_NOTE_MODULE);
            }
        }
    } else {
        $noteSurvey->Log->append('The function <strong>db_store_page_data</strong> could not be found. Possible error?', 'warning');
    }
}

if ($noteSurvey->mode === 'preview' && is_int($noteSurvey->otherId)) {
    $noteSurvey->setPage($noteSurvey->otherId);
}

if (isset($_NOTE_MODULE) && array_key_exists('PAGES', $_NOTE_MODULE)) {
    if ( is_array($_NOTE_MODULE['PAGES'][$noteSurvey->getPage()]) ) {
        $page_name = $_NOTE_MODULE['PAGES'][$noteSurvey->getPage()]['PAGE'];
        $_NOTE_MODULE['PAGE_INFO'] = $_NOTE_MODULE['PAGES'][$noteSurvey->getPage()];
    } else {
        $page_name = $_NOTE_MODULE['PAGES'][$noteSurvey->getPage()] ;
    }
	$last_page_id = count($_NOTE_MODULE['PAGES'])-1;	
    $noteSurvey->Log->append('Page name has been set as <strong>page_'.$page_name.'.php</strong>.', 'good');
} else {
	$page_name = false;
	$last_page_id = false;
    $noteSurvey->Log->append('Page name has NOTE been set. This will probably break NOTE.', 'error');
}

$noteSurvey->Log->append('Refreshing participant informaiton...', 'notice');
$noteSurvey->participant->refresh();

// Get data required for the current page (stimuli etc.)
if (function_exists('db_fetch_page_data')) {
    $noteSurvey->Log->append('Calling the function <strong>db_fetch_page_data</strong> to populate $_NOTE_MODULE[\'PRESENTATION_DATA\'].', 'notice');
    $_NOTE_MODULE['PRESENTATION_DATA'] = db_fetch_page_data($noteSurvey->participant->db, $noteSurvey->getPage(), $_NOTE_MODULE, $noteSurvey->participant->getUid());
} else {
    $_NOTE_MODULE['PRESENTATION_DATA'] = false;
}

$noteSurvey->Log->append('Closing database...', 'notice');
$noteSurvey->participant->closeDb();
?>

<div class="container">
    <form action="<?php echo $noteSurvey->requestUri; ?>" method="POST">
        <?php 
        // Reload $_NOTE as parts of the array may have been updated (e.g. $_NOTE['PAGE'])
        $noteSurvey->Log->append('Recreating the legacy $_NOTE array...', 'notice');
        $_NOTE = $noteSurvey->toArray(); // This is necessary while the code transitions from arrays to objects
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

<?php if ($noteSurvey->debug): ?>
    <div class="debugData" style="display: none;">
        <?php $noteSurvey->Log->render(); ?>
    </div>
    <div class="container">
        <div id="footerData" class="debugData border border-4 border-info m-3 p-3" style="display: none;">
            <h4 class="text-info">Survey object after data updates</h4>
            <pre><?php print_r($noteSurvey); ?></pre>
        </div>
    </div>
<?php endif; ?>

<?php require('components/footer.php'); ?>

<?php 
    if ($noteSurvey->getPage() == $last_page_id) {
        $_SESSION = array();
        session_destroy();
    }
?>
