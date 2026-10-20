<?php
    $query = 'SELECT * FROM responses';
    $result = $db->query($query);
?>
<h1>Responses</h1>

<?php foreach($result as $row): ?>
    <h2>From <?= $row['uid'] ?></h2>
    <p>The response: <?= $row['response'] ?></p>
    <p>The number: <?= $row['number'] ?></p>
<?php endforeach; ?>

<?php
    $query = 'SELECT * FROM trials order by uid, trial_id';
    $result = $db->query($query);
?>

<h2>Responses to the sequence of questions</h2>

<table class="table table-bordered">
<tr>
    <th>Unique ID</th>
    <th>Trial ID</th>
    <th>Random word</th>
    <th>Timestamp</th>
</tr>
<?php foreach($result as $row): ?>
    <tr>
        <td><?= $row['uid'] ?></td>
        <td><?= $row['trial_id'] ?></td>
        <td><?= $row['random_word'] ?></td>
        <td><?= $row['timestamp'] ?></td>
    </tr>
<?php endforeach; ?>
</table>
