<!DOCTYPE html>
<html>

<head>
  <title>Mapa Regional do Maranhão</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <style>
    #map {
      height: 1000px;

    }
  </style>
</head>

<body>
  <div id="map"></div>
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <script>
    // coordenadas do centro
    const centerLat = -5.291330;
    const centerLng = -45.314664;

    // mapa
    const map = L.map('map').setView([centerLat, centerLng], 7);

    // base do mapa
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const maranhaoBoundary = [
      [-2.0, -45.0],

    ];

    L.polygon(maranhaoBoundary, {
      color: 'blue',
      fillOpacity: 0.1
    }).addTo(map);


    const cities = [{
        name: 'São Luís',
        lat: -2.5307,
        lng: -44.3073
      },
      {
        name: 'Imperatriz',
        lat: -5.5262,
        lng: -47.4712
      },

    ];

    // marcadores  cidade
    cities.forEach(city => {
      L.marker([city.lat, city.lng]).addTo(map).bindPopup(city.name);
    });

    //GeoJSON
    fetch('json/mapcidade.json')
      .then(response => response.json())
      .then(data => {
        const regionalsLayer = L.geoJSON(data, {
          onEachFeature: function(feature, layer) {
            layer.on('click', function() {
              layer.setStyle({
                color: 'red',
                weight: 0.5
              });


              const municipioName = feature.properties.NM_MUN;


              fetchDataFromDB(municipioName, layer);
            });


            layer.on('mouseout', function() {
              layer.setStyle({
                color: 'blue',
                weight: 0.5,
                fillOpacity: 0.1
              });


              layer.closePopup();
            });
          }
        }).addTo(map);
      })
      .catch(error => console.error(error));

    //AJAX
    function fetchDataFromDB(municipioName, layer) {

      const url = `get_data.php?municipioName=${encodeURIComponent(municipioName)}`;
      fetch(url)
        .then(response => response.json())
        .then(data => {

          let popupContent = `<h3>${municipioName}</h3>`;
          popupContent += `<p>População: ${data.mun_pop}</p>`;
          popupContent += `<p>População: ${data.mun_regional}</p>`;



          layer.bindPopup(popupContent).openPopup();
        })
        .catch(error => console.error('Erro na requisição AJAX: ', error));
    }
  </script>
</body>

</html>