<?php

include "../../../../../inc/includes.php";
include "../../../../../inc/config.php";
include "../../inc/functions.php";
include "../../../inc/dash.class.php";

global $DB;

$dashboard = new NewDashboard;

$dados = $_GET;

$row = [];
$row = $dashboard->buscarTickets($dados);


echo json_encode($row);
