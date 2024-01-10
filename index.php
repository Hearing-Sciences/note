<?php

require 'env.php';

$env = new Env;
//print_r($env);
require 'utils.php';

function db_connect($dir)
{
	try {
		// Connect and initialise if necessary
		$db = new PDO('sqlite:'.$dir.DIRECTORY_SEPARATOR.'database.sqlite3');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
	}
	catch(PDOException $e) {
	  // Log error
	  echo $e->getMessage();
	  return false;
	}
}

// This is necessary while the code transitions from arrays to objects
$_NOTE = $env->toArray();

if (isset($env->display_id) && $env->display_id === 'data') {
	// Load module-specific parameters
	require $env->fs_module_path.'config.php';
	$db = db_connect($env->module_dir);
	require $env->fs_module_path.'db.php'; // for the 'display_module_data' function
} elseif (isset($env->module_id)) {
	// Set up session to track user across module pages
	session_start();
	$new_visitor = false;
	if(!isset($_SESSION['uniqueID_'.$env->module_id])) {
		$new_visitor = true;
		$_SESSION['uniqueID_'.$env->module_id] = uniqid($env->module_id.'_');
	}
	$env->uid = $_SESSION['uniqueID_'.$env->module_id];
	
	// Load module-specific parameters
	require $env->fs_module_path.'config.php';

	try {
		// Connect and initialise if necessary
		$db = db_connect($env->module_dir);
		require $env->fs_module_path.'db.php';
		/**
		 * CRUDE HACK ALERT!
		 * The required file db.php should contain the functions db_retrieve_visitor_data and db_store_visitor_data.
		 * Debugging / linting systems can't check this because the require statement contains variables, so the checks
		 * will always raise an alarm that these functions can't be found. To avert this, the functions are created below if
		 * they don't exist. The debugger can spot this function definition and so the script passes.
		 */
		if (!function_exists('db_retrieve_visitor_data')) {
			function db_retrieve_visitor_data($result, $_NOTE_MODULE)
			{
				return false;
			}
		}
		if (!function_exists('db_retrieve_trial_data')) {
			function db_retrieve_trial_data()
			{
				return false;
			}
		}
		if (!function_exists('db_store_visitor_data')) {
			function db_store_visitor_data($page, $post, $uid)
			{
				return false;
			}
		}
		if (!function_exists('db_store_page_data')) {
			function db_store_page_data($page, $post, $uid, $db, $_NOTE_MODULE)
			{
				return false;
			}
		}
		// Resume a previous session if a valid session code is provided
		$session_code_err = 0;
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uid'])) {
			$qry = $db->prepare('SELECT lastPage FROM visitors WHERE uniqueID=:uid LIMIT 1');
			$qry->bindValue(':uid', $env->module_id . '_' . $_POST['uid']);
			$qry->execute();
			$result = $qry->fetch(PDO::FETCH_ASSOC);
			if (!empty($result)) {
				// Participant exists in the database so not a new visitor
				$_SESSION['uniqueID_'.$env->module_id] = $env->module_id . '_' . $_POST['uid'];
				$new_visitor = false;
			} else {
				$session_code_err = 1;
				$new_visitor = true;
				$_SESSION['uniqueID_'.$env->module_id] = uniqid($env->module_id.'_');
			}
			$env->uid = $_SESSION['uniqueID_'.$env->module_id];
			$qry->closeCursor();
		}
		
		if (!($env->part_id === 'preview') && !$new_visitor) {
			// Check if visitor has been registered and determine current page
			$qry = $db->prepare('SELECT * FROM visitors WHERE uniqueID=:uid LIMIT 1');
			$qry->bindValue(':uid', $env->uid);
			$qry->execute();
			$result = $qry->fetch(PDO::FETCH_ASSOC);
			if (!empty($result)) {
				$env->page = $result['lastPage'];
				// Retrieve module-specific visitor data
				db_retrieve_visitor_data($result, $_NOTE_MODULE);
				
				// If request is the result of a POST because the
				// user has clicked the 'next' button , move them onwards
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					// Each page contains a hidden field with the current page number.
					// This should match the current location of the participant
					// according to the database, indicating that they were previously
					// on the page where the data was submitted from. If not, then the
					// page has been refreshed rather than submitted and the POST 
					// relates to the previous page they were on and should be ignored.
					$valid_post = true;
					if (isset($_POST['page']) && !($env->page == $_POST['page'])) {
						$valid_post = false;
					}
					
					// If this is a valid visit to a page, check with the module
					// whether page-related data needs to be stored based on which
					// page we are on; e.g. user data or task-specific information.
					// It's up to the module to work out if it wants to store any
					// POST'd data from the previous page.
					if ($valid_post) {
						if (function_exists('db_store_page_data')) {
							db_store_page_data($env->page, $_POST, $env->uid, $db, $_NOTE_MODULE);
						}
						
						// If a module sets the TASK_FINISH_FLAG (e.g. if an adaptive
						// track is completed) then move them forward to a specified page.
						// TODO: Rename flag and page variables so that they don't mention
						//       'task' or 'adaptive track' as this functionality could be useful
						//       for any use case where a module wants to move people forward by
						//       more than 1 page. The page variable should also not refer to 'debrief' as
						//       this functionality could be used to jump participantions to
						//       other task pages also.
						// TODO: Check existance and validity of 'target' page.
						if (array_key_exists('TASK_FINISH_FLAG', $_NOTE_MODULE)) {
							if ($_NOTE_MODULE['TASK_FINISH_FLAG']) {
								$env->page = $_NOTE_MODULE['TASK_ADAPTIVETRACK_DEBRIEFPAGECOUNT'];
							} 
							else {
								$env->page += 1;
							}
						}
						else {
							// Move them to the next page
							$env->page += 1;
						}
						
						// Record progress
						$qry = $db->prepare("UPDATE visitors SET lastPage = :page WHERE uniqueID = :uid");
						$qry->execute(array(':page'=>$env->page,':uid'=>$env->uid));
						db_store_visitor_data($env->uid, $db, $_NOTE_MODULE);
					}
				}
			} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['uid'])) {
				// Not in database but moved past first page - start tracking from now
				$env->page = 1;
				$qry = $db->prepare("INSERT INTO visitors (uniqueID,lastPage) VALUES (:uid, :page)");
				$result = $qry->execute(array(':uid'=>$env->uid,':page'=>$env->page));
				db_store_visitor_data($env->uid, $db, $_NOTE_MODULE);
				// This saves any responses recorded on the first page, now that we have a UID
				db_store_page_data(0, $_POST, $env->uid, $db, $_NOTE_MODULE);
			} else {
				// Not in database and not a POST so revisiting front page
				$env->page = 0;
			}
			$qry->closeCursor();
		} 
		elseif (!($env->part_id === 'preview')) 
		{
			// First visit to page - don't hit database at all
			$env->page = 0;
		}
		elseif ($env->part_id === 'preview' && isset($env->other_id))
		{
			$env->page = $env->other_id;
		}
		
		// Close connection
		$db = null;
	}
	catch(PDOException $e) {
	  // Log error
	  //echo $e->getMessage();
	}

	if (isset($_NOTE_MODULE) && array_key_exists('PAGES', $_NOTE_MODULE)) {
		$page_name = $_NOTE_MODULE['PAGES'][$env->page];
		$total_pages = count($_NOTE_MODULE['PAGES']);	
	} else {
		$page_name = false;
		$total_pages = false;	
	}
}
?>
<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>
			<?php
				if (isset($env->module_id) && isset($_NOTE_MODULE['TITLE'])) {
					echo $_NOTE_MODULE['TITLE']." - ";
				}
				echo "Hearing Sciences @ the University of Nottingham";
			?>
		</title>
        <meta name="description" content="This website is used to conduct research into hearing and related conditions such as tinnitus, hyperacusis, and balance disorders. Here you can find research projects being run by Hearing Sciences, part of the School of Medicine at the University of Nottingham, and as part of the Hearing Theme of the NIHR Nottingham Biomedical Research Centre.">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<?php if (isset($_NOTE_MODULE) && array_key_exists('CSS_LIBRARIES', $_NOTE_MODULE) && in_array('bootstrap5', $_NOTE_MODULE['CSS_LIBRARIES'])): ?>
			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
		<?php else: ?>
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha256-+IZRbz1B6ee9mUx/ejmonK+ulIP5A5bLDd6v6NHqXnI=" crossorigin="anonymous">
		<?php endif; ?>
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" integrity="sha384-vIQIPaHJRqkO8xhnLxSZ0rHF6LyiWXbLF3grsa9pv9yDFyOPGrE1teHOuRNMJoZs" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/skin/blue.monday/css/jplayer.blue.monday.css" integrity="sha512-hy+p5g5kWOE+l5AHCBmTo1gq1OetErrJK8u6TT2WQYUVmINNwzu9pu7oyG2f6S+qRJnbGcHu/QXpC2FV4sAvSg==" crossorigin="anonymous" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />

		<link rel="stylesheet" href="/css/main.css" />
		<?php if (file_exists($env->fs_module_path.'experiment.css')): ?>
			<link rel="stylesheet" href="<?= $env->module_path.'/experiment.css' ?>">
		<?php endif; ?>
		<?php if (isset($page_name) && file_exists($env->fs_module_path.sprintf('page_%s.css',$page_name))): ?>
			<link rel="stylesheet" href="<?= $env->module_path.'/'.sprintf('page_%s.css',$page_name) ?>">
		<?php endif; ?>
		
		<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/dist/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js" integrity="sha512-g0etrk7svX8WYBp+ZDIqeenmkxQSXjRDTr08ie37rVFc99iXFGxmD0/SCt3kZ6sDNmr8sR0ISHkSAc/M8rQBqg==" crossorigin="anonymous"></script>
		<?php if (isset($_NOTE_MODULE) && array_key_exists('JS_LIBRARIES', $_NOTE_MODULE) && in_array('bootstrap5', $_NOTE_MODULE['JS_LIBRARIES'])): ?>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
		<?php else: ?>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha256-GRJrh0oydT1CwS36bBeJK/2TggpaUQC6GzTaTQdZm0k=" crossorigin="anonymous"></script>
		<?php endif; ?>

		<script src="<?php echo $env->base_uri; ?>js/main.js"></script>
		<?php if (file_exists($env->fs_module_path.'experiment.js')): ?>
			<script src="<?= $env->module_path.'/experiment.js' ?>"></script>
		<?php endif; ?>
		<?php if (isset($page_name) && file_exists($env->fs_module_path.sprintf('page_%s.js',$page_name))): ?>
			<script src="<?= $env->module_path.'/'.sprintf('page_%s.js',$page_name) ?>"></script>
		<?php endif; ?>
    </head>
    <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" role="navigation">
      <div class="container">
		  <h1>
          <a class="navbar-brand" href="#">
			<?php
				if (isset($env->module_id) && isset($_NOTE_MODULE['TITLE'])) {
					echo $_NOTE_MODULE['TITLE'];
				} else {
					echo "Hearing Sciences - Online Research Portal";
				}
			?>
		  </a>
		  </h1>
		<!-- For debugging only, if 'preview' is added to the
			 end of the module URL then show buttons to allow
			 the developer to move between module pages quickly -->
		<?php if ($env->base_uri == '/dev/' && $env->part_id === 'preview') { ?>
		<div id="navbarSupportedContent" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<?php if ($env->page > 0) { ?>
				<li class="nav-item"><a class="nav-link" href="/dev/<?php echo $env->module_id; ?>/preview/<?php echo $env->page-1; ?>"><i class="fas fa-angle-left"></i> Back</a></li>
				<?php } ?>
				<li class="nav-item"><a class="nav-link" href="/dev/<?php echo $env->module_id; ?>/preview/<?php echo $env->page+1; ?>">Next <i class="fas fa-angle-right"></i></a></li>
			</ul>
		</div>
		<?php } ?>
      </div>
    </nav>
	<?php
		// This is necessary while the code transitions from arrays to objects
		// Regenerate array in case things have changed
		$_NOTE = $env->toArray();
	?>
    <div class="container">
		<?php if ($env->display_id != 'data'): ?>
			<!-- Load the appropriate page content -->
			<form action="<?php echo $env->request_uri; ?>" method="POST">
				<?php $_NOTE = $env->toArray(); // in case the values have changed ?>
				<?php if (isset($page_name)) {
					include($env->fs_module_path.sprintf('page_%s.php',$page_name));
				} else {
					include ($_SERVER['DOCUMENT_ROOT'].'/blank.html');
				} ?>
				<input type="hidden" name="page" value="<?php echo $env->page; ?>" />
			</form>
			
			<?php
			
			// If this is the last page in the module then
			// clear the module session UID to allow the user
			// to complete the module again in future, either
			// in the same study or other studies.
			if (isset($_NOTE_MODULE) && count($_NOTE_MODULE['PAGES']) == ($env->page+1)) {
				end_module_session();
			}
			
			// Show a progress bar - currently module-based (i.e. shows
			// progress through the module) but eventually should be study-based
			// as studies will comprise multiple modules.
			if ((!isset($db) || !$db) && $env->page > 0) :?>
			<hr>
			<div class="progress">
			  <?php
				if (($env->page+1) == $total_pages) {
					$progress = 100;
				} else {
					$progress = round((100/($total_pages-1))*$env->page);
				}			  
			  ?><div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress; ?>%;"><?php echo $progress; ?>%</div>
			</div>
			<?php endif; ?>
		<?php elseif (isset($env->display_id) && $env->display_id === 'data'): ?>
			<?php
				// Hide default progress table if requested
				if (!(array_key_exists("HIDE_DEFAULT_PROGRESS_DATA", $_NOTE_MODULE) && $_NOTE_MODULE['HIDE_DEFAULT_PROGRESS_DATA'])) {
					include 'data.php';
				}
				if (function_exists('display_module_data')) {
					display_module_data($db, $_NOTE, $_NOTE_MODULE);
				}
				include_once($env->fs_module_path."data.php"); // Just in case it isn't included as part of display_module_data above
				// Close connection
				$db = null;
			?>
		<?php else: ?>
			<div class="jumbotron">
				<h2>Get involved in hearing research</h2>
				<p>This website is used to conduct research into hearing and related conditions such as tinnitus, hyperacusis, and balance disorders. Here you can find research projects being run by <a href="http://www.nottingham.ac.uk/research/groups/hearingsciences">Hearing Sciences</a>, part of the School of Medicine at the University of Nottingham, and as part of the Hearing Theme of the <a href="https://nottinghambrc.nihr.ac.uk/hearing/">NIHR Nottingham Biomedical Research Centre</a>. For additional information regarding our hearing research, please email <a href="mailto:adele.horobin@nottingham.ac.uk">adele.horobin@nottingham.ac.uk</a> or follow us on Twitter: <a href="https://twitter.com/uonhearsci">@uonhearsci</a> and <a href="https://twitter.com/hearingnihr">@hearingnihr</a></p>
			</div>
			<div style="height: 200px;"></div>
	    <?php endif; ?>
    </div>
    <div class="container">
      <hr>
      <footer>
		<?php if (isset($_NOTE_MODULE['FOOTER_COPYRIGHT']) && strlen($_NOTE_MODULE['FOOTER_COPYRIGHT']) > 2): ?>
		<p><?= ($_NOTE_MODULE['FOOTER_COPYRIGHT']) ?></p>
		<?php else: ?>
        <p>The information and materials on this website are the property of the University of Nottingham and/or Nottingham University Hospitals NHS Trust</p>
		<?php endif; ?>
		<?php
		if ($env->base_uri == '/dev/') {
			echo "<p> (dev system / page_".$_NOTE_MODULE['PAGES'][$env->page].".php / page #".$env->page.")</p>";
		}
		?>
      </footer>
    </div>
	
    </body>
</html>
