jQuery(document).ready( function($){
	"use strict";

	
	function removeFirstDuplicate() {
		jQuery('div.single_component').each(function () {
			// Check if `.selected_product` contains the `.variations_form` class
			if (jQuery(this).find('.selected_product .variations_form').length > 0) {
				// Find all `.af_cp_component_inline_center` within the current `.single_component`
				let inlineCenters = jQuery(this).find('.af_cp_component_inline_center');
	
				// If more than one `.af_cp_component_inline_center` is found, remove the first one
				if (inlineCenters.length > 1) {
					inlineCenters.first().remove();
				}
	
				// Check the value of the input with class `variation_id` inside `.selected_product`
				let variationInput = jQuery(this).find('.selected_product .variation_id');
				if (variationInput.length > 0) {
					let variationValue = variationInput.val();
					if (!variationValue || variationValue == "0") {
						// Hide the quantity box if `variation_id` value is 0 or empty
						jQuery(this).find('.selected_product .af_cp_closest_variable_price_range').show();
						jQuery(this).find('.selected_product .af_cp_component_inline_center').hide();
						
					} else {
						// Show the quantity box if `variation_id` value is not 0 or empty
						jQuery(this).find('.selected_product .af_cp_closest_variable_price_range').hide();
						jQuery(this).find('.selected_product .af_cp_component_inline_center').show();
					}
				} else {
					// Hide the quantity box if no `variation_id` input is found
					jQuery(this).find('.selected_product .af_cp_closest_variable_price_range').show();
					jQuery(this).find('.selected_product .af_cp_component_inline_center').hide();
				}
			}
		});
	}
	
	

	

    // // Hover event listener
    // $(document).on('mouseenter', '[name^="af_cp_component_product_qty"]', function () {
    //     removeDuplicates();
    // });

    // Periodic check every second to remove duplicates
    

	

	jQuery(document).on('click', '.view_product', function(event) {
		// Stop all other event listeners on the .view_product element
		jQuery(this).off('click');
		event.stopPropagation();
	});


	
	
	$(document).on('click', '.af_cp_thumbnail_main .close-circle', function (event) {
	
		event.stopPropagation();
		var parentDiv = $(this).closest('.af_cp_thumbnail_main');
	
		var targetDiv = parentDiv.find('.af_cp_single_prod_th').filter(function () {
			var titleText = $(this).find('.title').text().trim();
			return titleText.includes('Select a product');
		});
		
		// Locate the hidden input field within the matched targetDiv
		
		const parentElement = targetDiv.parents().filter(function() {
			return this.className.match(/single_products_\d+_\d+/);
		}).first();


		if (parentElement.length) {
			const className = parentElement[0].className;
			const match = className.match(/single_products_\d+_(\d+)/);
			const lastNumber = match ? match[1] : null;

			jQuery('.af_cp_comp_product_comp_quantity_box_'+lastNumber).hide();
			jQuery(this).closest('.af_cp_thumbnail_main').find('input[type=hidden]').val('select');
			jQuery(this).closest('.af_cp_thumbnail_main').find('input[type=hidden]').removeClass('af_cp_thumbnail_selected');
			
			
						// Handle the cursor wait and AJAX call
			const singleComponentDiv = $(this).closest('div.single_component');
				if (singleComponentDiv.length) {
					 singleComponentDiv.addClass('af_cp_cursor_wait');
				}
	
	

				
		jQuery(this).closest('.af_cp_thumbnail_main .af_cp_single_prod_th').removeClass('af_cp_selected_product_item');
		targetDiv.addClass('af_cp_selected_product_item');
		
		var hiddenInput = targetDiv.find('input.af_cp_select_component_thumb');		

		// Set the value to "Selected" and add the class "af_cp_thumbnail_selected"
		   hiddenInput.val('Selected').addClass('af_cp_thumbnail_selected');
			af_cp_current_scenario_ajax(lastNumber, true, singleComponentDiv.attr('class'));
	
		}
		jQuery(this).remove();
	});


	$(document).on('click', '.af_cp_comp_radio_op_div .radio-close-circle', function (event) {
		// Stop the click event from propagating to the parent .af_cp_single_radio
		event.stopPropagation();
	
		// Find the closest div with class .af_cp_radio_optional_div within the same .af_cp_comp_radio_op_div
		var radioOptionalDiv = $(this).closest('.af_cp_comp_radio_op_div').find('.af_cp_radio_optional_div');
	
		const parentElement = radioOptionalDiv.parents().filter(function () {
			return this.className.match(/single_products_\d+_\d+/);
		}).first();
		
		if (parentElement.length) {
			const className = parentElement[0].className;
			const match = className.match(/single_products_\d+_(\d+)/);
			const lastNumber = match ? match[1] : null;
	
			// Remove existing elements
	
			$(this).parent('.af_cp_single_radio').removeClass('af_cp_selected_radio_product_item');
			
			// Check if radio button is not checked and add class
			const radioInput = radioOptionalDiv.find('input[type=radio]');
			if (radioInput.prop('checked') == false) {

				jQuery('.af_cp_comp_product_comp_quantity_box_'+lastNumber).hide();
				radioOptionalDiv.addClass('af_cp_selected_radio_product_item');
				radioOptionalDiv.closest('div.single_component').addClass('af_cp_cursor_wait');
				radioInput.prop('checked', true);

				af_cp_current_scenario_ajax(
					lastNumber,
					true,
					radioOptionalDiv.closest('div.single_component').attr('class')
				);
			} 
		}
	});
	
	$(document).on('click', '.af_cp_single_radio', function(event) {
		// Prevent click propagation if clicking on cross or open circle
		if ($(event.target).is('.radio-close-circle, .radio-open-circle')) {
			return;
		}

		// Check if the clicked element already has the selected class
		if ($(this).hasClass('af_cp_selected_radio_product_item')) {
			return; // Exit if the class exists
		}

		
	
		if (jQuery('.radio_comp_requrired_' + $(this).closest('.single_component').data('comp_key')).length > 0) {
			if (!$(this).find('.radio-open-circle').length) {
				$(this).append('<span class="radio-open-circle">✓</span>');
			}
		} else {
			if (!$(this).find('.radio-close-circle').length) {
				$(this).append('<span class="radio-close-circle">x</span>');
			}
		}
	
		// Remove selected class from all and add to the clicked element
		$(this).siblings('.af_cp_single_radio').removeClass('af_cp_selected_radio_product_item');
		if ($(this).find('input[type=radio]').prop('checked') == false) {
			$(this).addClass('af_cp_selected_radio_product_item');
			$(this).closest('div.single_component').addClass('af_cp_cursor_wait');
			$(this).find('input[type=radio]').prop('checked', true);
	
			$(this).closest('.single_component').find('.af_cp_component_qty').each(function() {
				let minValue = $(this).attr('min');
				if (minValue !== undefined) {
					$(this).val(minValue);
				}
			});

		

			af_cp_current_scenario_ajax(
				$(this).closest('.single_component').data('comp_key'),
				true,
				$(this).closest('div.single_component').attr('class')
			);
		}
	});
	
	
	


	$(document).on('click', '.af_cp_thumbnail_main .af_cp_single_prod_th', function(event) {
		

		if (!$(this).hasClass('af_cp_selected_product_item')) {
			jQuery('.close-circle').show();
			const parentElement = $(this).parents().filter(function() {
				return this.className.match(/single_products_\d+_\d+/);
			}).first();
	
			if (parentElement.length) {
				const className = parentElement[0].className;
				const match = className.match(/single_products_\d+_(\d+)/);
				const lastNumber = match ? match[1] : null;
	
				// jQuery('.af_cp_comp_product_comp_quantity_box_'+lastNumber).show();

				const hiddenInput = $(this).find('input.af_cp_select_component_thumb[type="hidden"]');
				if (hiddenInput.length) {
					const product_id = hiddenInput.data('product_id'); // Get data-product_id value
					const inputName = hiddenInput.data('input_name'); // Get data-input_name value
	
					// Ensure the hidden input field has a valid value
					if (product_id !== $("#" + inputName).val()) {
						// Update the value of the hidden input field
	
						
	
						$(this).closest('.af_cp_thumbnail_main').find('input[type=hidden]').val('select');
			            $(this).closest('.af_cp_thumbnail_main').find('input[type=hidden]').removeClass('af_cp_thumbnail_selected');
						$(this).siblings('.af_cp_single_prod_th').removeClass('af_cp_selected_product_item');

	
						hiddenInput.val('selected');  // Set hidden input value
						hiddenInput.addClass('af_cp_thumbnail_selected');  // Add the class to hidden input if needed
	
						$(this).addClass('af_cp_selected_product_item');
						$(this).siblings('.af_cp_single_prod_th .open-circle').remove();
						$(this).siblings('.af_cp_single_prod_th .close-circle').remove();

						if (jQuery('.thumbnail_comp_requrired_'+lastNumber).length) {
							
						if (!$(this).closest('.af_cp_single_prod_th').find('.open-circle').length) {
							$(this)
								.closest('.af_cp_single_prod_th')
								.find('.image')
								.append('<span class="close-circle">✓</span>');
						}

						} else {
							
						if (!$(this).closest('.af_cp_single_prod_th').find('.close-circle').length) {
							$(this)
								.closest('.af_cp_single_prod_th')
								.find('.image')
								.append('<span class="close-circle">x</span>');
						}

						}
						
						$("#" + inputName).val(product_id);  // Set the value in the associated hidden input field
	
						// Handle the cursor wait and AJAX call
						const singleComponentDiv = $(this).closest('div.single_component');
						if (singleComponentDiv.length) {
							singleComponentDiv.addClass('af_cp_cursor_wait');
						}

						
						$(this).closest('.single_component').find('.af_cp_component_qty').each(function() {
							let minValue = $(this).attr('min');
							if (minValue !== undefined) {
								$(this).val(minValue);
							}
						});

						
				
						af_cp_scenario_ajax(lastNumber, true, singleComponentDiv.attr('class'));
					}
				}
			}
		}
	});

	
	jQuery(document).on('click', '.simple_clear_all', function () {
		// Find the nearest .af_cp_get_simple_dropdown container
		var $select = $(this).closest('.af_cp_select_cover').find('select.af_cp_get_simple_dropdown');
		$(this).hide();
		// Check if the select element exists
		if ($select.length) {
			
			// Set the value of the select to 0
			$select.val('0').trigger('change');
		}
	});
	
	jQuery(document).on('click', '.clear_all', function () {

	    jQuery("."+jQuery('.af-cp-dropdown-'+jQuery(this).val()).data('selected')).html(jQuery('.af-single-component-dropdown-'+jQuery(this).val()+'-0').html());
		jQuery('.af-single-component-dropdown-'+jQuery(this).val()).removeClass('af-selected-product-option');
		jQuery('.af-single-component-dropdown-'+jQuery(this).val()+'-0').addClass('af-selected-product-option');
		jQuery( "#" + jQuery('.af-single-component-dropdown').data('input_name') ).val( 0 );
		jQuery('.af_cp_comp_product_comp_quantity_box_'+jQuery(this).val()).hide();
		$(this).closest('div.single_component').addClass('af_cp_cursor_wait');
		$(this).hide();
		
		af_cp_current_scenario_ajax( jQuery(this).val() , true , jQuery(this).parent().closest('div.single_component').attr('class') );
	
		});
		function pagisimpledropdown() {
			jQuery('.af_cp_select_cover').each(function () {
				var $qtyBoxProductImage = jQuery(this).closest('.single_component').find('.quantity .af_cp_component_qty');
				var $selectedProduct = jQuery(this).closest('.single_component').find('.selected_product'); // Identify the selected product
				var $detailDiv = jQuery(this).closest('.single_component').find('.detail'); // Find the .detail div
				

				jQuery(this).closest('.single_component').find('.view_selected_product_cover').show();
				
				// Ensure that the quantity input is moved to the correct position after the selected product
				if ($selectedProduct.length > 0) {
					// Insert the quantity box after the selected product
					if (!$qtyBoxProductImage.is($selectedProduct.next())) {
						$qtyBoxProductImage.insertAfter($selectedProduct).show();
		
						// Align with the .detail div
						if ($detailDiv.length > 0) {
							var imageWidth = jQuery(this).closest('.single_component').find('.image').outerWidth(true);
							
							$qtyBoxProductImage.css({
								'position': 'relative',
								'left': imageWidth + 'px', // Aligns with the end of the image div
								'max-width':'60px', // Matches the width of the detail div
								'display': 'block',
								'font-size':'11px',
								'max-height':'25px',
								'margin-top': '10px' // Optional if you need vertical spacing
							});
						}
					}
				}
			});
		}
		function pagiimagedropdownselection() {
			jQuery('.af-style-drop-down-main').each(function () {
				var $qtyBoxProductImage = jQuery(this).closest('.single_component').find('.quantity .af_cp_component_qty');
				var $selectedProduct = jQuery(this).closest('.single_component').find('.selected_product'); // Identify the selected product
				var $detailDiv = jQuery(this).closest('.single_component').find('.detail'); // Find the .detail div
				
				
				// Ensure that the quantity input is moved to the correct position after the selected product
				if ($selectedProduct.length > 0) {
					// Insert the quantity box after the selected product
					if (!$qtyBoxProductImage.is($selectedProduct.next())) {
						$qtyBoxProductImage.insertAfter($selectedProduct).show();

						// Align with the .detail div
						if ($detailDiv.length > 0) {
							var imageWidth = jQuery(this).closest('.single_component').find('.image').outerWidth(true);
				
							$qtyBoxProductImage.css({
								'position': 'relative',
								'left': imageWidth + 'px', // Aligns with the end of the image div
								'max-width':'60px', // Matches the width of the detail div
								'display': 'block',
								'font-size':'11px',
								'max-height':'25px',
								'margin-top': '10px' // Optional if you need vertical spacing
							});
						}
					}
				}
			});
		}
		
		
		function pagiradioselection() {
			
			jQuery('.af_cp_comp_radio_op_div').each(function() {
	
				var $radioBtnatag = jQuery(this).find('.view_product_comp a');
				
				$radioBtnatag.css({
					'font-size':'12px'
				});
			
				  var radioProductItem = jQuery(this).find('.af_cp_selected_radio_product_item');
								 
				
				  if (radioProductItem.length > 0) {  

			
					if (radioProductItem.find('.af_cp_radio_product_type_simple').length > 0) {
						var $radioqtyBox = radioProductItem.closest('.single_component').find('.af_cp_component_inline_center');
					
						var $radioBtn = radioProductItem.find('.af_cp_comp_radio_div .view_product_comp');
			
						if (!$radioqtyBox.is($radioBtn.prev())) {
			
							$radioqtyBox.insertBefore($radioBtn);
							$radioqtyBox.show();
			
			
						  }
					  }    


					  if (radioProductItem.find('.af_cp_radio_product_type_variation').length > 0) {
						var $radioqtyBox = radioProductItem.closest('.single_component').find('.af_cp_component_inline_center');
					
						var $radioBtn = radioProductItem.find('.af_cp_comp_radio_div .view_product_comp');
			
						if (!$radioqtyBox.is($radioBtn.prev())) {
			
							$radioqtyBox.insertBefore($radioBtn);
							$radioqtyBox.show();
			
			
						  }
					  } 



					  if (radioProductItem.find('.af_cp_radio_product_type_af_composite_product').length > 0) {
						var $radioqtyBox = radioProductItem.closest('.single_component').find('.af_cp_component_inline_center');
					
						var $radioBtn = radioProductItem.find('.af_cp_comp_radio_div .view_product_comp');
			
						if (!$radioqtyBox.is($radioBtn.prev())) {
			
							$radioqtyBox.insertBefore($radioBtn);
							$radioqtyBox.show();
			
			
						  }
					  } 		  
			
					  }
				   });
		}
	
	function pagithumbnailProductSelection() {
	
			jQuery('.af_cp_thumbnail_main').each(function() {
				var selectedProductItem = jQuery(this).find('.af_cp_selected_product_item');
			
				if (selectedProductItem.length > 0) {


					if (selectedProductItem.find('.af_cp_thumbnail_product_type_simple').length > 0) {
						var $qtyBox = selectedProductItem.closest('.single_component')
							.find('.af_cp_component_inline_center').first();
			
						var $selectBtn = selectedProductItem.find('.view_product_comp');
						if (!$qtyBox.is($selectBtn.prev())) {

							$qtyBox.insertBefore($selectBtn);
							$qtyBox.show();
							
						}
					}

					if (selectedProductItem.find('.af_cp_thumbnail_product_type_variation').length > 0) {
						var $qtyBox = selectedProductItem.closest('.single_component')
							.find('.af_cp_component_inline_center').first();
			
						var $selectBtn = selectedProductItem.find('.view_product_comp');
						if (!$qtyBox.is($selectBtn.prev())) {

							$qtyBox.insertBefore($selectBtn);
							$qtyBox.show();
							
						}
					}

					if (selectedProductItem.find('.af_cp_thumbnail_product_type_af_composite_product').length > 0) {
						var $qtyBox = selectedProductItem.closest('.single_component')
							.find('.af_cp_component_inline_center').first();
			
						var $selectBtn = selectedProductItem.find('.view_product_comp');
						if (!$qtyBox.is($selectBtn.prev())) {

							$qtyBox.insertBefore($selectBtn);
							$qtyBox.show();
							
						}
					}


					
					
				
			
				}
			});

	}
	$('.af_cp_simple_select2').select2();



$('.af_cp_simple_select2').on('select2:open', function() {
        setTimeout(function() {
			jQuery('.select2-results__options li').each(function () {
				var optionText = jQuery(this).text().trim();
				if (optionText.includes('Select a product')) {
					jQuery(this).hide(); // Hide the specific option
				}
			});
			
        }, 100); // Adjust the timeout if necessary
    });

	jQuery('.price').hide();

	var ajaxurl = af_comp_product.admin_url;
	var nonce   = af_comp_product.nonce;
	
	jQuery('.woocommerce-cart-form .show_component_name_block').remove();

	jQuery('.selected_product .woocommerce-variation-price').hide();

	jQuery('.selected_product .woocommerce-variation-availability').hide();

	setInterval(() => {
		pagiradioselection();
		pagithumbnailProductSelection();
		removeFirstDuplicate();

		$('.af_cp_single_radio').each(function() {
			if (!$(this).hasClass('af_cp_selected_radio_product_item')) {
				$(this).find('.radio-close-circle').remove();
				$(this).find('.radio-open-circle').remove();

			}
		});
	
		if (jQuery('.selected_product .woocommerce-variation-description').length) {
			// Apply the logic only to descriptions that don't already have the functionality
			jQuery('.selected_product .woocommerce-variation-description').each(function () {
				var descriptionContainer = jQuery(this);
			
				// Skip if the "Read More" functionality is already applied
				if (descriptionContainer.hasClass('read-more-applied')) {
					return; // Exit for this iteration
				}
			
				// Add marker to indicate functionality is applied
				descriptionContainer.addClass('read-more-applied');
			
				var description = descriptionContainer.find('p'); // Get the paragraph
			
				// Check if a paragraph exists inside the description container
				if (description.length === 0) {
					console.warn('No <p> tag found in .woocommerce-variation-description', descriptionContainer);
					return; // Exit if no paragraph found
				}
			
				var fullText = description.html() || ''; // Fallback to empty string if no content
			
				// Ensure fullText is valid and contains content
				if (fullText.length > 0) {
					var shortText = fullText.slice(0, 100) + ''; // Shortened version
			
					if (fullText.length > 100) {
						// Initially set the shortened text
						description.html(shortText);
			
						// Add the "Read More" link
						descriptionContainer.append('<a href="#" style="font-size:12px!important;" class="read-more-toggle">Read More</a>');
			
						// Add click event for toggling
						descriptionContainer.on('click', '.read-more-toggle', function (e) {
							e.preventDefault(); // Prevent the default anchor behavior
							if (description.hasClass('expanded')) {
								description.html(shortText).removeClass('expanded');
								jQuery(this).text('Read More');
							} else {
								description.html(fullText).addClass('expanded');
								jQuery(this).text('Read Less');
							}
						});
					}
				} else {
					console.warn('Empty description found in .woocommerce-variation-description', descriptionContainer);
				}
			});
			
		}
		
		
		
		jQuery('.selected_product .woocommerce-variation-price').hide();

		jQuery('.selected_product .woocommerce-variation-availability').hide();

		jQuery('.woocommerce-cart-form .show_component_name_block').remove();

		
		jQuery('.af_cp_single_prod_th').each(function () {
			var titleText = jQuery(this).find('.desc .title').text().trim();
			if (titleText.includes('Select a product')) {
				jQuery(this).hide(); // Shorter way to set display: none
			}
		});
		
		

		jQuery('.af_cp_radio_optional_div').hide();
	
		jQuery('.woocommerce-cart-form .show_component_name_block').remove();


		jQuery('.wc-block-components-product-name .show_component_name_block').each(function () {
			jQuery(this).insertBefore(jQuery(this).closest('.wc-block-components-product-name'));
		});

jQuery('.wc-block-components-product-metadata__description p').each(function() {
    var text = jQuery(this).text();
	
    if (text.indexOf('_af_cp_block_before_variation_') !== -1) {
        var beforeText = text.split('_af_cp_block_before_variation_')[0].trim();
        
        if (beforeText) {
            // Check if the new element has already been appended
            if (jQuery(this).closest('.wc-block-cart-item__wrap').find('.show_component_name_block').length === 0 && 
                jQuery(this).closest('.wc-block-components-order-summary-item__description').find('.show_component_name_block').length === 0) {

                // Create the bolded text
                var newBTag = jQuery('<b>').text(beforeText);
                var newPTag = jQuery('<span>').addClass('show_component_name_block').append(newBTag);
                
                // Insert the bolded text before the product name
                jQuery(this).closest('.wc-block-cart-item__wrap').find('.wc-block-components-product-name').before(newPTag);
                jQuery(this).closest('.wc-block-components-order-summary-item__description').find('.wc-block-components-product-name').before(newPTag);
            }
        }
		var updatedText = text.split('_af_cp_block_before_variation_')[1].trim();
	jQuery(this).text(updatedText);
    }
});


		$('.show_component_hide_block').hide();
		$('.wc-block-cart-item__wrap').each(function() {
			// Find the nearest parent with class 'wc-block-cart-item__wrap' (this wraps the whole cart item)
			var $cartItemWrap = $(this);
			
			// Check if the element with the class 'show_component_hide_block' exists
			var $showComponentHideBlock = $cartItemWrap.find('.show_component_hide_block');
			
			// Check if '_af_cp_block_hide_variation_' text is inside the '.wc-block-cart-item__wrap'
			var containsBlockHideVariationText = $cartItemWrap.text().includes('_af_cp_block_hide_variation_');
			
			if ($showComponentHideBlock.length > 0 || containsBlockHideVariationText) {
				// Find the quantity input and buttons within this cart item
				var $quantityInput = $cartItemWrap.find('.wc-block-components-quantity-selector__input');
				var $minusButton = $cartItemWrap.find('.wc-block-components-quantity-selector__button--minus');
				var $plusButton = $cartItemWrap.find('.wc-block-components-quantity-selector__button--plus');
		
				// Disable the quantity input and buttons
				if ($quantityInput.length && $minusButton.length && $plusButton.length) {
					$quantityInput.prop('disabled', true);
					$minusButton.prop('disabled', true);
					$plusButton.prop('disabled', true);
				}
			}
		});
		
// 	$('.wc-block-components-product-metadata__description p').each(function() {
//     var textContent = $(this).text();

//     // Remove the text '_af_cp_block_hide_variation_' if it exists
//     // textContent = textContent.replace('_af_cp_block_hide_variation_', '');

//     // Update the text content of the paragraph after removal
//     $(this).text(textContent);

//     var $cartItemWrap = $(this).closest('.wc-block-cart-item__wrap');

//     if ($cartItemWrap.length > 0) {
//         var $quantitySelector = $cartItemWrap.find('.wc-block-components-quantity-selector');
//         var $quantityInput = $quantitySelector.find('.wc-block-components-quantity-selector__input');
//         var $minusButton = $quantitySelector.find('.wc-block-components-quantity-selector__button--minus');
//         var $plusButton = $quantitySelector.find('.wc-block-components-quantity-selector__button--plus');

//         if ($quantityInput.length && $minusButton.length && $plusButton.length) {
//             $quantityInput.prop('disabled', true);
//             $minusButton.prop('disabled', true);
//             $plusButton.prop('disabled', true);
//         }
//     }
// });

		$('.wc-block-cart-item__wrap').each(function() {
			var $wrap = $(this);
	
			// Find all '.show_component_name_block' inside this specific '.wc-block-cart-item__wrap'
			var $showComponentNameBlocks = $wrap.find('.show_component_name_block');
	
			// Hide all '.show_component_name_block' elements except the first one
			$showComponentNameBlocks.each(function(index) {
				if (index > 0) {
					$(this).hide(); // Hide all except the first one
				}
			});
		});
	
		// // 3. Remove '_af_cp_block_hide_variation_' from <p> text globally
		// $('.wc-block-cart-items__row p').each(function() {
		// 	var updatedText = $(this).text().replace(/_af_cp_block_hide_variation_/g, '');
		// 	$(this).text(updatedText);
		// });
		jQuery('.woocommerce-cart-form .show_component_name_block').remove();


		$('.show_component_name_block').each(function() {
			var currentText = $(this).text();
			var newText = currentText.replace('_af_cp_block_hide_variation_', '');
			$(this).text(newText);
		});
  
	}, 100);



	
	setTimeout(function () {
		af_cp_calculate_validate_product( true , '' );
		
		jQuery('.price').show();
	


		// jQuery('.wc-block-components-product-name .show_component_name_block').each(function () {
		// 	jQuery(this).insertBefore(jQuery(this).closest('.wc-block-components-product-name'));
		// });


		// jQuery('.wc-block-components-product-metadata__description p').each(function() {
		// 	var text = jQuery(this).text();
			
		// 	if (text.indexOf('_af_cp_block_before_variation_') !== -1) {
		// 		var beforeText = text.split('_af_cp_block_before_variation_')[0].trim();
				
		// 		if (beforeText) {
		// 			// Create the bolded text
		// 			var newBTag = jQuery('<b>').text(beforeText);
		// 			var newPTag = jQuery('<p>').addClass('show_component_name_block').append(newBTag);
					
		// 			// Insert the bolded text before the product name
		// 			jQuery(this).closest('.wc-block-cart-item__wrap').find('.wc-block-components-product-name').before(newPTag);
					
		// 			// Remove the unwanted part from the original <p> element
		// 			var updatedText = text.split('_af_cp_block_before_variation_')[1].trim();
		// 			jQuery(this).text(updatedText);
		// 		}
		// 	}
		// });

		// $('.show_component_hide_block').hide();
		// $('.show_component_hide_block').each(function() {
		// 	// Find the nearest parent with class 'wc-block-cart-item__wrap' (this wraps the whole cart item)
		// 	var $cartItemWrap = $(this).closest('.wc-block-cart-item__wrap');
	
		// 	if ($cartItemWrap.length > 0) {
		// 		// Find the quantity input and buttons within this cart item
		// 		var $quantitySelector = $cartItemWrap.find('.wc-block-components-quantity-selector');
		// 		var $quantityInput = $quantitySelector.find('.wc-block-components-quantity-selector__input');
		// 		var $minusButton = $quantitySelector.find('.wc-block-components-quantity-selector__button--minus');
		// 		var $plusButton = $quantitySelector.find('.wc-block-components-quantity-selector__button--plus');
	
		// 		// Disable the quantity input and buttons
		// 		if ($quantityInput.length && $minusButton.length && $plusButton.length) {
		// 			$quantityInput.prop('disabled', true);
		// 			$minusButton.prop('disabled', true);
		// 			$plusButton.prop('disabled', true);
		// 		}
		// 	}
		// });
		// $('p').each(function() {
		// 	var textContent = $(this).text();
	
		// 	if (textContent.includes('_af_cp_block_hide_variation_')) {
		// 		var $cartItemWrap = $(this).closest('.wc-block-cart-item__wrap');
				
		// 		if ($cartItemWrap.length > 0) {
		// 			var $quantitySelector = $cartItemWrap.find('.wc-block-components-quantity-selector');
		// 			var $quantityInput = $quantitySelector.find('.wc-block-components-quantity-selector__input');
		// 			var $minusButton = $quantitySelector.find('.wc-block-components-quantity-selector__button--minus');
		// 			var $plusButton = $quantitySelector.find('.wc-block-components-quantity-selector__button--plus');
	
		// 			if ($quantityInput.length && $minusButton.length && $plusButton.length) {
		// 				$quantityInput.prop('disabled', true);
		// 				$minusButton.prop('disabled', true);
		// 				$plusButton.prop('disabled', true);
		// 			}
		// 		}
		// 	}
		// });
	
		// // 3. Remove '_af_cp_block_hide_variation_' from <p> text globally
		// $('p').each(function() {
		// 	var updatedText = $(this).text().replace(/_af_cp_block_hide_variation_/g, '');
		// 	$(this).text(updatedText);
		// });
  
		
	},1100);


	$(document).on('click' , '.af-selected-product-comp' , function(event){
		event.preventDefault();
		if ( $( "div." + $(this).data('key_id') ).is(":hidden") ) {
			$( "div." + $(this).data('key_id') ).show();
			$(".af-selected-product-comp .af-cp-arrow-div").html("&#9650;");
		} else {
			$( ".af-selected-product-comp .af-cp-arrow-div").html("&#9660;");
			$(".af-comp-products-dropdown").hide();
		}
	});
	
	$(document).mouseup(function(e) {
		var container_target = $(".af-selected-product-comp");
		if ( (!container_target.is(e.target) && !$('.af-cp-search-img-products').is(e.target) ) && container_target.has(e.target).length === 0) {
			$( ".af-selected-product-comp .af-cp-arrow-div").html("&#9660;");
			$(".af-comp-products-dropdown").hide();
		}
	});

	$(document).on('keyup , keydown' , '.af-cp-search-img-products' , function(){
		$('.af-single-component-dropdown').hide();

		var search_string        = $(this).val();
		let all_dropdown_options = $(this).closest('div.af-comp-products-dropdown').find('div.af-single-component-dropdown');
		if ( ('' != search_string) && ( ' ' != search_string ) ) {
			for (let index = 0; index < all_dropdown_options.length; index++) {
				let element = all_dropdown_options[index];
				if ( $(element).find('p.title').text().toUpperCase().indexOf(search_string.toUpperCase()) > -1 ) {
					$(element).show();
				} else {
					$(element).hide();
				}
			}
		} else {
			for (let index = 0; index < all_dropdown_options.length; index++) {
				let element = all_dropdown_options[index];
				$(element).show();
			}
		}
	});

	

	$(document).on('click' , 'a' , function(event){
		if ( $(this).attr('href') == $('#afcp-add-to-cart-product').data('href') ) {
			event.preventDefault();
			$('#afcp-add-to-cart-product').click();
		}
	});

	$(document).on('click', '.af-style-drop-down-main', function() {
		// Set the key ID value
		$("#af-composite-key-id").val($(this).data('key_id'));
	
		// Reference the clicked container
		var container = $(this); 
		var width = container.innerWidth(); // Get the inner width including padding (scrollbar-safe)
		
		// Find the dropdown and set its width
		container.find('.af-comp-products-dropdown').css({
			'width': width + 'px', // Apply the adjusted width
			'box-sizing': 'border-box' // Ensure padding/borders are included in the width calculation
		});
	});
	

	$(document).on('click' , '.single_component_title' , function(){
		if ( $('.' + $(this).data('desc') ).is(':hidden') ) {
			$('.single_component_title div.expand').html('&#9660;');
			$('.single_component_description').hide(500);
			$(this).find('div.expand').html('&#9650;');
			$('.' + $(this).data('desc') ).show(500);
		}
	});



	$('div.single_component').addClass('af_cp_cursor_wait');
	if ( $('.af_cp_current_product_id')[0] ) {
		$('.single_add_to_cart_button').prop('disabled' , true);
		$(".single_add_to_cart_button").addClass("disabled");

	setTimeout(() => {
	
		var current_key = 'all';
		af_cp_scenario_ajax( current_key , true , 'onload_running_ajax' );
		
	}, 1500);
}
	function af_cp_scenario_ajax( current_key , replace_data , attr_class ){
		var af_cp_sce_class = 'yes';
		if ( 'onload_running_ajax' == attr_class ) {
			var af_cp_sce_class = 'no';
		}
		var steps_div_show = '';
		if ( $('.single_component')[0] ) {
			for (let index = 0; index < $('.single_component').length; index++) {
				const element = $('.single_component')[index];
				if ( $(element).is(':visible') ) {
					if ( !$(element).hasClass('af-cp-toggle-title-div') ) {
						steps_div_show = element;
					}
				}
			}
		}
		var form_data = $('form.af_cp_cart_form' ).find('input , select').serialize();
		let product_id = $('.af_cp_current_product_id').val();

		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: {
				action : 'af_cp_on_select_scenarios_ajax'
				, form_data: form_data
				, current_key: current_key
				, af_cp_sce_class: af_cp_sce_class
				, product_id: product_id
				, nonce:nonce
			},
			success: function( return_data ){

				const all_btn_html = Object.values(return_data['new_btn']);
				const new_btn_keys = Object.keys(return_data['new_btn']);
				for (let index = 0; index < new_btn_keys.length; index++) {
					const element_key = new_btn_keys[index];
					const element_val = all_btn_html[index];
					$('.' + element_key).html(element_val);
				}

				$('.single_component').show();
				$(".af_cp_toggle_template_hidden").hide();
				if ( ( '' != steps_div_show ) && ( $('.af_cp_steps_template')[0] ) ) {
					$('.single_component').hide();
					$(steps_div_show).show();
					$('.bottom_btn_step_' + $('.af_cp_current_product_id').val() + '_' + $(steps_div_show).data('comp_key') ).show();
				}
				if ( ( '' != steps_div_show ) && ( $('.af-cp-toggle-title-div')[0] ) ) {
					$('.single_component_description').hide();
					$(steps_div_show).show();
				}
				$.each( return_data['hide'] , function( key , value ) {
					$(".single_component_" + value ).hide();
					$('.review-div_' + value ).hide();
					$('.af-cp-toggle-product-' + value ).hide();
					if ( $('.af_cp_steps_btn')[0] ) {
						for (let btn_inc = 0; btn_inc < $('.af_cp_steps_btn').length; btn_inc++) {
							const a_btn_element = $('.af_cp_steps_btn')[btn_inc];
							$(a_btn_element).data('hidden_from_scenario' , 'no');
							if ( "single_comp_step_" + value == $(a_btn_element).data('val') ) {
								$(a_btn_element).hide();
								$(a_btn_element).data('hidden_from_scenario' , 'yes');
							}
						}
					}
				});
				
				if ( replace_data ) {
					

					$.each( return_data['seniro_replace'] , function( key , value ) {
						$("." + key ).closest('div.products').html(value);
					});

					$.each( return_data['replace'] , function( key , value ) {

					//	console.log(return_data['replace']);
						if(key  == 'af_cp_select_product_'+product_id+'_'+current_key ){
							$("." + key ).closest('div.products').html(value);

						}
					});
					$.each( return_data['selected'] , function( key , value ) {
						if(key  == 'af_cp_select_product_'+product_id+'_'+current_key ){
							$("." + key ).html(value);
						}
					});


					
				}
				
				$('.af_cp_simple_select2').select2();
				setTimeout(() => {
				af_cp_calculate_validate_product( replace_data ,current_key);
				}, 100);
			}
		});
	}


	function af_cp_current_scenario_ajax( current_key , replace_data , attr_class ){
		var af_cp_sce_class = 'yes';
		if ( 'onload_running_ajax' == attr_class ) {
			var af_cp_sce_class = 'no';
		}
		var steps_div_show = '';
		if ( $('.single_component')[0] ) {
			for (let index = 0; index < $('.single_component').length; index++) {
				const element = $('.single_component')[index];
				if ( $(element).is(':visible') ) {
					if ( !$(element).hasClass('af-cp-toggle-title-div') ) {
						steps_div_show = element;
					}
				}
			}
		}
		var form_data = $('form.af_cp_cart_form' ).find('input , select').serialize();
		let product_id = $('.af_cp_current_product_id').val();

		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: {
				action : 'af_cp_on_select_scenarios_ajax'
				, form_data: form_data
				, current_key: current_key
				, af_cp_sce_class: af_cp_sce_class
				, product_id: product_id
				, nonce:nonce
			},
			success: function( return_data ){

				const all_btn_html = Object.values(return_data['new_btn']);
				const new_btn_keys = Object.keys(return_data['new_btn']);
				for (let index = 0; index < new_btn_keys.length; index++) {
					const element_key = new_btn_keys[index];
					const element_val = all_btn_html[index];
					$('.' + element_key).html(element_val);
				}

				$('.single_component').show();
				$(".af_cp_toggle_template_hidden").hide();
				if ( ( '' != steps_div_show ) && ( $('.af_cp_steps_template')[0] ) ) {
					$('.single_component').hide();
					$(steps_div_show).show();
					$('.bottom_btn_step_' + $('.af_cp_current_product_id').val() + '_' + $(steps_div_show).data('comp_key') ).show();
				}
				if ( ( '' != steps_div_show ) && ( $('.af-cp-toggle-title-div')[0] ) ) {
					$('.single_component_description').hide();
					$(steps_div_show).show();
				}
				$.each( return_data['hide'] , function( key , value ) {
					$(".single_component_" + value ).hide();
					$('.review-div_' + value ).hide();
					$('.af-cp-toggle-product-' + value ).hide();
					if ( $('.af_cp_steps_btn')[0] ) {
						for (let btn_inc = 0; btn_inc < $('.af_cp_steps_btn').length; btn_inc++) {
							const a_btn_element = $('.af_cp_steps_btn')[btn_inc];
							$(a_btn_element).data('hidden_from_scenario' , 'no');
							if ( "single_comp_step_" + value == $(a_btn_element).data('val') ) {
								$(a_btn_element).hide();
								$(a_btn_element).data('hidden_from_scenario' , 'yes');
							}
						}
					}
				});
				
				if ( replace_data ) {
				

					$.each( return_data['replace'] , function( key , value ) {

					//	console.log(return_data['replace']);
						if(key  == 'af_cp_select_product_'+product_id+'_'+current_key ){
							$("." + key ).closest('div.products').html(value);

						}
					});
					$.each( return_data['selected'] , function( key , value ) {
						if(key  == 'af_cp_select_product_'+product_id+'_'+current_key ){
							$("." + key ).html(value);
						}
					});


					
				}
				
				$('.af_cp_simple_select2').select2();
				setTimeout(() => {
				af_cp_calculate_validate_product( replace_data ,current_key);
				}, 100);
			}
		});
	}




	function af_cp_calculate_validate_product( replace_data , cart_key = '' ){
		if ( $('.af_cp_current_product_id')[0] ) {
			$('.single_add_to_cart_button').prop('disabled' , true);
			$(".single_add_to_cart_button").addClass("disabled");
		}
			
		var product_id = $('.af_cp_current_product_id').val();
		var form_data = $('form.af_cp_cart_form').find('input , select').serialize();
		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: { 
				action : 'af_cp_on_select_change_ajax'
				, form_data: form_data
				, product_id: product_id
				, nonce:nonce
			},
			success: function( return_data ){

				var priceText = $('p.price').text().trim();
				var isNumericPrice = !isNaN( parseFloat( priceText.replace(/[^\d.-]/g, '') ) );
				
				$('div.single_component').removeClass('af_cp_cursor_wait');
				$('p.price').removeClass('af_cp_cursor_wait');
				$('.single_add_to_cart_button').removeClass('af_cp_cursor_wait');
				$('.af_cp_simple_select2').select2();
				if ( 'yes' == return_data['success'] ) {
					$('.single_add_to_cart_button').prop('disabled' , false);
					$(".single_add_to_cart_button").removeClass("disabled");

				}
				if( isNumericPrice ){
					$('p.price').html(return_data['price']);
					$('span.storefront-sticky-add-to-cart__content-price').html(return_data['price']);
				}
				var errors = return_data['validation_msg'];
				$.each( errors , function( indexes , messages_html ) {
					$("." + indexes ).html(messages_html);
				});


				if ( replace_data ) {
					var selected_product = return_data['selected_product'];
					

					$.each( selected_product , function( div_class , product_html ) {
						$("." + div_class ).html(product_html);
		
						// if( cart_key != '' ){
							
						// 		if( div_class == 'af_cp_selected_product_'+product_id+'_'+cart_key ){
				
						// 	$("." + div_class ).html(product_html);
						// 		}
								
							


						// }else {
							
						// }
					});
				}

		

				$('.variations_form').each( function() {
					$( this ).wc_variation_form();
				});

				
			}
		});



			





	}

	$(document).on('change , keyup' , '.af_cp_select_product_qty ' , function(){
		$(this).closest('div.single_component').addClass('af_cp_cursor_wait');


		af_cp_scenario_ajax( $(this).closest('.single_component').data('comp_key') , false , $(this).closest('div.single_component').attr('class') );
	});

	$(document).on('change' , '.af_cp_select_product' , function(){
		
		

		if ($(this).val()>0) {
			$(this).closest('.single_component').find('.simple_clear_all').show();
		}

		$(this).closest('div.single_component').addClass('af_cp_cursor_wait');
		
		$(this).closest('.single_component').find('.af_cp_component_qty').each(function() {
			let minValue = $(this).attr('min');
			if (minValue !== undefined) {
				$(this).val(minValue);
			}
		});
		
		af_cp_scenario_ajax( $(this).closest('.single_component').data('comp_key') , true , $(this).closest('div.single_component').attr('class') );
	});
	
	$(document).on('click' , '.af-single-component-dropdown' , function(){

        jQuery('.clear_all').show();
		$( "." + $(this).closest('div.af-comp-products-dropdown').data('selected') ).html( $(this).html() );
		$('.af-single-component-dropdown').removeClass('af-selected-product-option');
		$(this).addClass('af-selected-product-option');
		$(".af-comp-products-dropdown").hide();
		$( ".af-selected-product-comp .af-cp-arrow-div").html("&#9660;");
		var product_id = $(this).data('product_id');
		$( "#" + $(this).data('input_name') ).val( product_id );
		$(this).closest('div.single_component').addClass('af_cp_cursor_wait');

		
		$(this).closest('.single_component').find('.af_cp_component_qty').each(function() {
			let minValue = $(this).attr('min');
			if (minValue !== undefined) {
				$(this).val(minValue);
			}
		});
		

		af_cp_scenario_ajax( $(this).closest('.single_component').data('comp_key') , true , $(this).closest('div.single_component').attr('class') );
	});


	
	
	$(document).on('click' , '.af_cp_comp_radio_options' , function(){
		$(this).closest('div.single_component').addClass('af_cp_cursor_wait');
		af_cp_scenario_ajax( $(this).closest('.single_component').data('comp_key') , true , $(this).closest('div.single_component').attr('class') );
	});
	
	// $(document).on('click', '.af_cp_single_radio', function() {
	// 	// Check if the clicked element already has the selected class
	// 	if ($(this).hasClass('af_cp_selected_radio_product_item')) {
	// 		return; // Exit if the class exists
	// 	}
	
	// 	// Remove existing circles
	// 	$('.radio-close-circle').remove();
	// 	$('.radio-open-circle').remove();
	

	// 	if (jQuery('.radio_comp_requrired_' + $(this).closest('.single_component').data('comp_key')).length>0) {
	// 		if (!$(this).find('.radio-open-circle').length) {
	// 			$(this).append('<span class="radio-open-circle">✓</span>');
	// 		}
	// 	} else {
	// 		if (!$(this).find('.radio-close-circle').length) {
	// 			$(this).append('<span class="radio-close-circle">x</span>');
	// 		}
	// 	}
	
	// 	// Remove selected class from all and add to the clicked element
	// 	$('.af_cp_single_radio').removeClass('af_cp_selected_radio_product_item');
	// 	if ($(this).find('input[type=radio]').prop('checked') == false) {
	// 		$(this).addClass('af_cp_selected_radio_product_item');
	// 		$(this).closest('div.single_component').addClass('af_cp_cursor_wait');
	// 		$(this).find('input[type=radio]').prop('checked', true);
	
	// 		af_cp_scenario_ajax(
	// 			$(this).closest('.single_component').data('comp_key'),
	// 			true,
	// 			$(this).closest('div.single_component').attr('class')
	// 		);
	// 	}
	// });
	
	$(document).on('click', '.reset_variations', function() {
		$('div.single_component').addClass('af_cp_cursor_wait');
	
		setTimeout(() => {
			af_cp_scenario_ajax(
				$(this).closest('.single_component').data('comp_key'),
				true,
				$(this).closest('div.single_component').attr('class')
			);
		}, 100);
	
	});
	
	
	$(document).on('show_variation' , 'div.af_cp_variable_product_div' , function(event, data){
		var variation_id = data.variation_id;
		if ( '' != data.image.src ) {
			$(this).closest('div.selected_product').find('img').attr( 'src' , data.image.src );
		}
		$('.af-cp-choose-attr-' + $(this).data('af_cp_key') ).hide();
	});
	$(document).on('hide_variation' , 'div.af_cp_variable_product_div' , function(){
		$('.af-cp-choose-attr-' + $(this).data('af_cp_key') ).show();
	});

	// $(document).on('change', '.af_cp_var_attr_select', function () {
	// 	const $variationTable = $(this).closest('table.variations'); // Get the closest variation table
	// 	const allSelected = $variationTable.find('.af_cp_var_attr_select').toArray().every(select => $(select).val() !== ""); // Check if all select boxes have a value
	
	// 	if (allSelected) {
	// 		$('div.single_component').addClass('af_cp_cursor_wait');
	
	// 		setTimeout(() => {
	// 			af_cp_scenario_ajax(
	// 				$(this).closest('.single_component').data('comp_key'),
	// 				true,
	// 				$(this).closest('div.single_component').attr('class')
	// 			);
	// 		}, 100);
	// 	}
	// });

	$(document).on('change', '.af_cp_var_attr_select', function () {
		const $variationTable = $(this).closest('table.variations'); // Get the closest variation table
		const allSelected = $variationTable.find('.af_cp_var_attr_select').toArray().every(select => $(select).val() !== ""); // Check if all select boxes have a value
	
		// If all select boxes have values OR any select box has no value, trigger the action
		if (allSelected || $(this).val() === "") {
			$('div.single_component').addClass('af_cp_cursor_wait');
	
			setTimeout(() => {
				af_cp_current_scenario_ajax(
					$(this).closest('.single_component').data('comp_key'),
					true,
					$(this).closest('div.single_component').attr('class')
				);
			}, 100);
		}
	});
	
	

	$(document).on('click' , '.af_cp_pagination_div a' , function( event ){
		event.preventDefault();
		var form_data           = $('form.af_cp_cart_form' ).find('input , select').serialize();
		var current_page        = $(this).closest('div.af_cp_pagination_div').find('span.current').text();
		var product_id          = $(this).closest('div.af_cp_pagination_div').data('product_id');
		var comp_key            = $(this).closest('div.af_cp_pagination_div').data('comp_key');
		var selected            = $(this).closest('div.products').find('#select-component-product-' + comp_key ).val();
		var product_div_replace = $(this).closest('div.products');
		var next_page           = $(this).text();
		var type_key            = '';
		if ( $(this).closest('div.products').find('.af_cp_sort_type')[0] ) {
			var type_key = $(this).closest('div.products').find('.af_cp_sort_type').val();
		}
		$(this).closest('.single_component').addClass('af_cp_cursor_wait');
		var order_key = '';
		if ( $(this).closest('div.products').find('.af_cp_sort_order')[0] ) {
			var order_key = $(this).closest('div.products').find('.af_cp_sort_order').val();
		}
		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: { 
				action : 'af_cp_thumbnail_pagination'
				, form_data: form_data
				, current_page: current_page
				, product_id: product_id
				, next_page: next_page
				, comp_key: comp_key
				, order_key: order_key
				, type_key: type_key
				, selected: selected
				, nonce:nonce
			},
			success: function( return_data ){
				$('html, body').animate({
					scrollTop: $(this).closest('.single_component').offset()
					}, 800, function(){
					});
				$('.single_component').removeClass('af_cp_cursor_wait');
				$(product_div_replace).html(return_data);
		

			}
		});
	});
	
	$(document).on('click' , '.af_cp_steps_btn' , function(){
		$('.af_cp_steps_btn').removeClass('af_cp_selected_tab');
		$(this).addClass('af_cp_selected_tab');
		var steps_tabs = $(this).closest('.af_cp_all_components_content').find('div.af_cp_all_comp_tabs .af_cp_steps_btn');
		var this_class = this;
		$.each( steps_tabs , function( div_class , product_html ) {
			if ( $(product_html).data('val') == $(this_class).data('val') ) {
				$(product_html).addClass('af_cp_selected_tab');
			}
		});
		$('.single_component').css('opacity' , 0.5 );
		$('.single_comp_step_review_div').hide();
		$('.single_comp_step').hide();
		$('.single_component').hide();
		$('.single_comp_step_review_btn').show();
		$('.' + $(this).data('val') ).show();
		$('.single_component').css('opacity' , 1 );
		$('html, body').animate({
			scrollTop: $('.af_cp_cart_form' ).offset().top
			}, 800, function(){
			});
	});

	$(document).on('click' , '.single_comp_step_review_btn' , function(){
		var form_data = $('form.af_cp_cart_form' ).find('input , select').serialize();
		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: { 
				action : 'af_cp_review_steps_selection'
				, form_data: form_data
				, product_id: $('.af_cp_current_product_id').val()
				, nonce:nonce
			},
			success: function( return_data ){
				$('.single_comp_step_review_div').show();
				$('.single_comp_step_review_div').html( return_data['data'] );
				$('.single_comp_step').hide();
				$( '.af_cp_steps_div' ).hide();
				$('.single_comp_step_review_btn').hide();
				af_cp_calculate_validate_product( false , '' );
			}
		});
	});
	
	$(document).on('change' , '.af_cp_sort_order , .af_cp_sort_type' , function(){
		var type_key      = $(this).closest('div.af_cp_sort_products').find('.af_cp_sort_type').val();
		var order_key     = $(this).closest('div.af_cp_sort_products').find('.af_cp_sort_order').val();
		var comp_key      = $(this).data('key');
		var replace_class = $(this).data('class_attr');
		$(this).closest('.single_component').addClass('af_cp_cursor_wait');
		var form_data = $('form.af_cp_cart_form' ).find('input , select').serialize();
		jQuery.ajax({
			url: ajaxurl, 
			type: 'POST',
			data: { 
				action : 'af_cp_sort_selection'
				, type_key: type_key
				, order_key: order_key
				, comp_key: comp_key
				, form_data: form_data
				, product_id: $('.af_cp_current_product_id').val()
				, nonce:nonce
			},
			success: function( return_data ){
				$( '.' + replace_class ).html( return_data['data'] );
				$('.single_component').removeClass('af_cp_cursor_wait');
				$('.af_cp_simple_select2').select2();
			}
		});
	});

	
});

