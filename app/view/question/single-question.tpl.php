<article class='article overflow'>

<div class='left'>
	<h1 class='questionTitle'><?=$question->question_title?></h1>
	<p class='questionSubTitle'>Postat av <a href='<?=$this->url->create('users/id/' . $question->user_id)?>' class='profileLink'><?=$question->question_author?></a> | <?=$question->created?></p>
</div>

<?php if(isset($_SESSION['user']) && $_SESSION['user']->id === $question->user_id) : ?>	
	<a href='<?=$this->url->create('questions/delete/' . $question->id)?>' class='deleteButton right' style='margin-top: 25px;' >x</a>
<?php endif; ?>

<hr class='clearBoth'/>

	<?=$question->question_content?>
	
	<?php if(isset($_SESSION['user']) && $_SESSION['user']->id === $question->user_id) : ?>	
		<p class='questionSubTitle'><a href='<?=$this->url->create('questions/edit/' . $question->id)?>' class='profileLink'>Redigera inlägg</a></p>
	<?php endif; ?>
	
	<?=$question->question_tags?>

	<?php if(!empty($comments)) : ?>	
	
	<h4 class='dashed'>Kommenterer:</h4>
	
	<?php foreach ($comments as $comment) : ?>
		<div class='marginTop'>
		<p class='questionSubTitle'><a href='<?=$this->url->create('users/id/' . $comment->user_id)?>' class='profileLink'><?=$comment->comment_author?></a> | <?=$comment->created?></p> 
		<hr />
		<?=$comment->comment_content?>
		</div>
	<?php endforeach; ?>	
		
	<?php endif; ?>

</article>

<?php if(isset($_SESSION['user'])) : ?>
<div class='buttonWrapper overflow'>
	<a href='<?=$this->url->create('comments/qpost/' . $question->id)?>'  class='button green small marginRight right'>Kommentera</a>
	<a href='<?=$this->url->create('answers/post/' . $question->id)?>'  class='button blue small marginRight right'>Svara</a>
</div>
<?php endif; ?>	