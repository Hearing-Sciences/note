<?php
spl_autoload_register(function ($class) {
    $unixClass = str_replace( "\\", '/', $class );
    include_once __DIR__.'/components/'.$unixClass.'.php';
});
use NOTE\Survey;

//$debug = true;

$noteSurvey = new Survey($_SERVER);

// This is necessary while the code transitions from arrays to objects
$_NOTE = $noteSurvey->toArray();

// Load module-specific parameters
try {
	require $noteSurvey->fsModulePath.'config.php';
} catch (Throwable $e) {

	require_once('components/header.php');
	require_once('components/banner.php');
	include 'data_blank.php';

	exit;
}

$db = $noteSurvey->participant->db;
require_once $noteSurvey->fsModulePath.'db.php'; 

if (isset($_NOTE_MODULE['GOLIVE_DATE'])) {
	$dt = DateTime::createFromFormat("Y-m-d", $_NOTE_MODULE['GOLIVE_DATE']);
} else {
	$dt = false;
}

if ($dt !== false) {
	$golive_date = $dt->format('Y-m-d')	;
} else {
	$golive_date = '1900-01-01';
}

$today = date('d-m-y h:i:s');
$visitorsQry = $db->query('select COUNT(uniqueID) as total, 
	strftime("%Y-%m-%d", timestamp) as "day" 
	from visitors where timestamp > date("'.$golive_date.'") group by "day" order by "day";');
$visitors = $visitorsQry->fetchAll();
$visits = array();

while (!empty($visitors)) {
	$row = array_shift($visitors);
	$date = new DateTime($row['day']);
	$visits[$date->format('Y-m-d')] = $row['total'];
	$date->modify('+1 day');
	while (!empty($visitors) && $visitors[0]['day'] != $date->format('Y-m-d')) {
		$visits[$date->format('Y-m-d')] = 0;
		$date->modify('+1 day');
	}
}

$pageCount = count($_NOTE_MODULE['PAGES']);
for ($percentage=1; $percentage<=4; $percentage++) {
	$progString = 'Between '.(100*($percentage-1)/4).'% and '.(100 * ($percentage)/4).'%';
	$progressQry = $db->query('select COUNT(uniqueID) as total from visitors where timestamp > date("'.$golive_date.'") and lastPage between '.(($pageCount * ($percentage-1))/4).' and '.(($pageCount * ($percentage))/4).';');	
	$progArray = $progressQry->fetchAll(PDO::FETCH_ASSOC);
	$progress[$progString] = $progArray[0]['total'];
}

$db_size = filesize(($noteSurvey->fsModulePath.'/database.sqlite3'))/1024/1024;

?>

<html lang="en">

<head>
<?php require('components/header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script language="javascript">
	$(document).ready(function() {
		$('.clipboardable').click(function() {
			$id = $(this).attr('target') + '-tsv';
			$div = $(this).attr('target') + '-message';
			navigator.clipboard.writeText($($id).text()).then(function() {
				$($div).html('<div class="text-success h4">Copy successful.</div>').fadeIn();
			}), function() {
				$($div).html('<div class="text-danger h4">ERROR</div>');
			};
			const myTimeout = setTimeout(clearMessage, 2000);
		});

		<?php if (!empty($visits)): ?>
			const visitorCht = $('#visitorTimes');
			const visitorChart = new Chart (visitorCht, {
				type: 'line',
				data: {
					labels: ['<?= implode("','", array_keys($visits)) ?>'],
					datasets: [{
						label: 'Website Visits',
						data: [<?= implode(",", array_values($visits)) ?>],
						borderColor: 'rgb(0,255,0)',
						tension: 0.1
					}]
				}
			})
		<?php endif; ?>
		<?php if (!empty($progress)): ?>
			const progressCht = $('#visitorProgress');
			const progressChart = new Chart (progressCht, {
				type: 'pie',
				data: {
					labels: ['<?= implode("','", array_keys($progress)) ?>'],
					datasets: [{
						label: 'Website Visits',
						data: [<?= implode(",", array_values($progress)) ?>],
						backgroundColor: [
						'rgb(255, 99, 132)',
						'rgb(255, 153, 0)',
						'rgb(255, 255, 0)',
						'rgb(86, 86, 255)'
						],
					}]
				}
			})
		<?php endif; ?>
	});
	
	function clearMessage() {
		$('.clipboardMessage').html('');
	}    

</script>

</head>

<body>
<div class="container">

<?php if ($debug): ?>
<h3>Session data</h3>
<pre>
	<?php if (isset($_SESSION) && !empty($_SESSION)): ?>
    <?php print_r($_SESSION); ?>
	<?php else: ?>
	<p>No session values could be found.</p>
	<?php endif; ?>
</pre>
<h3>Before...</h3>
<pre>
    <?php print_r($noteSurvey); ?>
</pre>
<?php endif; ?>

<h1><?= 'Data report run at '.$today ?></h1>

<h2>Current database size: <span class="<?= $db_size > 10 ? 'text-danger' : 'text-success'?>"><?= number_format($db_size, 2) ?> MB</span></h2>

<?php if($db_size>10): ?>
	<div class="border border-danger border-5 p-2">
	<p>There have been issues with database sizes exceeding 10MB. While this may not be a problem for your survey you may wish to check that data is being saved as efficiently as possible.</p>
	</div>
<?php endif; ?>

<h2>Go-live date: <?= $golive_date ?></h2>
<?php if ($golive_date == '1900-01-01'): ?>
	<p>NOTE was unable to read a go-live date in the configuration file <em>config.php</em>. Set or check this as <code>$_NOTE_MODULE['GOLIVE_DATE']</code>. NOTE has used '1900-01-01' as the date to make sure that database queries return all of the data.</p>
<?php else: ?>
	<p>NOTE has retrieved the go-live date from the configuration file <em>config.php</em>. The charts and progress table below will ignore visitors and data recorded before this date.</p>
<?php endif; ?>

<div class="row">
	<div class="col-sm-6">
		<h3>Visitors by day</h3>
		<canvas id="visitorTimes"></canvas>
	</div>
	<div class="col-sm-3" >
		<h3>Visitor Progress</h3>
		<canvas id="visitorProgress"></canvas>
	</div>
</div>

<h3>Progress</h3>

<div class="row m-2">
    <div class="col-sm-6 text-right">
        <button type="button" class="btn btn-primary clipboardable" target="#progress" id="copyToClipboard">Copy the progress information to the clipboard (TSV format)  <i class="fas fa-copy"></i></button>
    </div>
    <div class="col-sm-6 text-left">
        <div class="clipboardMessage" id="progress-message"></div>
    </div>
</div>
<?php $visitors = $db->query('SELECT * FROM visitors WHERE timestamp > date("'.$golive_date.'") and lastPage > 0'); ?>
<pre id="progress-tsv" style="display: none;">
Data report run at <?= $today ?>

UID&#9;Last seen&#9;Last page
<?php foreach ($visitors as $m): ?><?= $m['uniqueID'] ?>&#9;<?= $m['timestamp'] ?>&#9;<?= $m['lastPage'] ?>

<?php endforeach; ?>
</pre>

<table class="table table-striped">
	<thead>
		<tr>
			<th>UID</th><th>Last seen</th>
			<?php
			$lastPage = NULL;
			$dup_page_count = 0;
			for ($i=0; $i<count($_NOTE_MODULE["PAGES"]); $i++) {
				$page_name = is_array($_NOTE_MODULE["PAGES"][$i]) ? $_NOTE_MODULE["PAGES"][$i]['PAGE'] : $_NOTE_MODULE["PAGES"][$i];
				if ($page_name === $lastPage) {
					$dup_page_count += 1;
					echo "<th>".$page_name." (".$dup_page_count.")</th>";
				} else {
					$dup_page_count = 0;
					echo "<th>".$page_name."</th>";
				}
				$lastPage = $page_name;
			} ?>
		</tr>
	</thead>
	<tbody>
		<?php
		$visitors = $db->query('SELECT * FROM visitors WHERE lastPage > 0 AND timestamp > date("'.$golive_date.'") ORDER BY timestamp DESC');
		foreach ($visitors as $m) {
			echo "<tr>";
			echo "<td>".$m['uniqueID']."</td>";
			echo "<td>".$m['timestamp']."</td>";
			for ($i=0; $i<count($_NOTE_MODULE["PAGES"]); $i++) {
				if ($i < $m['lastPage']) {
					echo '<td><i class="fas fa-check-circle"></i></td>';
				} elseif ($i == $m['lastPage']) {
					if ($i == (count($_NOTE_MODULE["PAGES"])-1)) {
						echo '<td><i class="fas fa-check-circle"></i></td>';
					} else {
						echo '<td><i class="fas fa-user"></i></td>';
					}
				} else {
					echo '<td></td>';
				}
			}
			echo "</tr>";
		}
		?>
	</tbody>
</table>
<p></p>

<?php
include_once($noteSurvey->fsModulePath."data.php"); 
// Close connection
$db = null;
?>
</div>

<?php require('components/footer.php'); ?>

</body>
</html>
