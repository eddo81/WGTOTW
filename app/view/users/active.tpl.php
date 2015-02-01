<?php if (!empty($users)) : ?>
<article class='smallArticle'>
<h3><?=$title?></h3>
<hr />

<?php foreach ($users as $user) : ?>
	
	<div class="overflow usersummery">
		<a href='<?=$this->url->create('users/id/' . $user->id)?>'><img src='<?=$user->gravatar . '?s=40'?>' class='left'></a> 
		<div class="left marginLeft">
			<p><a href='<?=$this->url->create('users/id/' . $user->id)?>' class='profileLink'><?=$user->name?></a></p>
			<p><?=$user->email?></p>
		</div>	
	</div>

<?php endforeach; ?>

</article>
<?php endif; ?>