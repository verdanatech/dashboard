<?php

include "../../../../../inc/includes.php";
include "../../../../../inc/config.php";
include "../../inc/functions.php";
include "../../../inc/dash.class.php";

global $DB;

$dashboard = new NewDashboard;

$dados = $_GET;

$date_ini = DateTime::createFromFormat('d/m/Y', $dados['data1']);
$date_fim = DateTime::createFromFormat('d/m/Y', $dados['data2']);

$dados['data1'] = $date_ini->format('Y-m-d');
$dados['data2'] = $date_fim->format('Y-m-d');

$row = [];
$row = $dashboard->buscarTickets($dados);

$row['data1'] = $date_ini->format('Y-m-d');
$row['data2'] = $date_fim->format('Y-m-d');
echo json_encode($row);
