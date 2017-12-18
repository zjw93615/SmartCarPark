<div class="row">
  <h1> </h1>
</div>
<div class="row">
  <h1> </h1>
</div>
<div class="row">
  <h1> </h1>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-9 col-md-9">
    <h3>Find Car Parks</h3>
  </div>
  <div class="col-xs-12 col-sm-3 col-md-3">
    <h3>
      <div class="input-group">
        <input id="address" type="text" class="form-control">
        <div class="input-group-btn">
          <button id="submit" type="button" class="btn btn-default">search</button>
        </div>
      </div>
    </h3>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-9 col-md-9">
    <div id="map"></div>
  </div>
  <div class="col-xs-12 col-sm-3 col-md-3">
    <ul class="place-result list-group">

    </ul>
  </div>
</div>
<script>
  var map;
  var bounds;
  var origin;
  var placeMarker = [];

  function initMap() {


    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
    });
    bounds  = new google.maps.LatLngBounds();

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        origin = pos;
        var marker = addMarker(map, origin, '', 15, bounds);
        map.setCenter(pos);
      }, function() {
        console.log("success");
      });
    } else {
      // Browser doesn't support Geolocation
      console.log("failure")
    }

    // var directionsDisplay = new google.maps.DirectionsRenderer({
    //   map: map
    // });
    // var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
    // var markers = locations.map(function(location, i) {
    //   var marker = addMarker(map, location, image, -1, bounds);
    //   marker.addListener('click', function() {

    //     navi(directionsDisplay, origin, marker.getPosition());
    //   });
    // });
    var service = new google.maps.places.PlacesService(map);
    document.getElementById('submit').addEventListener('click', function() {
      geocodeAddress(map, origin, service, bounds);
    });
  }

  function addMarker(map, position, icon, zoom, bounds) {
    var marker = new google.maps.Marker({
          position: position,
          map: map
      });
    if(icon != '') {
     marker.setIcon(icon);
    }
    bounds.extend(position);
    if(zoom == -1) {
      map.fitBounds(bounds);
      map.panToBounds(bounds);
    }else {
      map.setZoom(zoom);
    }
    return marker;
  }

  function geocodeAddress(map, origin, service, bounds) {
    var address = document.getElementById('address').value;
    var request = {
      location: origin,
      radius: 5000,
      keyword: address
    };
    bounds  = new google.maps.LatLngBounds();
    bounds.extend(origin);
    var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var labelIndex = 0;

    service.nearbySearch(request, function(results, status) {
      if (status == google.maps.places.PlacesServiceStatus.OK) {
        cleanPlaceMarker();
        for (var i = 0; i < results.length; i++) {
          var pos = results[i].geometry.location;
          var marker = addMarker(map, pos, '', -1, bounds);
          placeMarker.push(marker);
          //Display the result list
          $('.place-result').append(('<button id="'+i+'"" type="button" class="list-group-item">'+'<p3>'+labels[labelIndex % labels.length]+'. </p3>      '+results[i].name+'</button>'));
          marker.setLabel(labels[labelIndex++ % labels.length]);
          marker.addListener('click', function() {
            var lat = results[i].geometry.location.latitude;
            var lng = results[i].geometry.location.longitude;
            var requestData = {lat: lat, lng: lng};
            $.post("test.php", requestData, function(data) {
              console.log(data);
            });
          });
        }
        $('ul button').on('click', function(e) {
          var i =e.target.id;
          map.setCenter(placeMarker[i].getPosition());
          map.setZoom(15);
        });
      }
    });
  }

  function cleanPlaceMarker() {
    for (var i = 0; i < placeMarker.length; i++) {
      placeMarker[i].setMap(null);
    }
    $('.place-result').empty();
    placeMarker = [];
  }

  function navi(directionsDisplay, origin, destination) {
    // Set destination, origin and travel mode.
    var request = {
      destination: destination,
      origin: origin,
      travelMode: 'DRIVING'
    };

    // Pass the directions request to the directions service.
    var directionsService = new google.maps.DirectionsService();
    directionsService.route(request, function(response, status) {
      if (status == 'OK') {
        // Display the route on the map.
        directionsDisplay.setDirections(response);
      }
    });
  }

  // $(document).ready(function() {
  //   $('#submit').click(function(event) {
  //     var address = $('#address').val();
  //     var lat = origin.lat;
  //     var lng = origin.lng;
  //     var requestData = {address: address, lat: lat, lng: lng};
  //     $.post("test.php", requestData, function(data) {
  //       console.log(data);
  //     });
  //   });
  // });

</script>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBafCANRIdz41dwRNf_GnNVCN4Mbeg3uCw&libraries=places&callback=initMap"
    async defer></script>