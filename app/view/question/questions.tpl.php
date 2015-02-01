<?php if (!empty($questions)) : ?>
<article class='smallArticle'>
<h3><?=$title?></h3>
<hr />

<ul class='question'>
<?php foreach ($questions as $question) : ?>
	<li style='margin-bottom: 10px;'>
		<a href="<?=$this->url->create('questions/id/' . $question->id)?>" class='profileLink'><?=$question->question_title?></a>
	</li>
<?php endforeach; ?>
</ul>

</article>
<?php endif; ?>