<?php

include "../../../../inc/includes.php";
include "../../../../inc/config.php";
include "../inc/functions.php";
include "../../inc/rel_sla.class.php";
global $DB;

$relatorio = new Relatorio_SLA;
$dados = $_GET;

$row = [];
$row = $relatorio->ticketsSla($dados);






echo json_encode($row);
