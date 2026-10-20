<h1>A sequence of questions, trial <?= $_NOTE_MODULE['PAGE_INFO']['TRIAL_ID'] ?></h1>

<input type="hidden" name="trial_id" id="trial_id" value="<?= $_NOTE_MODULE['PAGE_INFO']['TRIAL_ID'] ?>">

<p class="h3 text-primary">The random word this time is <?= strtoupper($_NOTE_MODULE['PRESENTATION_DATA']['word']) ?></p>

<input type="hidden" name="random_word" id="random_word" value="<?= $_NOTE_MODULE['PRESENTATION_DATA']['word'] ?>">

<p>The words that could be chosen: STRIKE, PARADE, SAMPLE, BUCKET, FAMILY, INSERT, REASON, MIRROR, BOTHER, VIABLE. If you refresh this page, another word is chosen at random. (It could be the same word; the selection is random.)</p>

<div class="row">
    <div class="col-sm-12 text-center">
        <button type="submit" id="nextpage" class="btn btn-primary btn">Next</button>
    </div>
</div>
