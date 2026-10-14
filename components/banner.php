<div class="d-flex p-0" style="background-color: #10263b;" role="navigation">
    <div class="me-5"><img src="img/logo.jpg" alt="Company logo" class="mr-4 me-4 p-0" height="130px"></div>
    <div class="text-white align-self-end ml-auto ms-auto pr-4 pe-4">
        <div class="h2 text-right text-end">
            <?php if (isset($noteSurvey->moduleId) && isset($_NOTE_MODULE['TITLE'])): ?>
                <?= $_NOTE_MODULE['TITLE'] ?>
            <?php else: ?>
                Nottingham Online Test Environment
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- For debugging only, if 'preview' is added to the end of the module URL then show buttons to allow the developer to move between module pages quickly -->
<div class="container">
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
