<html>

<head> <!-- untuk meta description, keywords, dan author bisa gantu dan di sesuaikan tapi yang meta charset sama viewport jangan di ganti -->
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name='description' content='WebGIS info-geospasial.com menyajikan berbagai konten spasial ke dalam bentuk Website'/>
<meta name='keywords' content='WebGIS, WebGIS info-geospasial, WebGIS Indoensia'/>
<meta name='Author' content='Oh Yes'/> 
<link rel="stylesheet" href="css/style.css" />
<link rel="stylesheet" href="css/leaflet.css" /> <!-- memanggil css di folder leaflet -->
<script src="js/leaflet.js"></script> <!-- memanggil leaflet.js di folder leaflet -->
<script src="js/jquery-3.4.1.min.js"></script> <!-- memanggil jquery di folder js -->
<script src="js/leaflet-providers.js"></script> <!-- memanggil leaflet-providers.js di folder leaflet provider -->
<script src="js/Gruntfile.js"></script>
<link rel="stylesheet" href="css/style.css" /> <!-- memanggil css style -->
<link rel="stylesheet" href="leaflet/leaflet-search-master/src/leaflet-search.css"/>
<link rel="stylesheet" href="leaflet/leaflet.defaultextent-master/dist/leaflet.defaultextent.css" />
<script src="leaflet/leaflet-ajax/dist/leaflet.ajax.js"></script>
<script src="leaflet/leaflet-search-master/src/leaflet-search.js"></script>
<script src="leaflet/leaflet.defaultextent-master/dist/leaflet.defaultextent.js"></script>
<title>WebGIS Info-Geospasial</title> <!-- title bisa di sesuaikan dengan nama judul WebGIS yang di inginkan -->
</head>
<body>
<div id="map"> <!-- ini id="map" bisa di ganti dengan nama yang di inginkan -->
<script>
// MENGATUR TITIK KOORDINAT TITIK TENGAN & LEVEL ZOOM PADA BASEMAP
var map = L.map('map').setView([-8.4521135,115.0599022], 5);

// PILIHAN BASEMAP YANG AKAN DITAMPILKAN
var baseLayers = {  
  'Esri.WorldTopoMap': L.tileLayer.provider('Esri.WorldTopoMap').addTo(map),
  'Esri WorldImagery': L.tileLayer.provider('Esri.WorldImagery')
};

// MENAMPILKAN TOOLS UNTUK MEMILIH BASEMAP
L.control.layers(baseLayers,{}).addTo(map);
// MENAMPILKAN SKALA
L.control.scale({imperial: false}).addTo(map);

function getColor(d) {
        return d == 'Regional 1' ? '#003171' :
           d == 'Regional 2' ? '#21abcd' :
           d == 'Regional 3' ? '#cc99cc' :
           d == 'Regional 4' ? '#ef3038' :
           d == 'Regional 5' ? '#ffff33' :
           d == 'Regional 6' ? '#048623' :
    '#dccf98';
    }

function getLine(d) {
        return d == 'Regional 1' ? '#048623' :
           d == 'Regional 2' ? '#ffff33' :
           d == 'Regional 3' ? '#ef3038' :
           d == 'Regional 4' ? '#cc99cc' :
           d == 'Regional 5' ? '#21abcd' :
           d == 'Regional 6' ? '#dccf98' :
    d == 'Regional 7' ? '#003171':
    '#ppppp';
    }
