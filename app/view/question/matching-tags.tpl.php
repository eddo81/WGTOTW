<article class='article paddingBottom'>
<h1><?=$title?></h1>
<hr />

<?php foreach ($questions as $question) : ?>

<div class='questionContainer'>
	<h3 class='questionTitle'><a href='<?=$this->url->create('questions/id/' . $question->id)?>' class='profileLink'><?=$question->question_title?></a></h3> 
		<p class='questionSubTitle'>Postat av <a href='<?=$this->url->create('users/id/' . $question->user_id)?>' class='profileLink'><?=$question->question_author?></a> | <?=$question->created?></p>
		<?=$question->question_tags?>
</div>
 
<?php endforeach; ?>


</article>
