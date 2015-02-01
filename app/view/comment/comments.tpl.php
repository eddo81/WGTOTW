<?php if (!empty($comments)) : ?>
<article class='smallArticle'>
<h3><?=$title?></h3>
<hr />



<ul class='question'>
<?php foreach ($comments as $comment) : ?>
	<li style='margin-bottom: 10px;'>
		<a href="<?=$this->url->create('questions/id/' . $comment->question_id)?>" class='profileLink'><?=$comment->comment_content?></a>
	</li>
<?php endforeach; ?>
</ul>

</article>
<?php endif; ?>







