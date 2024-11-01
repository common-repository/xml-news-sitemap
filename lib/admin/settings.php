<?php

$settings = $this->getSettings();

?>
<style type="text/css">
	#wrap { width: 700px; }
	#wrap code { margin: 0; padding: 0; }
	#gns_xml_admin_form #gns_xml_admin_table { width: 700px; }
	#gns_xml_admin_form #gns_xml_admin_table td { display: block; margin: 5px 10px; padding: 20px; border: 1px solid #ddd; background-color: #f8f8f8; }
	#gns_xml_admin_form #gns_xml_admin_table small td { display: table-cell; vertical-align: top; }
	#gns_xml_admin_form #gns_xml_admin_table h3 { margin: 0; }
	#gns_xml_admin_form #gns_xml_admin_table ul {list-style: none;}
	#gns_xml_admin_form #gns_xml_admin_table ul.children { padding-left: 15px; }
	#gns_xml_admin_form #gns_xml_admin_table ul li{ padding: 1px 0; margin: 0; list-style: none; }
</style>
<div id="wrap" class="wrap">
	<h2><?php _e('XML News Sitemap Settings'); ?></h2>
	<?php if (isset($_GET['updated']) == '1') { ?>
	<div id="gns_xml_success_message"><p style="font-weight:bold;font-size:110%;color:green;"><?php _e('Options Updated Successfully'); ?></p></div>
	<?php } ?>

	<form id="gns_xml_admin_form" method="post" action="">
		<table id="gns_xml_admin_table" class="form-table">
			<tbody>
				<tr>
					<td>

						<h3><label for="gns_xml_pubname"><?php _e('Publication Name'); ?></label></h3>
						<input type="text" class="widefat" name="gns_xml_pubname" value="<?php if ($settings['gns_xml_pubname']) { echo esc_html($settings['gns_xml_pubname']); } ?>" />
						<p><small>Enter the publication name to appear in the Google News Sitemap. This will appear in the &lt;news:name&gt;&lt;/news:name&gt; element of the sitemap.</small></p>
						<h3><label for="gns_xml_publanguage"><?php _e('Publication Language'); ?></label></h3>
						<input type="text" class="widefat" name="gns_xml_publanguage" value="<?php if ($settings['gns_xml_publanguage']) { echo esc_html($settings['gns_xml_publanguage']); } ?>" />
						<p><small>Enter the publication language to appear in the Google News Sitemap. This will appear in the &lt;news:language&gt;&lt;/news:language&gt; element of the sitemap. It should be an <a href="http://www.loc.gov/standards/iso639-2/php/code_list.php">ISO 639 Language Code</a> (either 2 or 3 letters)</small></p>
						<h3><label for="gns_xml_pubaccess"><?php _e('Access'); ?></label></h3>
						<select class="index-list widefat" name="gns_xml_pubaccess">
								<option value="na" <?php if (isset($settings['gns_xml_pubaccess']) && $settings['gns_xml_pubaccess'] == "na") { echo 'selected="selected"'; } ?>><?php _e('Not Applicable'); ?></option>
								<option value="Subscription" <?php if (isset($settings['gns_xml_pubaccess']) && $settings['gns_xml_pubaccess'] == "Subscription") { echo 'selected="selected"'; } ?>><?php _e('Subscription'); ?></option>
								<option value="Registration" <?php if (isset($settings['gns_xml_pubaccess']) && $settings['gns_xml_pubaccess'] == "Registration") { echo 'selected="selected"'; } ?>><?php _e('Registration'); ?></option>
							</select>
						<p><small>If the article is accessible to Google News readers without a registration or subscription, leave as <?php _e('Not Applicable'); ?></small></p>
						<h3><label for="gns_xml_genres"><?php _e('Genres'); ?></label></h3>
						<?php
							if(!empty($settings['gns_xml_genres'])){
								$gns_xml_genres = unserialize($settings['gns_xml_genres']);
							}
						?>
						<input type="text" class="widefat" name="gns_xml_genres" value="<?php if ($gns_xml_genres) { echo esc_html($gns_xml_genres); } ?>" />

						<p><small>A comma separated list of properties characterising the content of the article, such as "blog" or "satire". See <a href="https://support.google.com/news/publisher/answer/4582731" target="_blank">Google News content properties</a> for a list of possible values. Your content must be labelled accurately in order to provide a consistent experience for our users. <b>Note:</b> leave this blank if unsure of the best label to use and Google will add a label algorithmically.</small></p>
					</td>
					<td>
						<h3><label for="gns_xml_url"><?php _e('Sitemap URL'); ?></label></h3>
						<input type="text" class="widefat" name="gns_xml_url" value="<?php if ($settings['gns_xml_url']) { echo esc_url($settings['gns_xml_url']); } ?>" />
						<p><small>Enter a URL from which to serve the Google News Sitemap E.g. http://example.com/sitemap-news.xml</small></p>
					</td>
					<td>
						<h3><label for="gns_xml_custom_post_types[]"><?php _e('Post Types'); ?></label></h3>
						<p>
							<select class="index-list widefat" name="gns_xml_custom_post_types[]" multiple="multiple" size="4">
								<?php
									$args = array ( 'public' => true, '_builtin' => false );
									$output = 'names'; // names or objects, note names is the default
									$operator = 'or'; // 'and' or 'or'
									$custom_post_types = get_post_types( $args, $output, $operator );
									foreach($custom_post_types as $custom_post_type){
										if(isset($settings['gns_xml_custom_post_types'])){


										if (in_array($custom_post_type, $settings['gns_xml_custom_post_types'])) {
											$selected = 'selected="selected"';
										} else {
											$selected = "";
										}
										}
										echo '<option value="'.esc_html($custom_post_type).'" '.$selected.'>'.esc_html($custom_post_type).'</option>';
									}
								?>
							</select>
						</p>
						<p><small>Select the post types that should be allowed on the Google News Sitemap.</small></p>

					</td>

					<td>
					<h3><label for="gns_xml_url"><?php _e('Auto Promote Posts in Category:'); ?></label></h3>
					<ul>
					<?php
					if(isset($settings['gns_xml_cats'])){
						wp_category_checklist(0,0,$settings['gns_xml_cats'],false,NULL,false);
					} else {
						wp_category_checklist(0,0,false,false,NULL,false);
					}
					?>
					</ul>
					</td>

				</tr>
				<tr>
					<td>
						<h3 style="color: red;"><span>Share the Love! &hearts;&hearts; &#128515; &hearts;&hearts;</span></h3>
						<p>If you like this plugin and it helps you then please help me by linking to my site using the following snippet:</p>
						<hr />
						<p><b>Text version:</b><br /><code>XML News Sitemap created by <a href="https://www.jnorton.co.uk">Web Developer</a>: Justin Norton - https://www.jnorton.co.uk</code></p>
						<hr />
						<p><b>HTML version:</b><br />
						<code>XML News Sitemap created by &lt;a href=&quot;https://www.jnorton.co.uk&quot;&gt;Web Developer&lt;/a&gt;: Justin Norton - https://www.jnorton.co.uk</code></p>
<hr />
						<p>If you want to write a review about this plugin then please do as it all helps improve this project.</p>
					</td>
				</tr>

			</tbody>
		</table>
		<p class="submit">
			<?php wp_nonce_field('gns_xml_settings'); ?>
			<input type="submit" name="gns_xml_settings" value="<?php _e('Save Settings'); ?>" />
		</p>
	</form>
</div>