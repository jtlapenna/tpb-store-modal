import {LoadPointsFromApi} from "@octolize/flexible-shipping-points-map";

export const loadNearestPoints = ( coordinates, providers, pointTypes, callback ) => {
    const hash = coordinates.lat.toString() + ',' + coordinates.lon.toString() + ',' + providers + ',' + pointTypes;

    const storage_key = 'oct_points_' + hash;
    const storage_key_expiration = 'oct_points_expires_' + hash;

    let points_data = localStorage.getItem( storage_key );
    let expires = localStorage.getItem( storage_key_expiration );
    if ( points_data === '{}' || expires && Date.parse(expires) < Date.now()) {
        points_data = null;
    }

    if ( points_data ) {
        callback( JSON.parse( points_data ) );
    } else {
        LoadPointsFromApi(coordinates.lat, coordinates.lon, providers, '', 10, pointTypes)
			.then(response => {
				if (response.status !== 200) {
					throw new Error('Invalid response');
				}

				return response.json();
			})
			.then(points => {
				callback(points);
			});
    }
}
