<section class="section">
    <div class="container split">
        <div>
            <span class="eyebrow">Overview</span>
            <h2>College guidance for international applicants</h2>
            <p><?= e($college['summary']) ?></p>
            <p><?= e($college['applicationSupport']) ?></p>
        </div>
        <div class="key-facts">
            <div><span>Location</span><strong><?= e($college['location']) ?></strong></div>
            <div><span>Tuition / yr</span><strong><?= e($college['tuition']) ?></strong></div>
            <div><span>Duration</span><strong><?= e($college['duration']) ?></strong></div>
            <div><span>Intakes</span><strong><?= e($college['intakes']) ?></strong></div>
        </div>
        <?php if (!empty($college['additionalFeeNote'])): ?><p class="college-fee-note"><?= e($college['additionalFeeNote']) ?></p><?php endif; ?>
        <p class="college-fee-disclaimer"><?= e($college['feeDisclaimer']) ?></p>
    </div>
</section>
<section class="section soft">
    <div class="container">
        <div class="section-heading"><span class="eyebrow">Programmes</span><h2>Available programme interests</h2></div>
        <div class="programme-tags large-tags"><?php foreach ($college['programmes'] as $programme): ?><span><?= e($programme) ?></span><?php endforeach; ?></div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="section-heading"><span class="eyebrow">Requirements</span><h2>Information to prepare</h2></div>
        <ul class="check-list columns"><li>Academic results</li><li>Passport status</li><li>Birth certificate</li><li>Passport photograph</li><li>Transcript where applicable</li><li>Preferred intake and programme</li></ul>
    </div>
</section>
<section class="section cta-band"><div class="container cta-inner"><div><span class="eyebrow">Next step</span><h2>Discuss this college with MedCon</h2><p><?= e($college['applicationSupport']) ?></p></div><div class="cta-actions"><a class="btn btn-primary" href="apply.php?college=<?= e($college['slug']) ?>">Apply Now</a><a class="btn btn-secondary" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></div></div></section>
