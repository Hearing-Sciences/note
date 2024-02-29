<nav class="navbar navbar-expand-lg navbar-dark bg-dark" role="navigation">
    <div class="container">
        <h1>
        <a class="navbar-brand" href="#">
        <?= isset($_NOTE_MODULE['TITLE']) ? $_NOTE_MODULE['TITLE'] : "Hearing Sciences - Online Research Portal" ?>
        </a>
        </h1>
    <!-- For debugging only, if 'preview' is added to the end of the module URL then show buttons to allow the developer to move between module pages quickly -->
    <?php if ($noteSurvey->isInDevelopment && $noteSurvey->mode === 'preview'): ?>
    <div id="navbarSupportedContent" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
            <?php if ($noteSurvey->getPage() > 0) { ?>
            <li class="nav-item"><a class="nav-link" href="/dev/preview/<?php echo $noteSurvey->moduleId; ?>/<?php echo $noteSurvey->getPage()-1; ?>"><i class="fas fa-angle-left"></i> Back</a></li>
            <?php } ?>
            <li class="nav-item"><a class="nav-link" href="/dev/preview/<?php echo $noteSurvey->moduleId; ?>/<?php echo $noteSurvey->getPage()+1; ?>">Next <i class="fas fa-angle-right"></i></a></li>
        </ul>
    </div>
    <?php endif; ?>
    </div>
</nav>
