<article class='article'>
<h1><?=$title?></h1>
<hr/>

<div class='alert overflow'>
<img src='<?=$user->gravatar . '?s=100'?>' class='left'>
	<div class="left marginLeft"> 
		<p class='notop'><strong>Namn: </strong><?=$user->name?></p>
		<p><strong>E-post: </strong><?=$user->email?></p>
		<p><strong>Hemsida: </strong><?=$user->web?></p>
	</div>
</div>
 
</article>
 

 