<?php
defined( 'ABSPATH' ) || exit;
$af_cp_scenario_condition_comp   = (array) get_post_meta( $product_id , 'af_cp_scenario_condition_comp' , true );
$af_cp_scenario_action_comp      = (array) get_post_meta( $product_id , 'af_cp_scenario_action_comp' , true );
$component_name                  = (array) get_post_meta( $product_id , 'af_comp_product_component_name' , true );
$action_array                    = (array) get_post_meta( $product_id , 'af_cp_scenario_action_type' , true );
$af_comp_product_sc_hide_comp_cb = (array) get_post_meta( $product_id , 'af_comp_product_sc_hide_comp_cb' , true );

$af_comp_product_sc_hide_options_cb = (array) get_post_meta( $product_id , 'af_comp_product_sc_hide_options_cb' , true );
$af_comp_product_scenario_desc      = (array) get_post_meta( $product_id , 'af_comp_product_scenario_desc' , true );
$enable_scenario_cb                 = (array) get_post_meta( $product_id , 'af_cp_enable_scenario_cb' , true );
$enable_scenario_cb_keys            = (array) array_keys($enable_scenario_cb);
$af_cp_enable_scenario_cb           = '';

if ( in_array( $key , $enable_scenario_cb_keys ) ) {
	$af_cp_enable_scenario_cb = $enable_scenario_cb[ $key ];
}

