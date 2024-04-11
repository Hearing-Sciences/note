<div class="container">
    <hr>
    <footer>
        <div class="progress">
            <?php
            $total_pages = count($_NOTE_MODULE["PAGES"]);
            if (($noteSurvey->getPage()+1) == $total_pages) {
                $progress = 100;
            } else {
                $progress = round((100/($total_pages-1)) * $noteSurvey->getPage());
            }
            ?>
            <div class="progress-bar" role="progressbar" aria-valuenow="<?= $progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $progress; ?>%;"><?= $progress; ?>%</div>
        </div>
        <?php if (isset($_NOTE_MODULE['FOOTER_COPYRIGHT']) && strlen($_NOTE_MODULE['FOOTER_COPYRIGHT']) > 2): ?>
            <p><?= ($_NOTE_MODULE['FOOTER_COPYRIGHT']) ?></p>
        <?php else: ?>
            <p>The information and materials on this website are the property of the University of Nottingham and/or Nottingham University Hospitals NHS Trust</p>
        <?php endif; ?>
        <?php
        if ($noteSurvey->isInDevelopment) {
            echo "<p> (dev system / page_".$_NOTE_MODULE['PAGES'][$noteSurvey->getPage()].".php / page #".$noteSurvey->getPage().")</p>";
        }
        ?>
    </footer>
</div>

</body>
</html>