let abortController = null;

export const loadOptions = ( { searchString, ajaxUrl, formData, setFilteredOptions, setFilteredValue, setPointId, pointId } ) => {
	if ( abortController !== null ) {
		abortController.abort();
	}
	const abortControllerLocal = new AbortController();

	fetch( ajaxUrl, {
		method: 'POST',
		body: formData,
		signal: abortControllerLocal.signal
	} ).then( function ( response ) {
		if ( response.status === 200 ) {
			return response.json();
		} else {
			throw new Error( 'Invalid response status: ' + response.statusText );
		}
	} ).then( function ( response ) {
		const options = response.items.map( ( item ) => {
			return {
				label: item.text,
				value: String( item.id ),
			}
		} );
		setFilteredOptions( options );
		setFilteredValue( searchString );
		if ( setPointId ) {
			if ( pointId && options.filter( ( option ) => option.value === pointId ).length > 0 ) {
				setPointId( pointId );
			} else {
				setPointId( options[ 0 ].value );
			}
		}
	} ).catch( function ( error ) {
		window.console.log( error );
	} );

}
