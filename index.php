<?php

require 'env.php';
require 'utils.php';

if (isset($_NOTE["MODULE_ID"]) && $_NOTE["MODULE_ID"] === "data") {
	// Load module-specific parameters
	include 'modules'.DIRECTORY_SEPARATOR.$_NOTE["PART_ID"].DIRECTORY_SEPARATOR."config.php";
	
	$db_error = 0;
	try {
		// Connect and initialise if necessary
		$db = new PDO('sqlite:'.$_NOTE["MODULE_DIR"].DIRECTORY_SEPARATOR.'database.sqlite3');
		$db->setAttribute(PDO::ATTR_ERRMODE,
							PDO::ERRMODE_EXCEPTION);
		require 'modules'.DIRECTORY_SEPARATOR.$_NOTE["PART_ID"].DIRECTORY_SEPARATOR.'db.php';
	}
	catch(PDOException $e) {
	  // Log error
	  //echo $e->getMessage();
	  $db_error = 1;
	}
} elseif (isset($_NOTE["MODULE_ID"])) {
	// Set up session to track user across module pages
	session_start();
	$new_visitor = false;
	if(!isset($_SESSION['uniqueID_'.$_NOTE["MODULE_ID"]])) {
		$new_visitor = true;
		$_SESSION['uniqueID_'.$_NOTE["MODULE_ID"]] = uniqid($_NOTE["MODULE_ID"].'_');
	}
	$_NOTE['UID'] = $_SESSION['uniqueID_'.$_NOTE["MODULE_ID"]];
	
	// Load module-specific parameters
	include 'modules'.DIRECTORY_SEPARATOR.$_NOTE["MODULE_ID"].DIRECTORY_SEPARATOR."config.php";
	
	$db_error = 0;
	try {
		// Connect and initialise if necessary
		$db = new PDO('sqlite:'.$_NOTE["MODULE_DIR"].DIRECTORY_SEPARATOR.'database.sqlite3');
		$db->setAttribute(PDO::ATTR_ERRMODE,
							PDO::ERRMODE_EXCEPTION);
		require 'modules'.DIRECTORY_SEPARATOR.$_NOTE["MODULE_ID"].DIRECTORY_SEPARATOR.'db.php';
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		// Resume a previous session if a valid session code is provided
		$session_code_err = 0;
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uid'])) {
			$qry = $db->prepare('SELECT lastPage FROM visitors WHERE uniqueID=:uid LIMIT 1');
			$qry->bindValue(':uid', $_NOTE["MODULE_ID"] . '_' . $_POST['uid']);
			$qry->execute();
			$result = $qry->fetch(PDO::FETCH_ASSOC);
			if (!empty($result)) {
				// Participant exists in the database so not a new visitor
				$_SESSION['uniqueID_'.$_NOTE["MODULE_ID"]] = $_NOTE["MODULE_ID"] . '_' . $_POST['uid'];
				$new_visitor = false;
			} else {
				$session_code_err = 1;
				$new_visitor = true;
				$_SESSION['uniqueID_'.$_NOTE["MODULE_ID"]] = uniqid($_NOTE["MODULE_ID"].'_');
			}
			$_NOTE['UID'] = $_SESSION['uniqueID_'.$_NOTE["MODULE_ID"]];
		}
		
		if (!($_NOTE["PART_ID"] === "preview") && !$new_visitor) {
			// Check if visitor has been registered and determine current page
			$qry = $db->prepare('SELECT * FROM visitors WHERE uniqueID=:uid LIMIT 1');
			$qry->bindValue(':uid', $_NOTE['UID']);
			$qry->execute();
			$result = $qry->fetch(PDO::FETCH_ASSOC);
			if (!empty($result)) {
				$_NOTE["PAGE"] = $result['lastPage'];
				// Retrieve module-specific visitor data
				if (function_exists('db_retrieve_visitor_data')) {
					db_retrieve_visitor_data($result, $_NOTE_MODULE);
				}
				
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
					if (isset($_POST['page']) && !($_NOTE["PAGE"] == $_POST['page'])) {
						$valid_post = false;
					}
					
					// If this is a valid visit to a page, check with the module
					// whether page-related data needs to be stored based on which
					// page we are on; e.g. user data or task-specific information.
					// It's up to the module to work out if it wants to store any
					// POST'd data from the previous page.
					if ($valid_post) {
						if (function_exists('db_store_page_data')) {
							db_store_page_data($_NOTE['PAGE'], $_POST, $_NOTE['UID'], $db, $_NOTE_MODULE);
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
						if (array_key_exists("TASK_FINISH_FLAG", $_NOTE_MODULE)) {
                              if ($_NOTE_MODULE["TASK_FINISH_FLAG"]) {
                                    $_NOTE["PAGE"] = $_NOTE_MODULE['TASK_ADAPTIVETRACK_DEBRIEFPAGECOUNT'];
                              } 
							  else {
                                  $_NOTE["PAGE"] += 1;
                              }
                        }
                        else {
						      // Move them to the next page
                              $_NOTE["PAGE"] += 1;
                        }
					
					
						// Record progress
						$qry = $db->prepare("UPDATE visitors SET lastPage = :page, ipaddr = :ip WHERE uniqueID = :uid");
						$qry->execute(array(':page'=>$_NOTE["PAGE"],':ip'=>$ip,':uid'=>$_NOTE['UID']));
						if (function_exists('db_store_visitor_data')) {
							db_store_visitor_data($_NOTE['UID'], $db, $_NOTE_MODULE);
						}
					}
				}
			} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['uid'])) {
				// Not in database but moved past first page - start tracking from now
				$_NOTE["PAGE"] = 1;
				$qry = $db->prepare("INSERT INTO visitors (uniqueID,ipaddr,lastPage) VALUES (:uid, :ip, :page)");
				$result = $qry->execute(array(':uid'=>$_NOTE['UID'],':ip'=>$ip,':page'=>$_NOTE["PAGE"]));
				if (function_exists('db_store_visitor_data')) {
					db_store_visitor_data($_NOTE['UID'], $db, $_NOTE_MODULE);
				}
			} else {
				// Not in database and not a POST so revisiting front page
				$_NOTE["PAGE"] = 0;
			}
		} 
		elseif (!($_NOTE["PART_ID"] === "preview")) 
		{
			// First visit to page - don't hit database at all
			$_NOTE["PAGE"] = 0;
		}
		elseif ($_NOTE["PART_ID"] === "preview" && isset($_NOTE["OTHER_ID"]))
		{
			$_NOTE["PAGE"] = $_NOTE["OTHER_ID"];
		}
		
		// Close connection
		$db = null;
	}
	catch(PDOException $e) {
	  // Log error
	  //echo $e->getMessage();
	  $db_error = 1;
	}

	$page_name = $_NOTE_MODULE["PAGES"][$_NOTE["PAGE"]];
	$total_pages = count($_NOTE_MODULE["PAGES"]);
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>
			<?php
				if (isset($_NOTE["MODULE_ID"]) && isset($_NOTE_MODULE['TITLE'])) {
					echo $_NOTE_MODULE['TITLE']." - ";
				}
				echo "Hearing Sciences @ the University of Nottingham";
			?>
		</title>
        <meta name="description" content="This website is used to conduct research into hearing and related conditions such as tinnitus, hyperacusis, and balance disorders. Here you can find research projects being run by Hearing Sciences, part of the School of Medicine at the University of Nottingham, and as part of the Hearing Theme of the NIHR Nottingham Biomedical Research Centre.">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" integrity="sha384-xewr6kSkq3dBbEtB6Z/3oFZmknWn7nHqhLVLrYgzEFRbU/DHSxW7K3B44yWUN60D" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/skin/blue.monday/css/jplayer.blue.monday.css" integrity="sha512-hy+p5g5kWOE+l5AHCBmTo1gq1OetErrJK8u6TT2WQYUVmINNwzu9pu7oyG2f6S+qRJnbGcHu/QXpC2FV4sAvSg==" crossorigin="anonymous" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
	    <link rel="stylesheet" href="<?php echo $_NOTE["BASE_URI"]; ?>css/main.css">
		
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js" integrity="sha512-g0etrk7svX8WYBp+ZDIqeenmkxQSXjRDTr08ie37rVFc99iXFGxmD0/SCt3kZ6sDNmr8sR0ISHkSAc/M8rQBqg==" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
		<script src="<?php echo $_NOTE["BASE_URI"]; ?>js/main.js"></script>
    </head>
    <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" role="navigation">
      <div class="container">
          <a class="navbar-brand" href="#">
			<?php
				if (isset($_NOTE["MODULE_ID"]) && isset($_NOTE_MODULE['TITLE'])) {
					echo $_NOTE_MODULE['TITLE'];
				} else {
					echo "Hearing Sciences - Online Research Portal";
				}
			?>
		  </a>
		<!-- For debugging only, if 'preview' is added to the
			 end of the module URL then show buttons to allow
			 the developer to move between module pages quickly -->
		<?php if ($_NOTE["BASE_URI"] == '/dev/' && $_NOTE["PART_ID"] === "preview") { ?>
		<div id="navbarSupportedContent" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<?php if ($_NOTE["PAGE"] > 0) { ?>
				<li class="nav-item"><a class="nav-link" href="/dev/<?php echo $_NOTE["MODULE_ID"]; ?>/preview/<?php echo $_NOTE["PAGE"]-1; ?>"><i class="fas fa-angle-left"></i> Back</a></li>
				<?php } ?>
				<li class="nav-item"><a class="nav-link" href="/dev/<?php echo $_NOTE["MODULE_ID"]; ?>/preview/<?php echo $_NOTE["PAGE"]+1; ?>">Next <i class="fas fa-angle-right"></i></a></li>
			</ul>
		</div>
		<?php } ?>
      </div>
    </nav>
    <div class="container">
		<?php if (isset($_NOTE["MODULE_ID"]) && $_NOTE["MODULE_ID"] != "data") { ?>
			<!-- Load the appropriate page content -->
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
				<?php include('modules'.DIRECTORY_SEPARATOR.$_NOTE["MODULE_ID"].DIRECTORY_SEPARATOR.sprintf('page_%s.php',$page_name)); ?>
				<input type="hidden" name="page" value="<?php echo $_NOTE['PAGE']; ?>" />
			</form>
			
			<?php
			
			// If this is the last page in the module then
			// clear the module session UID to allow the user
			// to complete the module again in future, either
			// in the same study or other studies.
			if (count($_NOTE_MODULE["PAGES"]) == ($_NOTE['PAGE']+1)) {
				end_module_session();
			}
			
			// Show a progress bar - currently module-based (i.e. shows
			// progress through the module) but eventually should be study-based
			// as studies will comprise multiple modules.
			if (!$db_error && $_NOTE["PAGE"] > 0) {
			?>
			<hr>
			<div class="progress">
			  <?php
			  if (($_NOTE["PAGE"]+1) == $total_pages) {
				$progress = 100;
			  } else {
				$progress = round((100/($total_pages-1))*$_NOTE["PAGE"]);
			  }
			  
			  ?><div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress; ?>%;"><?php echo $progress; ?>%</div>
			</div>
			<?php }
		} elseif (isset($_NOTE["MODULE_ID"]) && $_NOTE["MODULE_ID"] === "data") {
			include 'data.php';
			if (function_exists('display_module_data')) {
				display_module_data($db, $_NOTE, $_NOTE_MODULE);
			}
			// Close connection
			$db = null;
		} else { ?>
			<div class="jumbotron">
				<h2>Get involved in hearing research</h2>
				<p>This website is used to conduct research into hearing and related conditions such as tinnitus, hyperacusis, and balance disorders. Here you can find research projects being run by <a href="http://www.nottingham.ac.uk/research/groups/hearingsciences">Hearing Sciences</a>, part of the School of Medicine at the University of Nottingham, and as part of the Hearing Theme of the <a href="https://nottinghambrc.nihr.ac.uk/hearing/">NIHR Nottingham Biomedical Research Centre</a>. For additional information regarding our hearing research, please email <a href="mailto:adele.horobin@nottingham.ac.uk">adele.horobin@nottingham.ac.uk</a> or follow us on Twitter: <a href="https://twitter.com/uonhearsci">@uonhearsci</a> and <a href="https://twitter.com/hearingnihr">@hearingnihr</a></p>
			</div>
			<div style="height: 200px;"></div>
	    <?php } ?>
    </div>
    <div class="container">
      <hr>
      <footer>
        <p>The information and materials on this website are the property of the University of Nottingham and/or Nottingham University Hospitals NHS Trust</p>
		<?php
		if ($_NOTE["BASE_URI"] == '/dev/') {
			echo "<p> (dev system / page_".$_NOTE_MODULE["PAGES"][$_NOTE["PAGE"]].".php / page #".$_NOTE["PAGE"].")</p>";
		}
		?>
      </footer>
    </div>
	
    </body>
</html>