?>
<div class="af_cp_scenario_div af_cp_scenario_name_<?php echo esc_attr($key); ?>" data-desc_div=".af_cp_scenario_desc_div_<?php echo esc_attr($key); ?>"  data-id="<?php echo esc_attr($key); ?>">        

		<div class="af_cp_enable_sce_div 
		<?php 

		if ( 'yes' == $af_cp_enable_scenario_cb ) {
			echo '  af_cp_enable_sce_div_selected '; 
		} 
		?>
		">
			<div class="af_cp_enable_sce_span  
			<?php 

			if ( 'yes' == $af_cp_enable_scenario_cb ) {
				echo '  af_cp_enable_sce_span_selected '; 
			} 

			?>
			"></div>
			<input type="checkbox" style="display:none;" name="af_cp_enable_scenario_cb[<?php echo esc_attr($key); ?>]"  data-key="<?php echo esc_attr($key); ?>" <?php checked( $af_cp_enable_scenario_cb , 'yes' ); ?> value="yes" class="af_cp_enable_scenario_cb " >
		</div>

		<div class="title_div">
			<span><?php echo esc_html__( $af_comp_product_scenario_name , 'af_comp_product' ); ?></span>
		</div>

		<input type="button" class="button af_cp_scenario" data-desc_div="af_cp_scenario_desc_div_<?php echo esc_attr($key); ?>" data-key="<?php echo esc_attr($key); ?>" value="<?php echo esc_html__( 'Remove Scenario' , 'af_comp_product' ); ?>">

	</div>

	<div class="af_cp_scenario_desc_div af_cp_scenario_desc_div_<?php echo esc_attr($key); ?>">

		<table class="af-comp-product-tbl">
			<tr>
				<td>
					<?php echo esc_html__( 'Scenario name' , 'af_comp_product' ); ?>
				</td>
				<td>
					<input type="text" name="af_comp_product_scenario_name[<?php echo esc_attr($key); ?>]"  data-key="<?php echo esc_attr($key); ?>" value="<?php echo esc_html__( $af_comp_product_scenario_name , 'af_comp_product' ); ?>" class="af_comp_product_scenario_name af-comp-product-input" >
					<br><br>
					<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Add condition title for internal reference.' , 'af_comp_product' ); ?></p>
				</td>
			</tr>

			<tr>
				<td>
					<?php echo esc_html__('Description' , 'af_comp_product'); ?>
				</td>
				<td class="af_comp_product_grid_view">
					<textarea name="af_comp_product_scenario_desc[<?php echo esc_attr($key); ?>]" class="af-comp-product-input" cols="30" rows="10">
																			<?php 
																				$array_keys = (array) array_keys($af_comp_product_scenario_desc);
																			if ( in_array( $key , $array_keys ) ) {
																				echo esc_html__( $af_comp_product_scenario_desc[ $key ] , 'af_comp_product' ); 
																			}
																			?>
																				</textarea>
					<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Add condition description for internal reference.' , 'af_comp_product' ); ?></p>
				</td>
			</tr>

		</table>
		
	<div class="sf_cp_scenario_conditions">

		<div class="title">
			<?php echo esc_html__('Conditions' , 'af_comp_product'); ?>
		</div>

		<div class="conditions">

			<table id="af_cp_conditions_">
				<tr>
					<td>
						<?php echo esc_html__( 'Choose a component' , 'af_comp_product'); ?>
					</td>
					<td>
						<?php echo esc_html__( 'Choose condition' , 'af_comp_product'); ?>
					</td>
					<td>
						<?php echo esc_html__( 'Choose products' , 'af_comp_product'); ?>
					</td>
					<td></td>
				</tr>

				<?php

				$condition_count = 0;

				if ( array_key_exists( $key , $af_cp_scenario_condition_comp ) ) {

					foreach ( (array) $af_cp_scenario_condition_comp[ $key ] as $condition_key => $selected ) {
						$condition_count = $condition_key;
						$scenario_ajax   = '';
						include AFCPB_DIR_PATH . '/includes/product-settings/scenario/af-condition.php';
					}
				}
				?>
				<tr>
					<td>
						<select data-key="<?php echo esc_attr($key); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" data-count="<?php echo esc_attr( $condition_count+1 ); ?>" class="af_cp_sc_add_condition">
							<option value=""><?php echo esc_html__( 'Choose a Component' , 'af_comp_product'); ?></option>
								<?php

								foreach ( $component_name as $comp_key => $comp_value ) {

									if ( '' == $comp_value ) {
										$comp_value = '( No Name )';
									}

									?>
											<option value="<?php echo esc_attr($comp_key); ?>"><?php echo esc_html__( $comp_value , 'af_comp_product'); ?></option>
										<?php
								}
								?>
						</select>
					</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</div>

		<div class="af_cp_sc_decisions_div">

			<table class="af-comp-product-tbl">
				<tr>
					<td>
						<?php echo esc_html__( 'Hide components' , 'af_comp_product'); ?>
					</td>
					<td>
						<?php
							$array_keys              = (array) array_keys($af_comp_product_sc_hide_comp_cb);
							$af_cp_hide_comp_list_cb = '';
						if ( in_array( $key , $array_keys ) ) {
							$af_cp_hide_comp_list_cb = $af_comp_product_sc_hide_comp_cb[ $key ];
								
						}
						?>
						<input type="checkbox" name="af_comp_product_sc_hide_comp_cb[<?php echo esc_attr($key); ?>]" value="yes" 
						<?php checked( $af_cp_hide_comp_list_cb , 'yes' , true ); ?> 
						class="af_cp_scenario_hide_comp_cb" data-comp_list="af_cp_sce_hide_comp_list_<?php echo esc_attr($key); ?>"
						>
						<br>
						<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Check if you want to hide the whole component if the above conditions are met.' , 'af_comp_product'); ?></p>
					</td>
				</tr>

				<tr  class="
				<?php 
				echo esc_attr( 'af_cp_sce_hide_comp_list_' . $key . '   ');
				if ( 'yes' != $af_cp_hide_comp_list_cb ) {
					echo esc_attr( '  af_comp_product_hidden ' ); } 
				?>
					">
					<td>
						<?php echo esc_html__( 'Choose components' , 'af_comp_product'); ?>
					</td>
					<td>
						<select data-key="<?php echo esc_attr($key); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" name="af_cp_sc_hide_comps[<?php echo esc_attr($key); ?>][]" class="af_cp_sc_hide_comps af_cp_simple_select2" multiple style="width:100%;">
								<?php

								foreach ( $component_name as $comp_key => $comp_value ) {

									if ( '' == $comp_value ) {
										$comp_value = '( No Name )';
									}
									?>
											<option value="<?php echo esc_attr($comp_key); ?>" 
																		<?php 
																		if ( in_array( $comp_key , $af_cp_sc_hide_comps ) ) {
																			echo 'selected'; } 
																		?>
											><?php echo esc_html__( $comp_value , 'af_comp_product'); ?></option>
										<?php
								}
								?>
						</select>
						<br>
						<p class="description"><?php echo esc_html__( 'Choose Components you wants to hide if the user select above conditions.' , 'af_comp_product'); ?></p>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo esc_html__( 'Hide specific component options' , 'af_comp_product'); ?>
					</td>
					<td>
						<?php

						$array_keys                 = (array) array_keys($af_comp_product_sc_hide_options_cb);
						$af_cp_sce_hide_comp_option = '';

						if ( in_array( $key , $array_keys ) ) {

							$af_cp_sce_hide_comp_option = $af_comp_product_sc_hide_options_cb[ $key ];
						}
						?>
						<input type="checkbox" name="af_comp_product_sc_hide_options_cb[<?php echo esc_attr($key); ?>]" value="yes" 
						<?php checked( $af_cp_sce_hide_comp_option , 'yes' , true ); ?> class="af_cp_scenario_hide_comp_cb" data-comp_list="af_cp_sce_hide_table_options_<?php echo esc_attr($key); ?>"
						>
						<br>
						<p class="description" style="margin-left:0px;"><?php echo esc_html__( 'Check if you want to hide the specific options from component if the above conditions are met.' , 'af_comp_product'); ?></p>
					</td>
				</tr>
			</table>
			<table class="af_cp_sc_actions_tbl af_cp_sce_hide_table_options_<?php echo esc_attr($key); ?> <?php 

			if ( 'yes' != $af_cp_sce_hide_comp_option ) {

				echo '  af_comp_product_hidden  '; } 
			?>
			">
				<tr>
					<td>
						<?php echo esc_html__( 'Choose a component' , 'af_comp_product'); ?>
					</td>
					<td>
						<?php echo esc_html__( 'Choose action ' , 'af_comp_product'); ?>
					</td>
					<td>
						<?php echo esc_html__( 'Choose products' , 'af_comp_product'); ?>
					</td>
					<td></td>
				</tr>
				<?php

				$condition_key          = 0;
				$array_keys_for_actions = (array) array_keys($af_cp_scenario_action_comp);

				if ( in_array( $key , $array_keys_for_actions ) ) {

					foreach ( (array) $af_cp_scenario_action_comp[ $key ] as $condition_key => $selected ) {

						$scenario_ajax = '';
						include AFCPB_DIR_PATH . '/includes/product-settings/scenario/af-action.php';
					}
				}
				?>
				<tr>
					<td>
						<select data-key="<?php echo esc_attr($key); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" data-count="<?php echo esc_attr( $condition_key+1 ); ?>" class="af_cp_sc_add_action">
							<option value=""><?php echo esc_html__( 'Choose a Component' , 'af_comp_product'); ?></option>
								<?php

								foreach ( $component_name as $comp_key => $comp_value ) {

									if ( '' == $comp_value ) {
										$comp_value = '( No Name )';
									}
									?>
											<option value="<?php echo esc_attr($comp_key); ?>"><?php echo esc_html__( $comp_value , 'af_comp_product'); ?></option>
										<?php
								}
								?>
						</select>
					</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</div>
	</div>
</div>
