<!DOCTYPE html>
<html>

<head>
    <title>Mapa Dinâmico do Maranhão</title>
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
        // Coordenadas do centro do estado do Maranhão
        const centerLat = -4.8368;
        const centerLng = -43.4432;

        //  mapa
        const map = L.map('map').setView([centerLat, centerLng], 7);

        // OpenStreetMap
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

        cities.forEach(city => {
            L.marker([city.lat, city.lng]).addTo(map).bindPopup(city.name);
        });

        // GeoJSON
        fetch('json/map.json')
            .then(response => response.json())
            .then(data => {
                const regionalsLayer = L.geoJSON(data, {
                    onEachFeature: function(feature, layer) {

                        layer.on('mouseover', function() {
                            layer.setStyle({
                                color: 'red',
                                weight: 1
                            });


                            const regionalName = feature.properties.NM_MICRO;


                            layer.bindPopup(regionalName).openPopup();
                        });


                        layer.on('mouseout', function() {
                            layer.setStyle({
                                color: 'blue',
                                weight: 1,
                                fillOpacity: 0.1
                            });


                            layer.closePopup();
                        });
                    }
                }).addTo(map);
            })
            .catch(error => console.error(error));
    </script>
</body>

</html>