// // ------------------- VECTOR ----------------------------
var layer_ADMINISTRASI = new L.GeoJSON.AJAX("layer/request_telkom.php",{ // sekarang perintahnya diawali dengan variabel
    style: function(feature){
            d = feature.properties.Regional; // perwarnaan objek polygon berdasarkan kode kabupaten di dalam file geojson
            return{
            fillColor: getColor(d),
            fillOpacity: 1,
            color: getLine(d),
            dashArray: '3',
            weight: 1,
            opacity: 1
            }
      },
      onEachFeature: function(feature, layer){
      layer.bindPopup("<center>" + feature.properties.name + "</br>"+ feature.properties.wilayah + "</center>"), // popup yang akan ditampilkan diambil dari filed kab_kot
      that = this; // perintah agar menghasilkan efek hover pada objek layer

            layer.on('mouseover', function (e) {
                this.setStyle({
                weight: 2,
                color: getColor(d),
                dashArray: '',
                fillOpacity: 0.8
                });

            if (!L.Browser.ie && !L.Browser.opera) {
                layer.bringToFront();
            }

                info.update(layer.feature.properties);
            });
            layer.on('mouseout', function (e) {
                layer_ADMINISTRASI.resetStyle(e.target); // isi dengan nama variabel dari layer
                info.update();
            });
    }
    }).addTo(map);
// MENAMBAHKAN TOOL PENCARIAN
var searchControl = new L.Control.Search({
    layer: layer_ADMINISTRASI, // ISI DENGAN ANAM VARIABEL LAYER
    propertyName: 'wilayah', // isi dengan nama field dari file geojson bali yang akan dijadiakn acuan ketiak melakukan pencarian
    HidecircleLocation: false,
    moveToLocation: function(latlng, title, map) {
      //map.fitBounds( latlng.layer.getBounds() );
      var zoom = map.getBoundsZoom(latlng.layer.getBounds());
        map.setView(latlng, zoom); // access the zoom
    }
  });

  searchControl.on('search:locationfound', function(e) {
    
    e.layer.setStyle({});
    if(e.layer._popup)
      e.layer.openPopup();

  }).on('search:collapsed', function(e) {

    featuresLayer.eachLayer(function(feature) {
      featuresLayer.resetStyle(layer);
    }); 
  });
  
  map.addControl( searchControl );  //menambahakn tool pencarian ke tampilan map
  // menambahkan tools defautl extent
  L.control.defaultExtent().addTo(map);

// REQUEST BALI ADMINISTRASI
// $.ajax({ // ini perintah syntax ajax untuk memanggil vektor
//     type: 'POST',
//     url: 'layer/request_telkom.php', // INI memanggil link request_bali yang sebelumnya telah di buat
//     dataType: "json",
//  success: function(response){
//    var data=response; 
//    L.geoJson(data,{
//      style: function(feature){
//     var Style1
//     return { fillColor: getColor(feature.properties.name), weight: 1, opacity: 1 }; // ini adalah style yang akan digunakan
//     },
//       // MENAMPILKAN POPUP DENGAN ISI BERDASARKAN ATRIBUT KAB_KOTA
//       onEachFeature: function( feature, layer ){
//         layer.bindPopup( "<center>" + feature.properties.name + "</br>"+ feature.properties.wilayah + "</center>")
//       }
//       }).addTo(m);  // di akhir selalu di akhiri dengan perintah ini karena objek akan ditambahkan ke map
//     }
// });


// // MENAMBAHKAN TOOL PENCARIAN
// var searchControl = new L.Control.Search({
//     layer: , // ISI DENGAN NANA VARIABEL LAYER
//     propertyName: 'Wilayah', // isi dengan nama field dari file geojson bali yang akan dijadiakn acuan ketiak melakukan pencarian
//     HidecircleLocation: false,
//     moveToLocation: function(latlng, title, m) {
//       //map.fitBounds( latlng.layer.getBounds() );
//       var zoom = m.getBoundsZoom(latlng.layer.getBounds());
//         m.setView(latlng, zoom); // access the zoom
//     }
//   });
//   searchControl.on('search:locationfound', function(e) {   
//     e.layer.setStyle({});
//     if(e.layer._popup)
//       e.layer.openPopup();
//   }).on('search:collapsed', function(e) {
//     featuresLayer.eachLayer(function(layer) {
//       featuresLayer.resetStyle(layer);
//     }); 
//   });  
//   m.addControl( searchControl );  //menambahakn tool pencarian ke tampilan map
//   // menambahkan tools defautl extent
//   L.control.defaultExtent().addTo(m);


</script>
</div> 
<!-- bagian ini akan di isi konten utama -->
</body>
</html>