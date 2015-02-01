<article class='article paddingBottom overflow'>
<h1 class='left'><?=$title?></h1>

	<a href='<?=$this->url->create('questions/post/')?>' style='margin-top: 8px;' class='button blue right'>Ny fråga</a>

<hr class='clearBoth'/>
<?php if (!empty($questions)) : ?>

<?php foreach ($questions as $question) : ?>

<div class='questionContainer'>
	<h3 class='questionTitle'><a href='<?=$this->url->create('questions/id/' . $question->id)?>' class='profileLink'><?=$question->question_title?></a></h3> 
		<p class='questionSubTitle'>Postat av <a href='<?=$this->url->create('users/id/' . $question->user_id)?>' class='profileLink'><?=$question->question_author?></a> | <?=$question->created?></p>
		<?=$question->question_tags?>
</div>

<?php endforeach; ?>


<?php else: ?>

<p class='alert'>Det finns inga frågor att visa upp.</p>	

<?php endif; ?>

</article>