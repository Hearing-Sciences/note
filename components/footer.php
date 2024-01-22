<div class="container">
    <hr>
    <footer>
        <?php if (isset($_NOTE_MODULE['FOOTER_COPYRIGHT']) && strlen($_NOTE_MODULE['FOOTER_COPYRIGHT']) > 2): ?>
            <p><?= ($_NOTE_MODULE['FOOTER_COPYRIGHT']) ?></p>
        <?php else: ?>
            <p>The information and materials on this website are the property of the University of Nottingham and/or Nottingham University Hospitals NHS Trust</p>
        <?php endif; ?>
        <?php
        if ($noteSurvey->isInDevelopment) {
            echo "<p> (dev system / page_".$_NOTE_MODULE['PAGES'][$noteSurvey->page].".php / page #".$noteSurvey->page.")</p>";
        }
        ?>
    </footer>
</div>
