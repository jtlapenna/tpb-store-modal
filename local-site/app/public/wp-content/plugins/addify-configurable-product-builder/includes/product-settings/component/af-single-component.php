<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="af_composite_product_title_div af_composite_product_title_div_<?php echo esc_attr($key); ?>"  data-default_product="<?php echo esc_attr($af_composite_product_default_product); ?>" data-key="<?php echo esc_attr($key); ?>">

					<span><?php echo esc_html__( $af_comp_product_component_name , 'af_comp_product' ); ?></span>

					<input type="button" class="button af_comp_product_remove_component" data-key="<?php echo esc_attr($key); ?>" value="<?php echo esc_html__( 'Remove Component' , 'af_comp_product' ); ?>">

				</div>


				<div class="af_composite_product_desc_div af_composite_product_desc_div_<?php echo esc_attr($key); ?>">

					<div class="af_cp_desc_div_toggle_btn">

						<span class="af_cp_comp_basic af_cp_comp_basic_btn_<?php echo esc_attr($key); ?> af_cp_comp_btn_active" data-key="<?php echo esc_attr($key); ?>" ><?php echo esc_html__('Basic settings' , 'af_comp_product'); ?></span>
						<span class="af_cp_comp_setting af_cp_comp_setting_btn_<?php echo esc_attr($key); ?> " data-key="<?php echo esc_attr($key); ?>" ><?php echo esc_html__('Advanced Settings' , 'af_comp_product'); ?></span>

					</div>


					<table class="af-comp-product-tbl af_cp_component_detail af_cp_comp_basic_<?php echo esc_attr($key); ?>">

						<tr>
							<td>
								<?php echo esc_html__('Name' , 'af_comp_product'); ?>
							</td>
							<td  class="af_comp_product_grid_view">
								<input type="text" class="af_component_title_input af-comp-product-input" name="af_comp_product_component_name[<?php echo esc_attr($key); ?>]" data-key="<?php echo esc_attr($key); ?>" placeholder="Enter name of component" value="<?php echo esc_html__( $af_comp_product_component_name , 'af_comp_product' ); ?>">
								<p class="description"><?php echo esc_html__( 'Enter name of the component.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo esc_html__('Description' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<textarea name="af_comp_product_component_desc[<?php echo esc_attr($key); ?>]" class="af-comp-product-input" cols="30" rows="10"><?php echo esc_html__( $af_comp_product_component_desc , 'af_comp_product' ); ?></textarea>
								<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Enter description of the component.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>
						
						<tr>
							<td>
								<?php echo esc_html__('Component Image' , 'af_comp_product'); ?>
							</td>
							<td  class="af_comp_product_grid_view">
								<input type="button" value="<?php echo esc_html__( 'Upload Image' , 'af_comp_product' ); ?>" style="width: 100px;" data-text="<?php echo esc_html__( 'Insert Image' , 'af_comp_product' ); ?>" data-image=".af_cp_comp_image_<?php echo esc_attr($key); ?>" data-attachment_id=".af_cp_comp_attachment_id_<?php echo esc_attr($key); ?>" data-id="<?php echo esc_attr($key); ?>" class="button-primary upload_image_for_component" id="upload_image_for_component-<?php echo esc_attr($key); ?>"/>
								<input type="hidden" name="af_comp_product_component_image[<?php echo esc_attr($key); ?>]" class="af_cp_comp_attachment_id_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($af_comp_product_component_image); ?>" /> </br>
								<img class="af_cp_component_img af_cp_comp_image_<?php echo esc_attr($key); ?>"
									<?php

									if ( '' != $af_comp_product_component_image ) {
										?>
										src="<?php echo esc_url( current(wp_get_attachment_image_src( (int) $af_comp_product_component_image ) ) ); ?>" width="200" height="200" 
										<?php
									} else {

										?>
										src="" width="0" height="0" 
										<?php
									}

									?>
									alt=""/>
									<br>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( "Select an image for component. Image will show when the selected product don't have image." , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php 
								echo esc_html__('Choose products' , 'af_comp_product'); 
								?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_all_products[<?php echo esc_attr($key); ?>][]" data-key="<?php echo esc_attr($key); ?>" data-default_product="<?php echo esc_attr($af_composite_product_default_product); ?>"  class="af_comp_product_default_product af_comp_product_default_all_products_<?php echo esc_attr($key); ?> af_composite_product_live_search" placeholder="<?php echo esc_html__( 'Choose products' , 'af_comp_product' ); ?>" multiple style="width:60%;">
									<?php

									foreach ($af_composite_product_all_products as $prod_key => $value) {

										if ( 0 == $value ) {
											continue;
										}

										if ( wc_get_product($value) ) {
											?>
												<option value="<?php echo esc_attr($value); ?>" selected><?php echo esc_html__( get_the_title($value) , 'af_comp_product' ); ?></option>
											<?php
										}
									}

									?>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Choose products for this component.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo esc_html__('Choose categories' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_categories[<?php echo esc_attr($key); ?>][]" data-default_product="<?php echo esc_attr($af_composite_product_default_product); ?>" data-key="<?php echo esc_attr($key); ?>" class="af_comp_product_default_product af_comp_product_default_all_cats_<?php echo esc_attr($key); ?> af_composite_product_select_2" data-placeholder="<?php echo esc_html__( 'Choose product categories' , 'af_comp_product' ); ?>" multiple style="width:60%;">
									<?php
									$args = array(
										'taxonomy'   => 'product_cat',
										'hide_empty' => false,
									);

									$product_cat = get_terms( $args );

									foreach ($product_cat as $parent_product_cat) {

										$cat_name     = $parent_product_cat->name;
										$af_cp_cat_id = $parent_product_cat->term_id;

										?>
										<option value="<?php echo esc_attr($af_cp_cat_id); ?>" 
																	<?php 
																	if ( in_array( $af_cp_cat_id , $af_composite_product_categories ) ) {
																		echo 'selected="selected"'; } 
																	?>
										><?php echo esc_html__($cat_name , 'af_comp_product'); ?></option>
										<?php

									}
									?>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Choose product categories for this component.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo esc_html__('Choose tags' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_tags[<?php echo esc_attr($key); ?>][]" data-default_product="<?php echo esc_attr($af_composite_product_default_product); ?>" data-key="<?php echo esc_attr($key); ?>" class="af_comp_product_default_product af_comp_product_default_all_tags_<?php echo esc_attr($key); ?> af_composite_product_select_2" data-placeholder="<?php echo esc_html__( 'Choose product tags' , 'af_comp_product' ); ?>" multiple style="width:60%;">
									<?php

									$args = array(
										'hide_empty' => false,
										'taxonomy'   => 'product_tag',
									);

									$product_tags = get_terms( $args );

									foreach ($product_tags as $parent_tag) {

										$tag_name = $parent_tag->name;
										$tag_id   = $parent_tag->term_id;

										?>
										<option value="<?php echo esc_attr($tag_id); ?>" 
																	<?php 
																	if ( in_array( $tag_id , $af_composite_product_tags ) ) {
																		echo 'selected="selected"'; } 
																	?>
										><?php echo esc_html__($tag_name , 'af_comp_product'); ?></option>
										<?php

									}
									?>
								</select>
								<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Choose product tags for this component.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo esc_html__('Default product' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view" data-key="<?php echo esc_attr($key); ?>">
								<select name="af_composite_product_default_product[<?php echo esc_attr($key); ?>]" data-default_product="<?php echo esc_attr($af_composite_product_default_product); ?>"  data-key="<?php echo esc_attr($key); ?>"  class="af_composite_product_select_2 af_cp_comp_def_product af_comp_pro_default_product<?php echo esc_attr($key); ?>  af-comp-product-input" placeholder="<?php echo esc_html__( 'Choose default selected product' , 'af_comp_product' ); ?>" style="width:60%;" >
									<?php

									if ( !empty($af_composite_product_default_product) ) {

										?>
										<option value="<?php echo esc_attr($af_composite_product_default_product); ?>"><?php echo esc_html__( get_the_title($af_composite_product_default_product) , 'af_comp_product' ); ?></option>
										<?php

									} else {

										?>
										<option value=""><?php echo esc_html__( 'Select products/categories/tags first' , 'af_comp_product' ); ?></option>
										<?php

									}
									?>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Choose a default selected product from above selected products, categories and tags.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo esc_html__('Component quantity type' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_quantity_op[<?php echo esc_attr($key); ?>]" id="af_comp_product_qty_setting_<?php echo esc_attr($key); ?>" class="af_comp_product_select_change af-comp-product-input">
									<option value="fixed" <?php selected( 'fixed' , $af_composite_product_quantity_op , true ); ?>><?php echo esc_html__( 'Fixed quantity' , 'af_comp_product' ); ?></option>
									<option value="range" <?php selected( 'range' , $af_composite_product_quantity_op , true ); ?>><?php echo esc_html__( 'Range Min/Max' , 'af_comp_product' ); ?></option>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Select option for quantity of component product.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>
						<tr class=" af_comp_product_qty_setting_<?php echo esc_attr($key); ?> af_comp_product_qty_setting_<?php echo esc_attr($key); ?>_fixed 
						<?php 

						if ( 'range' == $af_composite_product_quantity_op ) {
							echo ' af_comp_product_hidden '; 
						} 

						?>
						" >
							<td>
								<?php echo esc_html__('Quantity' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<input type="number"  class="af-comp-product-input" name="af_composite_product_fixed_qty[<?php echo esc_attr($key); ?>]" min="1" value="<?php echo esc_attr( $af_composite_product_fixed_qty ); ?>">
								<p class="description"><?php echo esc_html__( 'Enter quantity of component product' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr class=" af_comp_product_qty_setting_<?php echo esc_attr($key); ?> af_comp_product_qty_setting_<?php echo esc_attr($key); ?>_range  
						<?php 

						if ( 'range' != $af_composite_product_quantity_op ) {
							echo ' af_comp_product_hidden '; 
						} 

						?>
						">
							<td>
								<?php echo esc_html__('Minimum quantity ' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<input type="number"  class="af-comp-product-input" name="af_composite_product_min_qty[<?php echo esc_attr($key); ?>]" required min="1" value="<?php echo esc_attr( $af_composite_product_min_qty ); ?>">
								<p class="description"><?php echo esc_html__( 'Enter minimum quantity of component product.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr class=" af_comp_product_qty_setting_<?php echo esc_attr($key); ?> af_comp_product_qty_setting_<?php echo esc_attr($key); ?>_range  
						<?php 

						if ( 'range' != $af_composite_product_quantity_op ) {
							echo ' af_comp_product_hidden '; 
						} 
						?>
						">
							<td>
								<?php echo esc_html__('Maximum quantity ' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<input type="number"  class="af-comp-product-input" name="af_composite_product_max_qty[<?php echo esc_attr($key); ?>]" min="1" value="<?php echo esc_attr( $af_composite_product_max_qty ); ?>">
								<p class="description"><?php echo esc_html__( 'Enter maximum quantity of component product.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr class="">
							<td>
								<?php echo esc_html__('Adjustment Type ' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_cp_comp_adj_type[<?php echo esc_attr($key); ?>]" class="af-comp-product-input">
									<option value="fixed_increase" <?php selected( $af_cp_comp_adj_type , 'fixed_increase' ); ?>><?php echo esc_html__('Fixed increase ' , 'af_comp_product'); ?></option>
									<option value="fixed_decrease" <?php selected( $af_cp_comp_adj_type , 'fixed_decrease' ); ?>><?php echo esc_html__('Fixed decrease ' , 'af_comp_product'); ?></option>
									<option value="same_price" <?php selected( $af_cp_comp_adj_type , 'same_price' ); ?>><?php echo esc_html__('Same Price' , 'af_comp_product'); ?></option>
									<option value="percentage_increase" <?php selected( $af_cp_comp_adj_type , 'percentage_increase' ); ?>><?php echo esc_html__('Percentage increase ' , 'af_comp_product'); ?></option>
									<option value="percentage_decrease" <?php selected( $af_cp_comp_adj_type , 'percentage_decrease' ); ?>><?php echo esc_html__('Percentage decrease ' , 'af_comp_product'); ?></option>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Select adjustment type.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr class="">
							<td>
								<?php echo esc_html__('Adjustment Value' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<input type="number" name="af_cp_comp_adj_price_value[<?php echo esc_attr($key); ?>]" min="0" value="<?php echo esc_attr($af_cp_comp_adj_price_value); ?>" class="af-comp-product-input" >
								<p class="description"><?php echo esc_html__( 'Enter adjustment value.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

					</table>


					<table class="af-comp-product-tbl af_cp_comp_advance_settings af_cp_comp_setting_<?php echo esc_attr($key); ?>">

						<tr>
							<td>
								<?php echo esc_html__('Product listing style ' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_component_style[<?php echo esc_attr($key); ?>]"  class="af-comp-product-input">
									<option value="simple"  <?php selected( 'simple' , $af_composite_product_component_style , true ); ?>><?php echo esc_html__('Simple dropdown' , 'af_comp_product'); ?></option>
									<option value="image_product"  <?php selected( 'image_product' , $af_composite_product_component_style , true ); ?>><?php echo esc_html__('Dropdown with product image' , 'af_comp_product'); ?></option>
									<option value="radio"  <?php selected( 'radio' , $af_composite_product_component_style , true ); ?>><?php echo esc_html__('Radio buttons' , 'af_comp_product'); ?></option>
									<option value="thumbnail"  <?php selected( 'thumbnail' , $af_composite_product_component_style , true ); ?>><?php echo esc_html__('Thumbnail' , 'af_comp_product'); ?></option>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Select a style for component product.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo esc_html__('Product order' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_order[<?php echo esc_attr($key); ?>]"  class="af-comp-product-input">
									<option value="price" <?php selected( 'price' , $af_composite_product_order , true ); ?>><?php echo esc_html__('Price' , 'af_comp_product'); ?></option>
									<option value="name"  <?php selected( 'name' , $af_composite_product_order , true ); ?>><?php echo esc_html__('Name' , 'af_comp_product'); ?></option>
									<option value="date"  <?php selected( 'date' , $af_composite_product_order , true ); ?>><?php echo esc_html__('Date' , 'af_comp_product'); ?></option>
									<option value="rating"  <?php selected( 'rating' , $af_composite_product_order , true ); ?>><?php echo esc_html__('Rating' , 'af_comp_product'); ?></option>
									<option value="popularity"  <?php selected( 'popularity' , $af_composite_product_order , true ); ?>><?php echo esc_html__('Popularity' , 'af_comp_product'); ?></option>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Set product listing by price, name, date, rating or popularity.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo esc_html__('Order type' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_type[<?php echo esc_attr($key); ?>]"  class="af-comp-product-input">
									<option value="asc"  <?php selected( 'asc' , $af_composite_product_type , true ); ?>><?php echo esc_html__('Ascending' , 'af_comp_product'); ?></option>
									<option value="desc"  <?php selected( 'desc' , $af_composite_product_type , true ); ?>><?php echo esc_html__('Descending' , 'af_comp_product'); ?></option>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Set product listing by price, name, date, rating or popularity.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo esc_html__('Product price style ' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_price_style[<?php echo esc_attr($key); ?>]"  class="af-comp-product-input">
									<option value="hide"  <?php selected( 'hide' , $af_composite_product_price_style , true ); ?>><?php echo esc_html__('Hide price' , 'af_comp_product'); ?></option>
									<option value="active"  <?php selected( 'active' , $af_composite_product_price_style , true ); ?>><?php echo esc_html__('Active price' , 'af_comp_product'); ?></option>
									<option value="full_price"  <?php selected( 'full_price' , $af_composite_product_price_style , true ); ?>><?php echo esc_html__('Regular price + Sale price' , 'af_comp_product'); ?></option>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Select a style for component product price in dropdown.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr>
							<td>
								<?php echo esc_html__('Stock availability ' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<select name="af_composite_product_stock_cb[<?php echo esc_attr($key); ?>]" class="af-comp-product-input" >
									<option value="hide" <?php selected( 'hide' , $af_composite_product_stock_cb , true ); ?>><?php echo esc_html__( 'Hide stock message in dropdown' , 'af_comp_product' ); ?></option>
									<option value="message" <?php selected( 'message' , $af_composite_product_stock_cb , true ); ?>><?php echo esc_html__( 'Show stock message in drop down' , 'af_comp_product' ); ?></option>
								</select>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Select a option to show product stock message to user in dropdown.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr class="af_cp_comp_border_top">
							<td>
								<?php echo esc_html__('Selection detail visibility' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<span>
									<input type="checkbox" name="af_cp_selection_title_cb[<?php echo esc_attr($key); ?>]" <?php checked( 'yes' , $af_cp_selection_title_cb , true ); ?> value="yes">
									&nbsp;<strong><?php echo esc_html__('Title' , 'af_comp_product'); ?></strong>
								</span>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Check if you want to show title of selected component product to user.' , 'af_comp_product' ); ?></p>
								<br>
								<span>
									<input type="checkbox" name="af_cp_selection_desc_cb[<?php echo esc_attr($key); ?>]" <?php checked( 'yes' , $af_cp_selection_desc_cb , true ); ?> value="yes">
									&nbsp;<strong><?php echo esc_html__('Description' , 'af_comp_product'); ?></strong>
								</span>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Check if you want to show description of selected component product to user.' , 'af_comp_product' ); ?></p>
								<br>
								<span>
									<input type="checkbox" name="af_cp_selection_thumbnail_cb[<?php echo esc_attr($key); ?>]" <?php checked( 'yes' , $af_cp_selection_thumbnail_cb , true ); ?> value="yes">
									&nbsp;<strong><?php echo esc_html__('Thumbnail' , 'af_comp_product'); ?></strong>
								</span>
								<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Check if you want to show thumbnail of selected component product to user.' , 'af_comp_product' ); ?></p>
								<br>
								<span>
									<input type="checkbox" name="af_cp_selection_price_cb[<?php echo esc_attr($key); ?>]" <?php checked( 'yes' , $af_cp_selection_price_cb , true ); ?> value="yes">
								&nbsp;<strong><?php echo esc_html__('Price' , 'af_comp_product'); ?></strong>
								</span>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Check if you want to show price of selected component product to user.' , 'af_comp_product' ); ?></p>
								<br>
								<span>
									<input type="checkbox" name="af_cp_selection_stock_cb[<?php echo esc_attr($key); ?>]" <?php checked( 'yes' , $af_cp_selection_stock_cb , true ); ?> value="yes">
								&nbsp;<strong><?php echo esc_html__('Stock' , 'af_comp_product'); ?></strong>
								</span>
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Check if you want to show stock of selected component product to user.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr class="af_cp_comp_border_top">
							<td>
								<?php echo esc_html__('Allow sorting' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<input type="checkbox" name="af_composite_product_sorting_cb[<?php echo esc_attr($key); ?>]" <?php checked( 'yes' , $af_composite_product_sorting_cb , true ); ?> value="yes">
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Check if you want to allow user to sort the products by price / rating / newest and popularity.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr class="af_cp_comp_border_top">
							<td>
								<?php echo esc_html__('Required component' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<input type="checkbox" name="af_composite_product_required_component[<?php echo esc_attr($key); ?>]" <?php checked( 'yes' , $af_composite_product_required_component , true ); ?> value="yes">
								<p class="description" style="margin-left:0px;" ><?php echo esc_html__( 'Check if you want to make this component required for user to buy in configurable product.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

						<tr class="af_cp_comp_border_top">
							<td>
								<?php echo esc_html__('Exclusive product ' , 'af_comp_product'); ?>
							</td>
							<td class="af_comp_product_grid_view">
								<input type="checkbox" name="af_composite_product_exclusive_cb[<?php echo esc_attr($key); ?>]"  <?php checked( 'yes' , $af_composite_product_exclusive_cb , true ); ?>  value="yes">
								<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Check if you want to make this component exclusive,' , 'af_comp_product' ); ?></p>
								<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'If checked, the product select in this component will not be available for selection in any other component.' , 'af_comp_product' ); ?></p>
							</td>
						</tr>

					</table>

				</div>
