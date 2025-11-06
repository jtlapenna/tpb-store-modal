afcp_products_cat_tag_fn();
function afcp_products_cat_tag_fn(){
	"use strict";
	var ajaxurl = af_comp_product.admin_url;
	var nonce   = af_comp_product.nonce;

	jQuery( function($) {
			jQuery('.af_cp_simple_select2').select2();
			jQuery('.af_composite_product_select_2').select2();
		$(function () {
			// multi select
			$('.af_composite_product_live_search').select2({
				ajax: {
					url: ajaxurl, 
					dataType: 'json',
					delay: 250, 
					data: function (params) {
						return {
							q: params.term, 
							action: 'af_comp_product_live_search',
							nonce:nonce
						};
					},
					processResults: function( data ) {
						var options = [];
						if ( data ) {
							$.each( data, function( index, text ) { 
								options.push( { id: text[0], text: text[1]  } );
							});
						}
						return {
							results: options
						};
					},
					cache: true
				},
				placeholder: $(".af_composite_product_live_search").attr('placeholder'),
				minimumInputLength: 3 
			});        
		});
	});
}

jQuery(document).ready( function($){

	var ajaxurl = af_comp_product.admin_url;
	var nonce   = af_comp_product.nonce;


	function checkSelectValue(selectElement) {
        var selectedValue = jQuery(selectElement).val();
        var number = jQuery(selectElement).attr('name').match(/\d+/); // Extract the number from the name attribute

        // Construct the input field name based on the extracted number
        var inputFieldName = 'af_cp_comp_adj_price_value[' + number + ']';

        // Find the specific <tr> that contains the input field
        var row = jQuery('tr').has('input[name="' + inputFieldName + '"]');

        if (selectedValue === 'same_price') {
            row.hide(); // Hide the <tr> if 'same_price' is selected
        } else {
            row.show(); // Show the <tr> for other selections
        }
    }

    // Run on page load for all select elements with dynamic names
    jQuery('select[name^="af_cp_comp_adj_type["]').each(function() {
        checkSelectValue(this); // Check visibility for each select
    });

    // Run when any of the select values change
    jQuery('select[name^="af_cp_comp_adj_type["]').on('change', function() {
        checkSelectValue(this); // Check visibility on change
    });

	jQuery('#general_product_data .pricing').addClass('show_if_af_composite_product').show();

	jQuery('.inventory_options').addClass('show_if_af_composite_product').show();
	jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_af_composite_product').show();
	jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_af_composite_product').show();
	jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_af_composite_product').show();

	$(".af_comp_product_search_option_tr").closest('tr').hide();
	if ($('.af_comp_product_search_option').val() != 'show_all') {
		$(".af_comp_product_search_option_tr").closest('tr').show();
	}

	var af_cp_package_type = jQuery('.af_cp_package_type')[0].outerHTML;
	$('span.af_cp_component_desc').hide();
	$('span.af_cp_composite_desc').hide();
	jQuery('.af_cp_package_type').remove();
	var shipping_html = jQuery("#shipping_product_data").html();
	$("#shipping_product_data").html( af_cp_package_type + shipping_html );
	
	if ( 'composite' == $('select[name="af_cp_shipping_page_type"]').val() ) {
		$('span.af_cp_composite_desc').show();
	}
	if ( 'composite' != $('select[name="af_cp_shipping_page_type"]').val() ) {
		$('span.af_cp_component_desc').show();
		$('select[name="af_cp_shipping_page_type"]').closest('div#shipping_product_data').find("div").hide();
		$(".af_cp_package_type").show();
	}
	$(document).on('change' , 'select[name="af_cp_shipping_page_type"]' , function(){
		if ( 'composite' != $(this).val() ) {
			$(this).closest('div#shipping_product_data').find("div").hide();
			$(".af_cp_package_type").show();
			$('span.af_cp_component_desc').show();
			$('span.af_cp_composite_desc').hide();
		} else {
			$('span.af_cp_component_desc').hide();
			$('span.af_cp_composite_desc').show();
			$(this).closest('div#shipping_product_data').find("div").show();
		}
	});

	$(document).on('click' ,  "#product-type" , function(){
		if ( $(this).val() == 'af_composite_product' ) {
			jQuery('.product_data_tabs .general_options').show();
			jQuery('#general_product_data').show();
			$(".af_cp_shipping_page_type_field").show();
			if ( 'composite' == $('select[name="af_cp_shipping_page_type"]').val() ) {
				$('div#shipping_product_data').find("div").hide();
			} else {
				$('div#shipping_product_data').find("div").show();
			}
		} else {
			$('div#shipping_product_data').find("div").show();
			$(".af_cp_shipping_page_type_field").hide();
		}
	});

	$(document).on('change' , '.af_comp_product_search_option' , function(){
		if ($(this).val() == 'show_all') {
			$(".af_comp_product_search_option_tr").closest('tr').hide();
		} else {
			$(".af_comp_product_search_option_tr").closest('tr').show();
		}
	});

	$(document).on('change' , '.af_comp_product_select_change' , function(){
		$("." + $(this).attr('id') ).hide();
		$("." + $(this).attr('id') + '_' + $(this).val() ).show();
	});

	$(document).on('change , input' , '.af_component_title_input' , function(){
		$(".af_composite_product_title_div_" + $(this).data('key') + ' span' ).html($(this).val());
	});

	$(document).on('change , input' , '.af_comp_product_scenario_name' , function(){
		if ( '' == $(this).val() ) {
			$(this).val(' ');
		}
		$(".af_cp_scenario_name_" + $(this).data('key') + ' span' ).html($(this).val());
	});

	$(document).on('click' , '.af_cp_scenario_hide_comp_cb' , function(){
		if ( $(this).is(':checked') ) {
			$( '.' + $(this).data('comp_list') ).show();
		} else {
			$( '.' + $(this).data('comp_list') ).hide();
		}
	});

	$(document).on('click' , '.af_composite_product_title_div' , function(){


		if ( $(".af_composite_product_desc_div_" + $(this).data('key') ).is(":visible") ) {
			$(".af_composite_product_desc_div_" + $(this).data('key') ).hide(500);
		} else {
			$(".af_composite_product_desc_div_" + $(this).data('key') ).show(500);
		}
		var default_product = $(this).data('default_product');
		af_comp_pro_default_product_search( $(this).data('key') , default_product );
	});


	$(document).on('click' , '.af_cp_expand_all_sce' , function(){
		$('.af_cp_scenario_desc_div').show(500);
	});

	$(document).on('click' , '.af_cp_close_all_sce' , function(){
		$('.af_cp_scenario_desc_div').hide(500);
	});

	$(document).on('change' , '.af_cp_sc_add_action' , function(){
		var this_row       = this;
		var selected_value = $(this).val();
		var check          = true;
		$(this).closest('table').find('.af_cp_scenario_action_comp').each(function( index ) {
			if ( selected_value == $(this).val() ) {
				$(this).focus();
				check = false;
			}
		});
		if ( ('' != $(this_row).val()) && ( check ) ) {
			jQuery.ajax({
				url: ajaxurl, 
				type: 'POST',
				data: { 
					action : 'af_cp_add_scenario_actions_ajax'
					, size : $(this_row).data('count')
					, product_id : $(this_row).data('product_id')
					, key : $(this_row).data('key')
					, selected : $(this_row).val()
					, nonce:nonce
				},
				success: function( data ){
					jQuery(this_row).find('option[value=""]').prop('selected' , true);
					$(this_row).data('count' , data['size'] )
					$( data['tr_data'] ).insertBefore( $(this_row).closest('tr') );
					jQuery('.af_cp_simple_select2').select2();
				}
			});
		}
	});
	
	$(document).on('change' , '.af_cp_sc_add_condition' , function(){
		var this_row       = this;
		var selected_value = $(this).val();
		var check          = true;
		$(this).closest('table').find('.af_cp_scenario_condition_comp').each(function( index ) {
			if ( selected_value == $(this).val() ) {
				$(this).focus();
				check = false;
			}
		});
		if ( ('' != $(this).val()) && ( check ) ) {
			jQuery.ajax({
				url: ajaxurl, 
				type: 'POST',
				data: { 
					action : 'af_cp_add_scenario_condition_ajax'
					, size : $(this_row).data('count')
					, product_id : $(this_row).data('product_id')
					, key : $(this_row).data('key')
					, selected : $(this_row).val()
					, nonce:nonce
				},
				success: function( data ){
					$(this_row).find('option[value=""]').prop('selected' , true);
					$(this_row).data('count' , data['size'] )
					$( data['tr_data'] ).insertBefore( $(this_row).closest('tr') );
					jQuery('.af_cp_simple_select2').select2();
				}
			});
		}
	});

	$(document).on('change' , '.af_cp_sc_type_selection' , function(){
		if ( $(this).val() == 'any' ) {
			var Values = new Array();
			jQuery(this).closest('tr').find('.af_cp_sc_all_products').val(Values).trigger('change');
			$(this).closest('tr').find('.af_cp_sc_all_products').prop('disabled' , true);
		} else {
			$(this).closest('tr').find('.af_cp_sc_all_products').prop('disabled' , false);
		}
	});

	$(document).on('change' , '.af_cp_sc_component' , function(){
		var scenario_id  = $(this).data('sce_id');
		var condition_id = $(this).data('condition_id');
		var product_id   = $(this).data('product_id');
		var this_select  = this;
		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: {
				action : 'af_cp_add_component_products_ajax'
				, scenario_id : scenario_id
				, condition_id : condition_id
				, product_id : product_id
				, selected : $(this).val()
				, nonce:nonce
			},
			success: function( data ){
				jQuery('.' + jQuery(this_select).data('products')).html(data);
				jQuery('.' + jQuery(this_select).data('products')).select2();
			}
		});
	});

	$(document).on('click' , '.af_composite_product_add_new_component_btn' , function(){
		var size = $(this).data('size');
		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: { 
				action : 'af_composite_product_add_component_ajax'
				, array_size: size
				, nonce:nonce
			},
			success: function( data ){
				$(".af_composite_product_main_div").append(data['tr_data']);
				$('.af_composite_product_desc_div_' +size ).show();
				$(".af_composite_product_add_new_component_btn").data('size' , ++size );
				afcp_products_cat_tag_fn();
			}
		});
	});

	$(document).on('click' , '.af_cp_add_new_scenario_btn' , function(){
		var size = $(this).data('size');
		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: { 
				action : 'af_cp_add_actions_scenario_ajax'
				, array_size: size
				, product_id: $(this).data('product_id')
				, nonce:nonce
			},
			success: function( data ){
				$(".af_cp_scenario_main_div").append(data['tr_data']);
				$(".af_cp_scenario_desc_div_" + size ).show();
				$(".af_cp_add_new_scenario_btn").data('size' , ++size );

				afcp_products_cat_tag_fn();
			}
		});
	});

	$(document).on('click' , '.af_cp_scenario' , function(e){
		e.preventDefault();
			$(".af_cp_scenario_name_" + $(this).data('key') ).css('background','#ff000099');
			$(".af_cp_scenario_name_" + $(this).data('key') ).fadeOut(800,function(){
				$(this).remove();
			});
			$(".af_cp_scenario_desc_div_" + $(this).data('key') ).css('background','#ff000099');
			$(".af_cp_scenario_desc_div_" + $(this).data('key') ).fadeOut(800,function(){
				$(this).remove();
			});
	});

	$(document).on('click' , '.af_comp_product_remove_component' , function(e){
		e.preventDefault();
	
			$(".af_composite_product_desc_div_" + $(this).data('key') ).css('background','#ff000099');
			$(".af_composite_product_desc_div_" + $(this).data('key') ).fadeOut(800,function(){
				$(this).remove();
			});
			$(".af_composite_product_title_div_" + $(this).data('key') ).css('background','#ff000099');
			$(".af_composite_product_title_div_" + $(this).data('key') ).fadeOut(800,function(){
				$(this).remove();
			});
	});

	$(document).on('click' , '.af_cp_expand_all' , function(){
		$(".af_composite_product_desc_div").show(500);
	});

	$(document).on('click' , '.af_cp_close_all' , function(){
		$(".af_composite_product_desc_div").hide(500);
	});

	$(document).on('click' , '.af_cp_comp_basic' , function(){
		$('.af_cp_comp_setting_' + $(this).data('key') ).hide();
		$('.af_cp_comp_basic_' + $(this).data('key') ).show();
		$(this).addClass('af_cp_comp_btn_active');
		$('.af_cp_comp_setting_btn_' + $(this).data('key') ).removeClass('af_cp_comp_btn_active');
	});
	
	$(document).on('click' , '.af_cp_comp_setting' , function(){
		$('.af_cp_comp_setting_' + $(this).data('key') ).show();
		$('.af_cp_comp_basic_' + $(this).data('key') ).hide();
		$(this).addClass('af_cp_comp_btn_active');
		$('.af_cp_comp_basic_btn_' + $(this).data('key') ).removeClass('af_cp_comp_btn_active');
	});

	$(document).on('click' , '.af_cp_layout_div' , function(){
		var id = $(this).attr('id');
		$('.af_comp_product_layout').prop('checked' , false);
		$(this).find('input[type=radio]').prop('checked' , true);
		$(this).find('input[type=radio]').focus();
		$('.af_cp_layout_div').removeClass('af_cp_selected_layout');
		$(this).addClass('af_cp_selected_layout');
	});

	// component image
	$(document).on('change' , '.af_component_image-input' , function(){
		$( '.' + $(this).data('image_class') ).attr('src', URL.createObjectURL(event.target.files[0]) );
		$( '.' + $(this).data('image_class') ).attr('height', '200' );
		$( '.' + $(this).data('image_class') ).attr('width', '200' );
	});

	$(document).on("change",".af_comp_product_default_product",function(){
		var key             = $(this).data('key');
		var default_product = $(this).data('default_product');
		$('.af_comp_pro_default_product' + key).empty();
		af_comp_pro_default_product_search( key , default_product ); 
	});

	$(document).on('change' , '.af_cp_comp_def_product' , function(){
		$(this).closest('table').find('.af_comp_product_default_product').data('default_product' , $(this).val() );
		$('.af_composite_product_title_div_' + $(this).data('key')  ).data('default_product' , $(this).val() );
		$(this).data('default_product' , $(this).val() );
	});

	$(document).on( 'click' , '.upload_image_for_component' , function(){
		var image_class   = $(this).data('image');
		var attachment_id = $(this).data('attachment_id');
		if (this.window === undefined) {
			this.window = wp.media({
				title: $(this).data('text'),
				library: {type: 'image'},
				multiple: false,
				button: {text: $(this).data('text')}
			});

			var self = this;
			this.window.on('select', function() {
				var response = self.window.state().get('selection').first().toJSON();
				$( attachment_id ).val(response.id);
				$( image_class ).attr('src', response.url);
				$( image_class ).attr('width', '200' );
				$( image_class ).attr('height', '200' );
			});
		}
		this.window.open();
		return false;
	});

	function af_comp_pro_default_product_search( key , default_product ){
		var all_products = $(".af_comp_product_default_all_products_" + key ).val();
		var all_cats     = $(".af_comp_product_default_all_cats_" + key ).val();
		var all_tags     = $(".af_comp_product_default_all_tags_" + key ).val();
		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: { 
				action: 'af_comp_default_product_live_search',
				default_product : default_product,
				products : all_products,
				cats : all_cats,
				tags : all_tags,
				nonce:nonce
			},
			success: function( data ){

				for (let index = 0; index < data.length; index++) {

					    const element = data[index];
						$('.af_comp_pro_default_product' + key).append(element);
			    	}


				
               $('.af_comp_pro_default_product' + key).each(function() {

                  let seenValues = new Set();

                  $(this).find('option').each(function() {

                     if (seenValues.has($(this).val())) {
            
                            $(this).remove();
                     }else {
    
                            seenValues.add($(this).val());
                     }
               });
             });


		    	}
		});
	}

	$(document).on('click' , '.af_cp_remove_row' , function(){
		$(this).closest('tr').css( 'background-color' , 'tomato' );
		$(this).closest('tr').fadeOut( "slow", function() {
			$(this).closest('tr').remove();
		});
	});

	$(document).on('click' , '.af_cp_enable_sce_div' , function(event){
		event.preventDefault();
		if ( $(this).hasClass('af_cp_enable_sce_div_selected') ) {
			$(this).find('.af_cp_enable_sce_span').removeClass('af_cp_enable_sce_span_selected');
			$(this).find('input[type=checkbox]').prop('checked' , false);
			$(this).removeClass('af_cp_enable_sce_div_selected');
		} else {
			$(this).find('.af_cp_enable_sce_span').addClass('af_cp_enable_sce_span_selected');
			$(this).find('input[type=checkbox]').prop('checked' , true);
			$(this).addClass('af_cp_enable_sce_div_selected');
		}
	});

	$(document).on('click' , '.af_cp_scenario_div .title_div' , function(e){
		if ( $( $(this).closest('.af_cp_scenario_div').data('desc_div') ).is(":visible") ) {
			$( $(this).closest('.af_cp_scenario_div').data('desc_div') ).hide(500);
		} else {
			$( $(this).closest('.af_cp_scenario_div').data('desc_div') ).show(500);
		}
	});

	$('input.af_cp_adj_value_input').closest('tr').hide();
	if ( 'ignore' != $('select.af_cp_adj_type_select').val() ) {
		$('input.af_cp_adj_value_input').closest('tr').show();
	}
	$(document).on('change' , 'select.af_cp_adj_type_select' , function(){
		if ( 'ignore' == $(this).val() ) {
			$('input.af_cp_adj_value_input').closest('tr').hide();
		} else {
			$('input.af_cp_adj_value_input').closest('tr').show();
		}
	});

	$(document).on('click' , '.shipping_options' , function(){
		jQuery('.options_group').show();
	});
	
});
