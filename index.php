<!DOCTYPE html>
<html>
<head>
  <title>Rat Density Web Map</title>
  <link rel="stylesheet" href="./css/leaflet.css"/>
  <link rel="stylesheet" href="./css/MarkerCluster.css" />
  <script src="./scripts/leaflet.js"></script>
  <script src="./scripts/leaflet.markercluster.js"></script>
  <script src="./scripts/jquery.js"></script>
  <style>
    html,body,#map{ height: 100% }
  </style>
</head>
<body>

  <div id="map"></div>
  <script>
  //INITIALIZATION OF MAP
  var map = L.map('map').setView([42.35, -71.08], 13);
 
  // LOADING LAYER
  L.tileLayer('http://tiles.mapc.org/basemap/{z}/{x}/{y}.png',
    {
      attribution: 'Tiles by <a href="http://mapc.org">MAPC</a>, Data by <a href="http://mass.gov/mgis">MassGIS</a>',
      maxZoom: 20,
      minZoom: 12
    }).addTo(map);
	
	
  //LOADING DATA  
  $.getJSON("/geojson/neighborhood.geojson",function(hoodData){
    L.geoJson( hoodData, {
      style: function(feature){
        var fillColor,
            density = feature.properties.density;
        if ( density > 80 ) fillColor = "#006837";
        else if ( density > 40 ) fillColor = "#31a354";
        else if ( density > 20 ) fillColor = "#78c679";
        else if ( density > 10 ) fillColor = "#c2e699";
        else if ( density > 0 ) fillColor = "#ffffcc";
        else fillColor = "#f7f7f7";  // no data
        return { color: "#995", weight: 1, fillColor: fillColor, fillOpacity: .3 };
      },
      onEachFeature: function( feature, layer ){
        /*layer.bindPopup( "<strong>" + feature.properties.Name + "</strong><br/>" + feature.properties.density + " rats per square mile" )
         layer.on('click',function(e){
			e.target.editing.enable();
		 
		 });
             */
        
        var input = L.DomUtil.create('input', 'my-input');
   
		input.value = feature.properties.density;
  
		L.DomEvent.addListener(input, 'change', function () {
       
		    //alert(input.value+" - "+feature.properties.Name);
			feature.properties.density = input.value;
			//console.log(feature.properties.Name);
			//console.log(input.value);
			$.post("https://webmap-rat.herokuapp.com/updateGeojson.php", {density : feature.properties.density, name: feature.properties.Name}, function(data){  
				alert("Changed the Value of "+data);
				console.log(data);
			});
			
		});
		layer.bindPopup(input);		
	  }
    }).addTo(map);
  });
 
 //-----------------------------------------LEGEND-------------
 
 
 	function getColor(d) {
		return  d > 80  ? '#006837' :
				d > 40   ? '#FD8D3C' :
				d > 20   ? '#FEB24C' :
				d > 10   ? '#FED976' :
							'#FFEDA0';
	}

	L.LegendControl = L.Control.extend({ 
		onAdd: function (map) {
	
			var div = L.DomUtil.create('div', 'info legend');
			var grades = [0, 10, 20, 40,80];
			var labels = [];
			var from;
			var to;
	
			for (var i = 0; i < grades.length; i++) {
				from = grades[i];
				to = grades[i + 1];
	
				labels.push(
					'<i style="background:' + '#006837' + '"></i> ' +
					from + (to ? '-' + to : '+'));
			}
	
			div.innerHTML = labels.join('<br>');
			return div;
		}
	});

	L.legendControl = function(options) {
	  	return new L.LegendControl(options);
	};

	// Here we are creating control to show it on the map;
	L.legendControl({position: 'topright'}).addTo(map);
 
 
 
 
 //-------------------------------------------------------
 //CREATING CLUSTERS 
	$.getJSON("/geojson/rodents.geoson",function(data){
    var rodents = L.geoJson(data,{
      pointToLayer: function(feature,latlng){
        var marker = L.marker(latlng);
        marker.bindPopup(feature.properties.Location + '<br/>' + feature.properties.OPEN_DT);
        return marker;
      }
    });
    var clusters = L.markerClusterGroup();
    clusters.addLayer(rodents);
    map.addLayer(clusters);
  });
  </script>
</body>
</html>
