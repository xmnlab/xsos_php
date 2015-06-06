jQuery.parseXML = function ( xml ) {
    if( window.ActiveXObject && window.GetObject ) {
        var dom = new ActiveXObject( 'Microsoft.XMLDOM' );
        dom.loadXML( xml );
        return dom;
    }
    if( window.DOMParser )
        return new DOMParser().parseFromString( xml, 'text/xml' );
    throw new Error( 'No XML parser available' );
} 