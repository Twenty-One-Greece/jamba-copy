			<tr>
				<th scope="row"><?php _e('Menus:', 'fgj2wpp'); ?></th>
				<td>
					<input id="create_submenus" name="create_submenus" type="checkbox" value="1" <?php checked($data['create_submenus'], 1); ?> /> <label for="create_submenus" ><?php _e('Create submenus with subcategories for categories menus', 'fgj2wpp'); ?></label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e('SEO:', 'fgj2wpp'); ?></th>
				<td>
					<input id="import_meta_seo" name="import_meta_seo" type="checkbox" value="1" <?php checked($data['import_meta_seo'], 1); ?> /> <label for="import_meta_seo" ><?php _e('Import the meta description and the meta keywords to WordPress SEO by Yoast', 'fgj2wpp'); ?></label>
					<br />
					<input id="get_metadata_from_menu" name="get_metadata_from_menu" type="checkbox" value="1" <?php checked($data['get_metadata_from_menu'], 1); ?> /> <label for="get_metadata_from_menu" ><?php _e('Set the meta data from menus instead of articles', 'fgj2wpp'); ?></label>
					<br />
					<input id="get_slug_from_menu" name="get_slug_from_menu" type="checkbox" value="1" <?php checked($data['get_slug_from_menu'], 1); ?> /> <label for="get_slug_from_menu" ><?php _e('Set the post slugs from menus instead of aliases', 'fgj2wpp'); ?></label>
					<br />
					<input id="keep_joomla_id" name="keep_joomla_id" type="checkbox" value="1" <?php checked($data['keep_joomla_id'], 1); ?> /> <label for="keep_joomla_id" ><?php _e('Keep the Joomla articles IDs', 'fgj2wpp'); ?></label>
					<br />
					<input id="url_redirect" name="url_redirect" type="checkbox" value="1" <?php checked($data['url_redirect'], 1); ?> /> <label for="url_redirect" ><?php _e("Redirect the Joomla URLs", 'fgj2wpp'); ?></label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e('Partial import:', 'fgj2wpp'); ?></th>
				<td>
					<div id="partial_import_toggle"><?php _e('expand / collapse', 'fgj2wpp'); ?></div>
					<div id="partial_import">
					<input id="skip_categories" name="skip_categories" type="checkbox" value="1" <?php checked($data['skip_categories'], 1); ?> /> <label for="skip_categories" ><?php _e('Don\'t import the categories', 'fgj2wpp'); ?></label>
					<br />
					<input id="skip_articles" name="skip_articles" type="checkbox" value="1" <?php checked($data['skip_articles'], 1); ?> /> <label for="skip_articles" ><?php _e('Don\'t import the articles', 'fgj2wpp'); ?></label>
					<br />
					<input id="skip_weblinks" name="skip_weblinks" type="checkbox" value="1" <?php checked($data['skip_weblinks'], 1); ?> /> <label for="skip_weblinks" ><?php _e('Don\'t import the web links', 'fgj2wpp'); ?></label>
					<br />
					<input id="skip_users" name="skip_users" type="checkbox" value="1" <?php checked($data['skip_users'], 1); ?> /> <label for="skip_users" ><?php _e('Don\'t import the users', 'fgj2wpp'); ?></label>
					<br />
					<input id="skip_menus" name="skip_menus" type="checkbox" value="1" <?php checked($data['skip_menus'], 1); ?> /> <label for="skip_menus" ><?php _e('Don\'t import the menus', 'fgj2wpp'); ?></label>
					<br />
					<input id="skip_modules" name="skip_modules" type="checkbox" value="1" <?php checked($data['skip_modules'], 1); ?> /> <label for="skip_modules" ><?php _e('Don\'t import the modules', 'fgj2wpp'); ?></label>
					<?php do_action('fgj2wp_post_display_partial_import_options', $data); ?>
					</div>
				</td>
			</tr>
