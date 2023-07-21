<!DOCTYPE html>
<html>

<head>
    <title>Mapa Regional do Maranhão</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        #map {
            height: 600px;
            width: 1160px;
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <h1>Mapa Regional do Maranhão</h1>
        <p>Clique para interagir</p>
        <div id="map"></div>
        <label for="selectJson" class="form-label">Selecione o Modo de Vizualização:</label>
        <select id="selectJson" class="form-select" onchange="loadSelectedJson()">
            <option value="json/mapcidade.json">Cidades</option>
            <option value="json/map.json">Regionais</option>
        </select>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        //  Leaflet

        // coordenadas do centro
        const centerLat = -5.291330;
        const centerLng = -45.314664;

        // mapa
        const map = L.map('map').setView([centerLat, centerLng], 7);

        // base 
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // limite do estado do Maranhão
        const maranhaoBoundary = [
            [-2.0, -45.0],

        ];

        L.polygon(maranhaoBoundary, {
            color: 'blue',
            fillOpacity: 0.1
        }).addTo(map);

        //------------corrigir colchetes das cidades
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

        // marcadores cidade
        cities.forEach(city => {
            L.marker([city.lat, city.lng]).addTo(map).bindPopup(city.name);
        });

        //  AJAX popup
        function fetchDataFromDB(municipioName, layer) {
            const url = `get_data.php?municipioName=${encodeURIComponent(municipioName)}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {

                    if (data && data.mun_pop && data.mun_regional) {

                        let popupContent = `<h6>${municipioName}</h6>`;
                        popupContent += `<p>População: ${data.mun_pop}</p>`;
                        popupContent += `<p>Regional: ${data.mun_regional}</p>`;

                        layer.bindPopup(popupContent).openPopup();
                    } else {

                        layer.bindPopup(`<p>Erro ao carregar dados para ${municipioName}</p>`).openPopup();
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição AJAX: ', error);

                    layer.bindPopup(`<p>Erro ao carregar dados para ${municipioName}</p>`).openPopup();
                });
        }

        // JSON 
        function loadSelectedJson() {
            const selectElement = document.getElementById('selectJson');
            const selectedJson = selectElement.value;

            fetch(selectedJson)
                .then(response => response.json())
                .then(data => {
                    applyGeoJSONToMap(data, selectedJson);
                })
                .catch(error => console.error(error));
        }

        function applyGeoJSONToMap(data, selectedJson) {
            // Limpa camadas - verificar erros dos colchetes das cidades
            map.eachLayer(function(layer) {
                if (layer !== map && !(layer instanceof L.TileLayer)) {
                    map.removeLayer(layer);
                }
            });

            //  GeoJSON
            const newLayer = L.geoJSON(data).addTo(map);

            newLayer.eachLayer(function(layer) {
                layer.on('click', function() {
                    layer.setStyle({
                        color: 'red',
                        weight: 0.5
                    });

                    if (selectedJson === "json/map.json" && layer.feature && layer.feature.properties && layer.feature.properties.NM_MICRO) {

                        const popupContent = `<p>${layer.feature.properties.NM_MICRO}</p>`;
                        layer.bindPopup(popupContent).openPopup();
                    } else if (layer.feature && layer.feature.properties && layer.feature.properties.NM_MUN) {

                        fetchDataFromDB(layer.feature.properties.NM_MUN, layer);
                    }
                });
                layer.on('mouseout', function() {
                    layer.setStyle({
                        color: 'blue',
                        weight: 0.5,
                        fillOpacity: 0.1
                    });


                    layer.closePopup();
                });
            });
        }


        loadSelectedJson();
    </script>
</body>

</html>