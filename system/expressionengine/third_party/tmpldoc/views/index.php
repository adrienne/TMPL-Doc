<?php
	
	function clean_up_text($string) 
	{
		return str_replace(array("\n"), ' ', $string);
	}
	
?>

<div id="doc-wrapper">
	
	<a href="" id="home-link">Home</a>
	
	<?php if (count($template_data) === 0): ?>
	
		<section id="main">

			<div class="template intro">
				<h1>Welcome to TMPL Docs!</h1>
				<p>You must have your templates saved as files for us to display the documentation.</p>
		</div>
	
	<?php else: ?>
	
	<aside>
		
		<?php if (count($sites) > 2): ?>
		<?php echo form_open($mcp->base_url, array('id' => 'sites-form')); ?>
			<label for="site_name">Sites:</label>
			<select name="site_name">
				<?php foreach($sites as $site_name => $site_label): ?>
					<option value="<?php echo $site_name ?>"><?php echo $site_label ?></option>
				<?php endforeach; ?>
			</select>
		</form>
		<?php endif; ?>
		
		<nav>
			<ul>
			<?php $count = 0; ?>
			<?php foreach($template_data as $template_group => $templates): ?>
				<li class="<?php if ($count == 0) echo 'first '; ?>top">
					<a href=""><?php echo $template_group ?></a>
					<ul>
						<?php $count = 0; $total = count($templates)-1; ?>
						<?php foreach($templates as $template): ?>
							<li <?php if ($total == $count) echo 'class="last"'; ?>>
								<span class="template-icon"></span>
								<a href="<?php echo $count.'-'.str_replace('.', '', $template->name) ?>">
									<?php echo trim($template->name) ?>
								</a>
							</li>
						<?php $count++; ?>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php $count++; ?>
			<?php endforeach; ?>
			</ul>
		</nav>
	</aside>

	<section id="main">

		<div class="template intro">
			<h1>Welcome to TMPL Docs! <span class="notice">(BETA)</span></h1>
			<p>Start exploring by using the navigation to the left.</p>
			<p class="notice">*Currently only templates saved as files are supported.*</p>
			<br />
			<h4>Examples</h4>
			<br />
						
			<p class="note">Comment associated with a tag (needs the # signs):</p>
			<div class="example">
				<code>
					{!--#
					<br />
					&nbsp;&nbsp;&nbsp;&nbsp;Displays the currently viewed news article.
					<br /><br />
					&nbsp;&nbsp;&nbsp;&nbsp;@tag {exp:channel:entries} that holds the news articles
					<br /><br />
					&nbsp;&nbsp;&nbsp;&nbsp;@param url channel that holds the news articles
					<br />
					#--}
					<br />
					{exp:channel:entries channel="news" limit="1"} 
					<br />
					 &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlentities("<h2>{title}</h2>") ?>
					<br />
					{/exp:channel:entries}
				</code>
			</div>
			<br />

			<p class="note">Comment not associated with a tag:</p>
			<div class="example">
				<code>
					{!--:
					<br />
					&nbsp;&nbsp;&nbsp;&nbsp;This template displays the currently viewed news article. <br />
					&nbsp;&nbsp;&nbsp;&nbsp;@todo Debug routing conditionals <br />
					:--}
				</code>
			</div>
			<br />
			
			
			<p class="note">User Defined Globals (prefix global name with gbl):</p>
			<div class="example">
				<code>
					{gbl-name-of-global}
				</code>
			</div>
		
			<br />
			<p class="note">Snippets (prefix snippet name with snip):</p>
			<div class="example">
				<code>
					{snip-name-of-snippet}
				</code>
			</div>
		</div>

		<?php foreach($template_data as $template_group => $templates): ?>


			<?php $count = 0; ?>
			<?php foreach($templates as $template): ?>

				<div class="template" id="<?php echo $count.'-'.str_replace('.', '', $template->name) ?>">

					<div class="breadcrumb">
						<h1><?php echo $template_group .' / '. $template->name ?></h1>
					</div>

					<?php if (count($template->globals) > 0): ?>
					<h2 class="collapsable-handle"><span class="note">[+]</span>User-Defined Globals</h2>
					<div class="collapsable">
						<?php foreach($template->globals as $global): ?>
							<ul>
								<li><?php echo $global ?></li>
							</ul>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					
					<?php if (count($template->snippets) > 0): ?>
					<h2 class="collapsable-handle"><span class="note">[+]</span>Snippets</h2>
					<div class="collapsable">
						<?php foreach($template->snippets as $snippet): ?>
							<ul>
								<li><?php echo $snippet ?></li>
							</ul>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<?php if (!empty($template->comments)): ?>

						<h2>Comments</h2>

						<?php foreach($template->comments as $comment): ?>

							<div class="comment">

								<?php if (!empty($comment->tag)): ?>
								<p class="tag header-gradient"><strong><?php echo clean_up_text($comment->tag) ?></strong></p>	
								<?php endif; ?>

								<p class="note">
									<?php echo $comment->comment ?>
								</p>

								<?php if (count($comment->tags('@param')) > 0): ?>

								<h3>Parameters</h3>
								<ul>
								<?php foreach($comment->tags('@param') as $tag): ?>

										<li>
												<?php if ($tag->param): ?>
													<strong><?php echo $tag->param ?></strong> 
												<?php endif; ?>

												<?php if ($tag->value): ?>
													<span class="">[<?php echo $tag->value ?>]</span>
												<?php endif; ?>
												
												<?php if ($tag->comment): ?>
												<span class="note">- <?php echo $tag->comment ?> </span>
												<?php endif; ?>
										</li>

								<?php endforeach; ?>

								</ul><!-- end of parameters -->
								
								<?php endif; ?>

								<?php if (count($comment->tags('@tag')) > 0): ?>

								<h3>Tag</h3>
								<ul>
								<?php foreach($comment->tags('@tag') as $tag): ?>

										<li>
												<?php if ($tag->param): ?>
													<?php echo $tag->param ?>
												<?php endif; ?>

												<?php if ($tag->comment): ?>
												<span class="note">- <?php echo $tag->comment ?> </span>
												<?php endif; ?>
										</li>

								<?php endforeach; ?>

								</ul><!-- end of tags -->
								
								<?php endif; ?>
								
								
								<?php if (count($comment->tags('@todo')) > 0): ?>

								<h3>Todos</h3>
								<ul>
								<?php foreach($comment->tags('@todo') as $tag): ?>

										<li>
												<?php if ($tag->param): ?>
													<strong><?php echo $tag->param ?></strong> 
												<?php endif; ?>
												
												<?php if ($tag->comment): ?>
												<span class="note"><?php echo $tag->comment ?> </span>
												<?php endif; ?>
										</li>

								<?php endforeach; ?>

								</ul><!-- end of todos -->
								
								<?php endif; ?>
								
								
								<?php if (count($comment->tags('@unknown')) > 0): ?>

								<h3>Other</h3>
								<ul>
								<?php foreach($comment->tags('@unknown') as $tag): ?>

										<li>
												<?php if ($tag->param): ?>
													<strong><?php echo $tag->param ?></strong> 
												<?php endif; ?>
												
												<?php if ($tag->comment): ?>
												<span class="note"><?php echo $tag->comment ?> </span>
												<?php endif; ?>
										</li>

								<?php endforeach; ?>

								</ul><!-- end of other -->
								
								<?php endif; ?>
								
								

							</div>

						<?php endforeach; ?>

						
					<?php else: ?>	
						
						<p>There are no comments in this template.</p>
						
					<?php endif; // end of if template comments ?>

				</div>

			<?php $count++; ?>
			<?php endforeach; ?>

		<?php endforeach; ?>	
	</section>
	
	<?php endif; ?>
	
</div><!-- #wrapper -->
