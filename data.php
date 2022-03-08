<?php
	$visitorsQry = $db->query('select COUNT(uniqueID) as total, 
	strftime("%Y-%m-%d", timestamp) as "day" 
	from visitors group by "day" order by "day";');
	$visitors = $visitorsQry->fetchAll();
	$visits = array();
	//print_r($visitors); die;
	while (!empty($visitors)) {
		$row = array_shift($visitors);
		$date = new DateTime($row['day']);
		$visits[$date->format('Y-m-d')] = $row['total'];
		$date->modify('+1 day');
		//print($data[0]['day']);
		while (!empty($visitors) && $visitors[0]['day'] != $date->format('Y-m-d')) {
			$visits[$date->format('Y-m-d')] = 0;
			$date->modify('+1 day');
			//print ($data[0]['day']).', '; print($date->format('Y-m-d').'<br>');
		}
	}

	$pageCount = count($_NOTE_MODULE['PAGES']);
	for ($percentage=1; $percentage<=4; $percentage++) {
		$progString = 'Between '.(100*($percentage-1)/4).'% and '.(100 * ($percentage)/4).'%';
		$progressQry = $db->query('select COUNT(uniqueID) as total from visitors where lastPage between '.(($pageCount * ($percentage-1))/4).' and '.(($pageCount * ($percentage))/4).';');	
		$progArray = $progressQry->fetchAll(PDO::FETCH_ASSOC);
		$progress[$progString] = $progArray[0]['total'];
	}
	//print_r($progress); die;

?>
<script src="/js/chart.min.js"></script>
<script language="javascript">
	$(document).ready(function() {
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
</script>
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
<table class="table table-striped">
	<thead>
		<tr>
			<th>UID</th><th>Last seen</th>
			<?php
			$last_page = NULL;
			$dup_page_count = 0;
			for ($i=0; $i<count($_NOTE_MODULE["PAGES"]); $i++) {
				$page_name = $_NOTE_MODULE["PAGES"][$i];
				if ($page_name === $last_page) {
					$dup_page_count += 1;
					echo "<th>".$page_name." (".$dup_page_count.")</th>";
				} else {
					$dup_page_count = 0;
					echo "<th>".$page_name."</th>";
				}
				$last_page = $page_name;
			} ?>
		</tr>
	</thead>
	<tbody>
		<?php
		$visitors = $db->query("SELECT * FROM visitors WHERE lastPage > 0");
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
