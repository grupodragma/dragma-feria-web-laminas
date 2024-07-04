window.onload = function() {
  obtenerUbicacionActual();
}

function obtenerUbicacionActual() {

  if (!navigator.geolocation){
    console.log("Su navegador no admite la geolocalización");
    return;
  }

  function success(position) {

    var latlng;
    var latitude  = position.coords.latitude;
    var longitude = position.coords.longitude;

    //console.log('Latitud: ' + latitude + ' Longitud: ' + longitude);
    
    latlng = new google.maps.LatLng(latitude, longitude);

    new google.maps.Geocoder().geocode({'latLng' : latlng}, function(results, status) {

        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                var country = null, countryCode = null, city = null, cityAlt = null;
                var c, lc, component;
                for (var r = 0, rl = results.length; r < rl; r += 1) {
                    var result = results[r];

                    if (!city && result.types[0] === 'locality') {
                        for (c = 0, lc = result.address_components.length; c < lc; c += 1) {
                            component = result.address_components[c];
                            if (component.types[0] === 'locality') {
                                city = component.long_name;
                                break;
                            }
                        }
                    }
                    else if (!city && !cityAlt && result.types[0] === 'administrative_area_level_1') {
                        for (c = 0, lc = result.address_components.length; c < lc; c += 1) {
                            component = result.address_components[c];
                            if (component.types[0] === 'administrative_area_level_1') {
                                cityAlt = component.long_name;
                                break;
                            }
                        }
                    } else if (!country && result.types[0] === 'country') {
                        country = result.address_components[0].long_name;
                        countryCode = result.address_components[0].short_name;
                    }

                    if (city && country) {
                        break;
                    }
                }

                if(document.getElementById('valor_distrito') != null){
                    document.getElementById('valor_distrito').value = city;
                }
                if(document.getElementById('distrito') != null){
                    document.getElementById('distrito').value = city;
                }

            }
        } else {
          console.log("Error: " + status);
        }
    });


  };

  function error() {
    console.log("No se puede recuperar su ubicación");
  };

  navigator.geolocation.getCurrentPosition(success, error);

}