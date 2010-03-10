<h3>New Comment</h3>
<?= $this->startForm($this->comment); ?>
<? foreach($this->buildForm($this->comment) as $f): ?>

    <?= $f->label("email", true); ?>
    <?= $f->text_field("email"); ?>
    
    <?= $f->label("content", true); ?>
    <?= $f->text_area("content"); ?>
    
    <?= $f->submit(); ?>
<? endforeach; ?>
<?= $this->endForm(); ?>