<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>
    <?php
        if (isset($noteSurvey->moduleId) && isset($_NOTE_MODULE['TITLE'])) {
            echo $_NOTE_MODULE['TITLE']." - ";
        }
        echo "Nottingham Online Test Environment";
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
<?php if (file_exists($noteSurvey->fsModulePath.'experiment.css')): ?>
    <link rel="stylesheet" href="<?= $noteSurvey->modulePath.'/experiment.css' ?>">
<?php endif; ?>
<?php if (isset($page_name) && file_exists($noteSurvey->fsModulePath.sprintf('page_%s.css',$page_name))): ?>
    <link rel="stylesheet" href="<?= $noteSurvey->modulePath.'/'.sprintf('page_%s.css',$page_name) ?>">
<?php endif; ?>

<?php if (isset($_NOTE_MODULE) && array_key_exists('JS_LIBRARIES', $_NOTE_MODULE) && in_array('jquery4', $_NOTE_MODULE['JS_LIBRARIES'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@4.0.0-beta/dist/jquery.min.js" integrity="sha256-qcGEWHXz2gu3QlFVrFYIqB1N6Z0xsQixd1cnhZFXO6U=" crossorigin="anonymous"></script>
<?php else: ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/dist/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js" integrity="sha512-g0etrk7svX8WYBp+ZDIqeenmkxQSXjRDTr08ie37rVFc99iXFGxmD0/SCt3kZ6sDNmr8sR0ISHkSAc/M8rQBqg==" crossorigin="anonymous"></script>
<?php if (isset($_NOTE_MODULE) && array_key_exists('JS_LIBRARIES', $_NOTE_MODULE) && in_array('bootstrap5', $_NOTE_MODULE['JS_LIBRARIES'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<?php else: ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha256-GRJrh0oydT1CwS36bBeJK/2TggpaUQC6GzTaTQdZm0k=" crossorigin="anonymous"></script>
<?php endif; ?>
<?php if (isset($_NOTE_MODULE) && array_key_exists('JS_LIBRARIES', $_NOTE_MODULE) && in_array('p5', $_NOTE_MODULE['JS_LIBRARIES'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/p5@1.9.3/lib/p5.min.js" integrity="sha256-L+4Tyqh4wjUK45I691vlFI2tf8BmzlPoN/0mol3LVTw=" crossorigin="anonymous"></script>
<?php endif; ?>
<?php if (file_exists($noteSurvey->baseUri.'/js/main.js')) : ?>
    <script src="<?php echo $noteSurvey->baseUri; ?>js/main.js"></script>
<?php endif; ?>
<?php if (file_exists($noteSurvey->fsModulePath.'experiment.js')): ?>
    <script src="<?= $noteSurvey->modulePath.'/experiment.js' ?>"></script>
<?php endif; ?>
<?php if (isset($page_name) && file_exists($noteSurvey->fsModulePath.sprintf('page_%s.js',$page_name))): ?>
    <script src="<?= $noteSurvey->modulePath.'/'.sprintf('page_%s.js',$page_name) ?>"></script>
<?php endif; ?>
</head>

<body>