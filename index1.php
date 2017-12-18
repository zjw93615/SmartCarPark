<!DOCTYPE html>
<html>
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style>
       #map {
        height: 400px;
        width: 100%;
       }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>


    <div class="container">
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
        <div class="col-xs-12 col-sm-6 col-md-6">
          <h3>My Google Maps Demo</h3>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
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
        <div class="col-xs-12 col-sm-12 col-md-12">
          <div id="map"></div>
        </div>
      </div>
    </div>
    <script>
      $(document).ready(function() {
        $('#submit').click(function(event) {
          var address = $('#address').val();
          var requestData = {address: address};
          $.post("test.php", requestData, function(data) {
            console.log(data);
          });
        });
      });
      function initMap() {

        var locations = [
            {lat: -37.799928, lng: 144.959526},
            {lat: -37.799900, lng: 144.959408},
            {lat: -37.799894, lng: 144.959295}
          ];

        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 16,
          center: locations[0]
        });
        var bounds  = new google.maps.LatLngBounds();

        var origin;
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var pos = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            origin = pos;
            var marker = addMarker(map, origin, '', bounds);
            map.setCenter(pos);
          }, function() {
            console.log("success");
          });
        } else {
          // Browser doesn't support Geolocation
          console.log("failure")
        }

        var directionsDisplay = new google.maps.DirectionsRenderer({
          map: map
        });
        var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
        var markers = locations.map(function(location, i) {
          var marker = addMarker(map, location, image, bounds);
          marker.addListener('click', function() {

            navi(directionsDisplay, origin, marker.getPosition());
          });
        });
        var service = new google.maps.places.PlacesService(map);
        document.getElementById('submit').addEventListener('click', function() {
          geocodeAddress(map, origin, service, bounds);
        });
      }

      function addMarker(map, position, icon, bounds) {
        var marker = new google.maps.Marker({
              position: position,
              map: map
          });
        if(icon != '') {
         marker.setIcon(icon);
        }
        bounds.extend(position);
        map.fitBounds(bounds);
        map.panToBounds(bounds);
        return marker;
      }

      function geocodeAddress(map, origin, service, bounds) {
        var address = document.getElementById('address').value;
        var request = {
          location: origin,
          radius: 5000,
          keyword: address
        };

        service.nearbySearch(request, function(results, status) {
          if (status == google.maps.places.PlacesServiceStatus.OK) {
            for (var i = 0; i < results.length; i++) {
              var marker = addMarker(map, results[i].geometry.location, '', bounds);
            }
          }
        });
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

    </script>
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script>
    <script async defer
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBafCANRIdz41dwRNf_GnNVCN4Mbeg3uCw&libraries=places&callback=initMap"
        async defer></script>

  </body>
</html>