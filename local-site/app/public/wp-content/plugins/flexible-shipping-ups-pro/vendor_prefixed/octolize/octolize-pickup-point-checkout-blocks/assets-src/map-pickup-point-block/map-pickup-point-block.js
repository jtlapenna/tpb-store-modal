import { MapPopup, MiniMap } from "@octolize/flexible-shipping-points-map";
import { TranslateProvider } from "@octolize/flexible-shipping-points-map";
import { useEffect, useState, useCallback, useRef } from '@wordpress/element';
import {
	Spinner,
	Button,
	BaseControl,
	RadioControl,
	__experimentalText as Text,
	Flex,
	FlexBlock,
	FlexItem
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch, select } from '@wordpress/data';
import { debounce } from 'lodash';
import { loadNearestPoints } from './load-nearest-points.js';
import { geoCodeAddress } from './geocode-address.js';
import { Fragment } from "react";

const isCollectionPointRate = ( rate ) => {
	return rate.meta_data.reduce( ( collectionPointRate, meta ) => ( meta.key === 'flexible-shipping-pickup-points-map-providers' || collectionPointRate ), false );
}

const shouldShowField = ( selectedRates ) => {
	return selectedRates.reduce( ( selected, rate ) => ( isCollectionPointRate( rate ) || selected ), false );
}

const isRequiredField = ( selectedRates ) => {
	return shouldShowField( selectedRates );
}

