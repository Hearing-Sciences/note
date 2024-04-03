<?php
try {
    // Connect and initialise if necessary
    $db = new PDO('sqlite:database.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    // Log error
    echo $e->getMessage();
}

$qry = $db->prepare("select * from surveys;");
$result = $qry->execute();
if ($result) {
    $surveys = $qry->fetchAll();
} else {
    echo "Error retrieving list of surveys from the database.";
}

function get_module_path($folder, $dev = false) : String
{
    if ($dev) {
        return $_SERVER['DOCUMENT_ROOT'].'/dev/modules/'.$folder;
    } else {
        return $_SERVER['DOCUMENT_ROOT'].'/modules/'.$folder;
    }
}

?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>
    NOTE Dashboard
</title>
<meta name="description" content="This website is used to conduct research into hearing and related conditions such as tinnitus, hyperacusis, and balance disorders. Here you can find research projects being run by Hearing Sciences, part of the School of Medicine at the University of Nottingham, and as part of the Hearing Theme of the NIHR Nottingham Biomedical Research Centre.">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" integrity="sha384-vIQIPaHJRqkO8xhnLxSZ0rHF6LyiWXbLF3grsa9pv9yDFyOPGrE1teHOuRNMJoZs" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/skin/blue.monday/css/jplayer.blue.monday.css" integrity="sha512-hy+p5g5kWOE+l5AHCBmTo1gq1OetErrJK8u6TT2WQYUVmINNwzu9pu7oyG2f6S+qRJnbGcHu/QXpC2FV4sAvSg==" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
<link rel="stylesheet" href="/css/main.css" />
<link rel="stylesheet" href="led.css" />

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/dist/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js" integrity="sha512-g0etrk7svX8WYBp+ZDIqeenmkxQSXjRDTr08ie37rVFc99iXFGxmD0/SCt3kZ6sDNmr8sR0ISHkSAc/M8rQBqg==" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        $('.showhide').click(function() {
            $type = $(this).attr('exp');
            if ($(this).text() == 'Show ' + $type) {
                $('.' + $type).show();
                $(this).text('Hide ' + $type);
            } else {
                $('.' + $type).hide();
                $(this).text('Show ' + $type);
            }
        });
    });
</script>

</head>
<body>
    <div class="container-fluid">
        <h1>Report</h1>
        <a class="btn btn-primary" href="/dashboard/">Back to the dashboard</a>
        <h2>DB, not in filesystem</h2>
        <p>The following records in the database have no corresponding folder in the file system (have they expired?):</p>

        <?php
            $qry = $db->prepare("select * from surveys where is_dev=0 and is_expired=0;");
            $result = $qry->execute();
            if ($result) {
                $surveys = $qry->fetchAll();
            } else {
                echo "Error retrieving list of surveys from the database.";
            }
        ?>
        <ul>
            <?php foreach($surveys as $survey): ?>
                <?php $folders[] = $survey['folder']; ?>
                <?php if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/modules/'.$survey['folder'])): ?>
                    <li><?= $survey['folder'] ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <h2>Filesystem, not in DB</h2>
        <p>The following folders have been found in the filesystem but don't have corresponding records in the DB:</p>

        <?php $dirs = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].'/modules'), array('..', '.') ); ?>
        <ul>
        <?php foreach($dirs as $dir): ?>
            <?php if (is_dir($_SERVER['DOCUMENT_ROOT'].'/modules/'.$dir) && !array_search($dir, $folders)): ?>
                <li><?= $dir ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
        </ul>
    </div>
</body>