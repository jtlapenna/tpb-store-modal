import {Geocoder} from "@octolize/flexible-shipping-points-map";

export const geoCodeAddress = async ( address ) => {
    const address_query = address.address_1 + ' ' + ( address.address_2 ?? '' ) + ', ' + address.city + ' ' + address.postcode + ', ' + address.country;

    const hash = address_query.replace(' ', '' );

    const storage_key = 'oct_address_' + hash;

    let coordinates = localStorage.getItem( storage_key );

    if ( coordinates ) {
        return JSON.parse( coordinates );
    } else {
        const geocoding_result = await Geocoder(address_query);
        if (geocoding_result && geocoding_result.length) {
            const lat = geocoding_result[0].lat;
            const lon = geocoding_result[0].lon;
            const coordinates = {
                lat: lat,
                lon: lon,
            };
            localStorage.setItem( storage_key, JSON.stringify( coordinates ) );
            return coordinates;
        } else {
            throw new Error('Geocoding failed for address: ' + address_query);
        }
    }
}
