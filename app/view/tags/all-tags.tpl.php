<article class='article'>
<h1><?=$title?></h1>
<hr/>
<?php if ($tags != null) : ?>

<?=$tags?>

<?php else: ?>

<p class='alert'>Inga frågor har taggats.</p>	

<?php endif; ?>

</article>