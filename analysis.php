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
    <h3>Analysis</h3>
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
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <div id="container"></div>
    <script>
    function plotChart(name, data){
      Highcharts.chart('container', {
          chart: {
              renderTo: 'container',
              type: 'column'
          },
          title: {
              text: 'Frequency of Parking'
          },
          yAxis: {
              title: {
                  text: 'Frequency'
              },
              tickInterval: 1
          },
          xAxis: {
              title: {
                  text: 'Time'
              },
              tickInterval: 1
          },
          legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle'
          },
          series: [{
              name: name,
              data: data
          }],

          responsive: {
              rules: [{
                  condition: {
                      maxWidth: 500
                  },
                  chartOptions: {
                      legend: {
                          layout: 'horizontal',
                          align: 'center',
                          verticalAlign: 'bottom'
                      }
                  }
              }]
          }
      });
    }
    </script>
  </div>
</div>
<script>
  var map;
  var bounds;
  var origin;
  var placeMarker = [];
  var carParkMarker = [];
  var directionsDisplay;
  var infowindow = null;

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
        // console.log("success");
      });
    } else {
      // Browser doesn't support Geolocation
      // console.log("failure")
    }

    directionsDisplay = new google.maps.DirectionsRenderer({
      map: map
    });

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
    resetBounds(true);
    var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var labelIndex = 0;

    service.nearbySearch(request, function(results, status) {
      if (status == google.maps.places.PlacesServiceStatus.OK) {
        cleanAllMarker();
        for (var i = 0; i < results.length; i++) {
          var pos = results[i].geometry.location;
          var image = {
            url: "https://maps.gstatic.com/mapfiles/ms2/micons/yellow.png",
            // This marker is 20 pixels wide by 32 pixels high.
            size: new google.maps.Size(32, 32),
            // The origin for this image is (0, 0).
            origin: new google.maps.Point(0, -5),
          };
          // var image = "https://maps.gstatic.com/mapfiles/ms2/micons/yellow.png";
          var marker = addMarker(map, pos, image, -1, bounds);
          placeMarker.push(marker);
          //Display the result list
          $('.place-result').append(('<button id="'+i+'"" type="button" class="list-group-item">'+'<p3>'+labels[labelIndex % labels.length]+'. </p3>      '+results[i].name+'</button>'));
          marker.setLabel(labels[labelIndex++ % labels.length]);
        }
        var markers = placeMarker.map(function(marker, i) {
          marker.addListener('click', function() {
            getCarParks(marker.getPosition().lat(),marker.getPosition().lng());
            map.setCenter(marker.getPosition());
            map.setZoom(15);
          });
        });
        $('ul button').on('click', function(e) {
          var i =e.target.id;
          map.setCenter(placeMarker[i].getPosition());
          map.setZoom(15);
          getCarParks(placeMarker[i].getPosition().lat(),placeMarker[i].getPosition().lng());
        });
      }
    });
  }

  function cleanAllMarker() {
    cleanPlaceMarker();
    cleanCarParkMarker();
    if(infowindow) {
      infowindow.close();
    }
  }

  function cleanCarParkMarker() {
    if (carParkMarker.length > 0) {
      for (var i = 0; i < carParkMarker.length; i++) {
        carParkMarker[i].setMap(null);
      }
      carParkMarker = [];
    }
    directionsDisplay.setMap(null);

  }

  function cleanPlaceMarker() {
    for (var i = 0; placeMarker.length > 0 && i < placeMarker.length; i++) {
      placeMarker[i].setMap(null);
    }
    $('.place-result').empty();
    placeMarker = [];
    directionsDisplay.setMap(null);
  }

  function navi(directionsDisplay, origin, destination) {
    // Set destination, origin and travel mode.
    directionsDisplay.setMap(map);
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

  function getCarParks(locationLat, locationLng) {
    resetBounds(false);
    var position = {
      lat: locationLat,
      lng: locationLng
    };
    bounds.extend(position);
    cleanCarParkMarker();
    var requestData = {lat: locationLat, lng: locationLng};
    $.post("../assets/php/getAllCarPark.php", requestData, function(data) {
      // console.log(data);
      var obj = $.parseJSON(data);
      var results = obj.results;
      for (var i = 0; i < results.length; i++) {
        var id = results[i].data.id;
        var lat = parseFloat(results[i].data.lat);
        var lng = parseFloat(results[i].data.lng);
        var price = results[i].data.price;
        var pos = new google.maps.LatLng(lat,lng);
        if(results[i].data.vacant == 0) {
          var image = 'https://maps.gstatic.com/mapfiles/ms2/micons/green.png';
        }else {
          var image = 'https://maps.gstatic.com/mapfiles/ms2/micons/red.png';
        }

        var marker = addMarker(map, pos, image, -1, bounds);
        carParkMarker.push(marker);
        addListiener(marker,id);
      }
    });
    $.post("../assets/php/getAreaAnalysisData.php", requestData, function(data) {
      console.log(data);
      var obj = $.parseJSON(data);
      var results = obj.results;
      plotChart("Area Car Parks", results);
    });
  }

  function addListiener(marker,id) {
    marker.addListener('click', function() {
      //navi(directionsDisplay, origin, marker.getPosition());
      if(infowindow) {
        infowindow.close();
      }
      var contentString = '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h3 id="firstHeading" classw="firstHeading">Car Park '+id+'</h3>'+
        '<div id="bodyContent">'+
        '<p><button id="'+ id +'" type="button" class="btn btn-success">Available</button><button id="'+ id +'" type="button" class="btn btn-danger">Parked</button><button id="'+ id +'" type="button" class="btn btn-info">Info</button></p>'+
        '</div>'+
        '</div>';
      infowindow = new google.maps.InfoWindow({
        content: contentString
      });
      infowindow.open(map, marker);

      $('.btn-success').on('click', function(e) {
        var i =e.target.id;
        var requestData = {id: i, state: "available"};
        $.post("../assets/php/setCarPark.php", requestData, function(data) {
          if(data == "Success") {
            marker.setIcon('https://maps.gstatic.com/mapfiles/ms2/micons/green.png')
          }
        });
      });

      $('.btn-danger').on('click', function(e) {
        var i =e.target.id;
        var requestData = {id: i, state: "parked"};
        $.post("../assets/php/setCarPark.php", requestData, function(data) {
          if(data == "Success") {
            marker.setIcon('https://maps.gstatic.com/mapfiles/ms2/micons/red.png')
          }
        });
      });

      $('.btn-info').on('click', function(e) {
        var i =e.target.id;
        var requestData = {id: i};
        $.post("../assets/php/getAnalysisData.php", requestData, function(data) {
          // console.log(data);
          var obj = $.parseJSON(data);
          var results = obj.results;
          plotChart("Car Park " + i, results);
        });
      });
    });
  }

  function resetBounds(includeOrigin) {
    bounds = new google.maps.LatLngBounds();
    if(includeOrigin) {
      bounds.extend(origin);
    }
  }

</script>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBafCANRIdz41dwRNf_GnNVCN4Mbeg3uCw&libraries=places&callback=initMap"
    async defer></script>