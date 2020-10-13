<h3>Progress</h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th>UID</th><th>IP</th><th>Last seen</th>
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
			echo "<td>".$m['ipaddr']."</td>";
			echo "<td>".$m['timestamp']."</td>";
			for ($i=0; $i<count($_NOTE_MODULE["PAGES"]); $i++) {
				if ($i < $m['lastPage']) {
					echo '<td><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></td>';
				} elseif ($i == $m['lastPage']) {
					if ($i == (count($_NOTE_MODULE["PAGES"])-1)) {
						echo '<td><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></td>';
					} else {
						echo '<td><span class="glyphicon glyphicon-user" aria-hidden="true"></span></td>';
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
