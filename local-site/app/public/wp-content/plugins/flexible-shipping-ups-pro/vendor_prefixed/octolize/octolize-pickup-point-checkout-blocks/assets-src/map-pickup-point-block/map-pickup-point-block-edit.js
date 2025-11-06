import { __ } from '@wordpress/i18n';
import {useBlockProps} from '@wordpress/block-editor';
import { Disabled, RadioControl } from '@wordpress/components';

export const MapPickupPointBlockEdit = ( { attributes, setAttributes, metadata } ) => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps} style={{display: 'block'}}>
			<div>
				<div className="wc-block-components-combobox is-active">
				<Disabled>
					<RadioControl
						label={ __( 'Select pickup point', 'octolize-pickup-point-checkout-blocks' ) }
						options={ [
							{ label: __( 'Point 1', 'octolize-pickup-point-checkout-blocks' ), 'value' : '1' },
							{ label: __( 'Point 2', 'octolize-pickup-point-checkout-blocks' ), 'value' : '2' }
						] }
					/>
				</Disabled>
				</div>
			</div>
		</div>
	);
};

export const MapPickupPointBlockSave = ( { attributes } ) => {
	const { text } = attributes;
	return (
		<div { ...useBlockProps.save() }>
		</div>
	);
};
