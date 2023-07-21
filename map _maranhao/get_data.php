<?php
include 'conexao.php';


if (isset($_GET['municipioName'])) {
    $municipioName = $_GET['municipioName'];

    $sql = "SELECT * FROM municipios WHERE mun_municipio = '$municipioName'";
    $busca = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($busca) > 0) {

        $dadosMunicipio = mysqli_fetch_assoc($busca);

        header('Content-Type: application/json');
        echo json_encode($dadosMunicipio);
    } else {

        echo 'Município não encontrado.';
    }
}