const MapPickupPointBlock = ( { checkoutExtensionData, cart, metadata } ) => {

	const { extensionCartUpdate } = wc.blocksCheckout;

	const settings = wcSettings[ metadata.settingsKey ];

	const { extensionData, setExtensionData } = checkoutExtensionData;

	const [ shippingAddress, setShippingAddress ] = useState( cart.shippingAddress );

	const [ showField, setShowField ] = useState( false );

	const [ requiredField, setRequiredField ] = useState( false );

	const [ showMap, setShowMap ] = useState( false );

	const [ selectedRates, setSelectedRates ] = useState( [] );

	const [ providers, setProviders ] = useState( [] );

	const [ pointTypes, setPointTypes ] = useState( [] );

	const [
		pointId,
		setPointId,
	] = useState( wcSettings.checkoutData.extensions[ settings.integrationName ][ settings.fieldName ] ?? '' );

	const [ points, setPoints ] = useState( [] );

	const [ error, setError ] = useState( null );

	const [ loading, setLoading ] = useState( true );

	const [ coordinates, setCoordinates ] = useState( { lat: null, lon: null } );

	const [ currentPoint, setCurrentPoint ] = useState( null );

	const [ mapAddress, setMapAddress ] = useState( '' );

	const [ availableCountries, setAvailableCountries ] = useState( '' );

	const validationErrorId = settings.integrationName + '-' + settings.fieldName;

	const { setValidationErrors, clearValidationError } = useDispatch(
		'wc/store/validation'
	);

	const { __internalIncrementCalculating, __internalDecrementCalculating } = useDispatch( 'wc/store/checkout' );
	const getSelectedRates = () => {
		let selectedRates = [];
		const shippingRates = cart.shippingRates;

		shippingRates.map( ( shipping_package ) => {
			shipping_package.shipping_rates.filter( ( rate ) => {
				if ( rate.selected ) {
					selectedRates.push( rate );
				}
			} );
		} );

		return selectedRates;
	}

	const getSelectedProviders = ( selectedRates ) => {
		let providers = [];

		selectedRates.map( ( rate ) => {
			let rate_providers = [];
			rate.meta_data.map( ( meta ) => {
				if ( meta.key === 'flexible-shipping-pickup-points-map-providers' ) {
					rate_providers.push( meta.value );
				}
			} );
			if ( rate_providers.length > 0 ) {
				providers = rate_providers[ 0 ];
				rate_providers.map( ( single_rate_provider ) => {
					providers = providers.filter( ( provider ) => {
						return single_rate_provider.includes( provider );
					} );
				} )
			}
		} );

		return providers.join( ',' );
	}

	const getSelectedPointTypes = ( selectedRates ) => {
		let point_types = [];

		selectedRates.map( ( rate ) => {
			let rate_point_types = [];
			rate.meta_data.map( ( meta ) => {
				if ( meta.key === 'flexible-shipping-pickup-points-map-point-types' ) {
					rate_point_types.push( meta.value );
				}
			} );
			if ( rate_point_types.length > 0 ) {
				point_types = rate_point_types[ 0 ];
				rate_point_types.map( ( single_rate_point_type ) => {
					point_types = point_types.filter( ( point_type ) => {
						return single_rate_point_type.includes( point_type );
					} );
				} )
			}
		} );

		return point_types.join( ',' );
	}

	useEffect( () => {
		setSelectedRates( getSelectedRates() );
	}, [ ...cart.shippingRates ] );

	useEffect( () => {
		setShowField( shouldShowField( selectedRates ) );
		setRequiredField( isRequiredField( selectedRates ) );

		let selectedProviders = getSelectedProviders( selectedRates );
		if ( selectedProviders !== providers ) {
			setProviders( selectedProviders );
		}

		let selectedPointTypes = getSelectedPointTypes( selectedRates );
		if ( selectedPointTypes !== pointTypes ) {
			setPointTypes( selectedPointTypes );
		}
	}, [ selectedRates ] );

	useEffect( () => {
		setExtensionData( settings.integrationName, settings.fieldName, pointId );

		if ( showField && requiredField && pointId.length === 0 ) {
			setValidationErrors( {
				[ validationErrorId ]: {
					message: fieldLabel,
					hidden: false,
				},
			} );
		} else {
			clearValidationError( validationErrorId );
		}

	}, [ pointId ] );

	useEffect( () => {
		if ( ! showField || ! requiredField ) {
			clearValidationError( validationErrorId );
		}

	}, [ ...cart.shippingRates, requiredField ] );

	const pointIdChanged = ( newPointId ) => {
		setPointId( newPointId );
		setMapAddress( '' );
		setExtensionData( settings.integrationName, settings.fieldName, newPointId );
		__internalIncrementCalculating();
		extensionCartUpdate( {
			namespace: settings.integrationName,
			data: {
				point_id: newPointId,
			},
		} ).finally( () => {
			__internalDecrementCalculating()
		} );
	}

	useEffect( () => {
		let currentPointExists = false;
		points.map( ( point ) => {
			if ( point.id.toString() === pointId.toString() ) {
				setCurrentPoint( point );
				currentPointExists = true;
			}
		} );
		if ( ! currentPointExists ) {
			setCurrentPoint( null );
			setShippingAddress( cart.shippingAddress );
		}
	}, [ points, pointId, providers ] );

	const debouncedLoadNearestPoints = useCallback( debounce( ( coordinates, providers, pointTypes ) => {
		setLoading( true );
		setError( null );
		setPoints( [] );
		if ( coordinates.lat === null || coordinates.lon === null ) {
			setLoading( false );
			return;
		}
		loadNearestPoints( coordinates, providers, pointTypes, nearestPointsLoaded );
	}, 500 ), [] );

	const nearestPointsLoaded = ( points ) => {
		setLoading( false );
		setError( null );
		setPoints( points.slice( 0, settings.pointsLimit ?? 1 ) );
	}

	const nearestPointsLoadedException = ( error ) => {
		setLoading( false );
		setError( error );
	}

	useEffect( () => {
		debouncedLoadNearestPoints( coordinates, providers, pointTypes );
	}, [ providers, pointTypes, coordinates ] );

	const debouncedGeoCodeAddress = useCallback( debounce( ( address ) => {
		setLoading( true );
		setError( null );
		geoCodeAddress( address ).then( ( coordinates ) => {
			setCoordinates( coordinates );
		} ).catch( ( error ) => {
			setError( error );
		} ).finally( () => {
			setLoading( false );
		} );
	}, 500 ), [] );

	useEffect( () => {
		setAvailableCountries( shippingAddress.country );
		let mapAddress = '';
		if ( shippingAddress.address_1 ) {
			mapAddress += shippingAddress.address_1;
			if ( shippingAddress.address_2 ) {
				mapAddress += ' ' + shippingAddress.address_2;
			}
		}
		if ( shippingAddress.city ) {
			mapAddress += ', ' + shippingAddress.city;
			if ( shippingAddress.postcode ) {
				mapAddress += ' ' + shippingAddress.postcode;
			}
		}
		if ( shippingAddress.country ) {
			mapAddress += ', ' + shippingAddress.country;
		}
		setMapAddress( mapAddress.trim() );
		debouncedGeoCodeAddress( shippingAddress );
	}, [ shippingAddress ] );

	useEffect( () => {
		if ( shippingAddress.address_1 !== cart.shippingAddress.address_1
			|| shippingAddress.address_2 !== cart.shippingAddress.address_2
			|| shippingAddress.city !== cart.shippingAddress.city
			|| shippingAddress.postcode !== cart.shippingAddress.postcode
			|| shippingAddress.country !== cart.shippingAddress.country ) {
			setShippingAddress( cart.shippingAddress );
		}
	}, [ cart.shippingAddress ] );

	const pointSelectedCallback = ( point ) => {
		if ( points.filter( ( p ) => p.id === point.id ).length === 0 ) {
			points.pop();
			points.push( point );
		}
		setCurrentPoint( point );
		pointIdChanged( point.id.toString() );
		setShowMap( false );
	}

	const fieldLabel = __( 'Select pickup point', 'octolize-pickup-point-checkout-blocks' );

	return (
		<>
			{ showField &&
				<div className="octolize-pickup-point-block">
					{ settings.displayList &&
						<div className="points-list">
							{ loading && <Spinner/> }
							{ error && <div>{ error.message }</div> }
							<RadioControl
								label={ fieldLabel }
								options={ points.map( ( point ) => {
									return {
										label: point.title + ', ' + point.address + ', ' + point.postCode + ' ' + point.city,
										value: point.id.toString()
									};
								} ) }
								selected={ pointId }
								onChange={ ( value ) => pointIdChanged( value ) }
							/>
						</div>
					}
					{ settings.displayPopup &&
						<div className="points-popup-map">
							<TranslateProvider>
								<MapPopup
									key={ 'flexible-shipping-map-popup' }
									show={ showMap }
									address={ mapAddress }
									lat={ currentPoint?.lat ?? coordinates.lat }
									lng={ currentPoint?.long ?? coordinates.lon }
									currentPoint={ pointId }
									zoom={ pointId ? 18 : 10 }
									availableProviders={ providers }
									availableCountries={ availableCountries }
									availablePointTypes={ pointTypes }
									setShowCallback={ setShowMap }
									pointSelectedCallback={ pointSelectedCallback }
								/>
							</TranslateProvider>
							<Button isDefault onClick={ () => setShowMap( true ) }>{ __( 'Select point from map', 'octolize-pickup-point-checkout-blocks')}</Button>
						</div>
					}
					{ settings.displayMap && currentPoint &&
						<div className="selected-point">
							<BaseControl label={ __( 'Selected point', 'octolize-pickup-point-checkout-blocks' ) }/>
							<Flex className="points-map">
								<FlexItem className='map'>
									<MiniMap pointId={ parseInt( currentPoint.id ) }/>
								</FlexItem>
								<FlexItem className='point-details'>
									<Flex>
										<FlexItem>
											<div className='point-title'>{ currentPoint.title }</div>
											<div className='point-address'>{ currentPoint.address }</div>
											<div
												className='point-postcode'>{ currentPoint.postCode } { currentPoint.city }</div>
											<div className='point-opening-hours'>{ currentPoint.openingHours }</div>
										</FlexItem>
									</Flex>
								</FlexItem>
							</Flex>
						</div>
					}
				</div>
			}
		</>
	);
};

export { MapPickupPointBlock };
