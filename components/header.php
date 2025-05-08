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
    <link href="/css/bootstrap-536.min.css" rel="stylesheet">
<?php else: ?>
    <link rel="stylesheet" href="/css/bootstrap-462.min.css">
<?php endif; ?>
<link rel="stylesheet" href="/css/jquery-ui-1141.min.css">
<link rel="stylesheet" href="/css/jplayer.blue.monday.css" />
<link rel="stylesheet" href="/css/fontawesome-672-all.min.css"  />
<link rel="stylesheet" href="/css/main.css" />
<?php if (file_exists($noteSurvey->fsModulePath.'experiment.css')): ?>
    <link rel="stylesheet" href="<?= $noteSurvey->modulePath.'/experiment.css' ?>">
<?php endif; ?>
<?php if (isset($page_name) && file_exists($noteSurvey->fsModulePath.sprintf('page_%s.css',$page_name))): ?>
    <link rel="stylesheet" href="<?= $noteSurvey->modulePath.'/'.sprintf('page_%s.css',$page_name) ?>">
<?php endif; ?>

<?php if (isset($_NOTE_MODULE) && array_key_exists('JS_LIBRARIES', $_NOTE_MODULE) && in_array('jquery4', $_NOTE_MODULE['JS_LIBRARIES'])): ?>
    <script src="/js/jquery-4b2.min.js"></script>
<?php else: ?>
    <script src="/js/jquery-371.min.js"></script>
<?php endif; ?>
<script src="/js/jquery-ui-1141.min.js"></script>
<script src="/js/jquery.jplayer.min.js"></script>
<?php if (isset($_NOTE_MODULE) && array_key_exists('JS_LIBRARIES', $_NOTE_MODULE) && in_array('bootstrap5', $_NOTE_MODULE['JS_LIBRARIES'])): ?>
    <script src="/js/bootstrap-536.bundle.min.js"></script>
<?php else: ?>
    <script src="/js/bootstrap-462.bundle.min.js"></script>
<?php endif; ?>
<?php if (isset($_NOTE_MODULE) && array_key_exists('JS_LIBRARIES', $_NOTE_MODULE) && in_array('p5', $_NOTE_MODULE['JS_LIBRARIES'])): ?>
    <script src="js/p5.min.js"></script>
    <script src="js/p5.sound.min.js"></script>
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