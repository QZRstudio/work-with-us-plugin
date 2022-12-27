<?php

/* 
	Just a render function to show the CTA Editor in the admin;
	When rendering the CTA Editor, we will check if the user is
	an admin. If not, we will show a message and exit the function.
*/

function ilpost_plugin_settings_page() {
	add_options_page(
		'CTA Editor',
		'CTA Editor',
		'manage_options',
		'ilpost-plugin',
		'ilpost_plugin_settings_page_render'
	);
}
add_action( 'admin_menu', 'ilpost_plugin_settings_page' );

function ilpost_plugin_settings_page_render() {

	// Check that the user is allowed to update options
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// Get all the tags
	$tags = get_terms( array(
		'taxonomy' => 'post_tag',
		'hide_empty' => false,
	) );

	// Save the CTA if the form is submitted and filter by $_POST['tag'] to avoid saving all the CTA at once
	if(isset($_POST['tag'])) {

		// Preparing the data
		$status = isset($_POST['cta-status-'.$_POST['tag']]) && $_POST['cta-status-'.$_POST['tag']];
		$html = $_POST['cta'][$_POST['tag']];
		$callback = 'qzr_rule_after_nth_element';
		$options = array(
			'element' => $_POST['cta-rule-element-'.$_POST['tag']],
			'nth' => $_POST['cta-rule-nth-'.$_POST['tag']],
		);

		// Update the CTA
		qzr_update_cta($_POST['tag'],array(
			'status' => $status,
			'html' => $html,
			'tag_ID' => $_POST['tag'],
			'rule' => array(
				'callback' => $callback,
				'options' => $options
			)
		));
	}

	// Get all the CTA saved in the options table
	$ctas = qzr_get_cta_data();
	
	?>
	<div class="wrap">
		<h1>CTA Editor</h1>

		<div class="description">
			<p>Ciao, siamo <b><a href="https://qzrstudio.com" target="_blank" title="QZR">QZR</a></b>,<br>
			ci sembrava interessante fare un plugin in grado di soddisfare più condizioni,</p>
			<p>rendendo il lavoro pronto a soddisfare le esigenze del futuro: questo è il modo di progettare con cui ci piace lavorare.</p>
		</div>

		<?php if($tags): ?>
			<h2>TAGS</h2>
			<form method="post">

				<!-- Tag selector, on change the JS will show the current CTA Editor -->
				<select name="tag" id="tag">
					<option value="-1">Seleziona un tag</option>
					<?php
						foreach ($tags as $tag):
							$cta = isset($ctas[$tag->term_id]) ? $ctas[$tag->term_id] : false;
							$label = $cta && $cta['status'] ? $tag->name . ' (Attivo)' : $tag->name;
							?>
								<option value="<?= $tag->term_id; ?>" <?= isset($_GET['selected']) && $_GET['selected'] == $tag->term_id? 'selected' : ''?>><?= $label ?></option>
							<?php
						endforeach;
					?>
				</select>
				
				<div class="no-tags-selected cta-editor" data-tag-id="-1">
					<p>Nessun tag selezionato.</p>
				</div>

				<?php
					// Loop through all the tags and show the CTA Editor for each one with the name cta-editor-<tag_id>
					foreach ($tags as $tag):

						// Get the CTA for the tag
						$cta = qzr_get_cta($tag->term_id);

						?>
						<div class="cta-editor" data-tag-id="<?= $tag->term_id?>">
							<div class="cta-editor-wrapper-heading">
								<h2><?= $tag->name; ?><span class="current-status"></span></h2>
								<div class="cta-editor-wrapper-options">
									<div class="status">
										<label for="cta-status-<?= $tag->term_id; ?>">
											<input type="checkbox" name="cta-status-<?= $tag->term_id; ?>" id="cta-status-<?= $tag->term_id; ?>" value="1" <?= $cta['status'] ? 'checked' : ''; ?>>
											<div class="label"></div>
										</label>
									</div>
									<div data-option="settings" class="dashicons dashicons-admin-generic"></div>
								</div>
							</div>

							<!-- Advanced Settings -->
							<div class="cta-settings">
								<table class="form-table">
									<tbody>

										<!-- Preview -->
										<tr>
											<th scope="row">
												<label for="preview">Anteprima</label>
											</th>
											<td>
												<?php
													// Generate the preview, getting one post by tag_ID
													$posts = get_posts(array(
														'posts_per_page' => 1,
														'orderby' => 'rand',
														'tax_query' => array(
															array(
																'taxonomy' => 'post_tag',
																'field' => 'term_id',
																'terms' => $tag->term_id
															)
														)
													));
												?>
												<div class="cta-preview">
													<?php
														if($posts):
															$post = $posts[0];
															setup_postdata($post);
															?>
																<a target="_blank" href="<?= get_permalink($post->ID)?>?qzr-cta-preview=true"><?= get_the_title($post->ID)?></a>
															<?php
															wp_reset_postdata();
														else:
															?>
																<p>Non ci sono articoli con questo tag.</p>
															<?php
														endif;
													?>
											</td>
										</tr>
										
										<!-- Callback selector -->
										<tr>
											<th scope="row">
												<label for="cta-rule-callback-<?= $tag->term_id; ?>">Regola</label>
											</th>
											<td>
												<select id="cta-rule-callback-<?= $tag->term_id; ?>" name="cta-rule-callback-<?= $tag->term_id; ?>">
													<option disabled selected>Inserisci CTA <b>dopo</b> l'elemento</option>
												</select>
											</td>
										</tr>

										<!-- The HTML Element to find, "p", "h1", "h2"... or by class ".classname" -->
										<tr>
											<th scope="row">
												<label for="cta-rule-element-<?= $tag->term_id; ?>">Elemento</label>
												
											</th>
											<td>
												<input type="text" name="cta-rule-element-<?= $tag->term_id; ?>" id="cta-rule-element-<?= $tag->term_id; ?>" value="<?= isset($cta['rule']['options']['element'])?  $cta['rule']['options']['element'] : 'p' ?>">
												<p class="description">Tipo <i>p, h1, h2...</i>, ma anche <i>.qzr, .qualsiasi-classe-dichiarata</i></p>
											</td>
										</tr>

										<!-- After :nth element, insert the CTA. Default is 4 -->
										<tr>
											<th scope="row">
												<label for="cta-rule-<?= $tag->term_id; ?>">Numero di elementi</label>
											</th>
											<td>
												<input type="number" name="cta-rule-nth-<?= $tag->term_id; ?>" id="cta-rule-nth<?= $tag->term_id; ?>" value="<?= isset($cta['rule']['options']['nth'])?  $cta['rule']['options']['nth'] : 4 ?>">
											</td>
										</tr>
									</tbody>
								</table>

							</div>
							
							<?php
							// This is the HTML editor, using TinyMCE included in WP
							$settings = array(
								'textarea_name' => 'cta[' . $tag->term_id . ']',
								'textarea_rows' => 10,
								'tinymce' => array(
									'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,unlink,|,undo,redo',
									'toolbar2' => '',
								),
							);

							wp_editor(
								stripslashes($cta['html']), // Need to strip slashes, otherwise the editor will show the slashes
								'cta-editor-' . $tag->term_id,
								$settings
							);

							?>
						</div>
						<?php
					endforeach;
				?>
				<input type="submit" value="Salva" class="button button-primary">
			</form>
		<?php else: ?>
			<p>Nessun tag dichiarato.</p>
			<p><a href="edit-tags.php?taxonomy=post_tag">Perché non generarne qualcuno?</a></p>
		<?php endif;
}