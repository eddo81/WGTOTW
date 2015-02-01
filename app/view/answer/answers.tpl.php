<?php if (!empty($answers)) : ?>
<article class='smallArticle'>
<h3><?=$title?></h3>
<hr />

<ul class='question'>
<?php foreach ($answers as $answer) : ?>
	<li style='margin-bottom: 10px;'>
		<a href="<?=$this->url->create('questions/id/' . $answer->question_id)?>" class='profileLink'><?=$answer->answer_content?></a>
	</li>
<?php endforeach; ?>
</ul>

</article>
<?php endif; ?>