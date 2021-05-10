<?php

include("../../../../inc/includes.php");
include("../../../../inc/config.php");
include("../../inc/rel_tickets_reopen.php");

Session::checkLoginUser();
Session::checkRight("profile", READ);

// ------------------- Verificando submit ---------------------------------------

// Obtendo data de pesquisa

// Valores padrões para select - Todos

$saerch = array(
	// Valores padrão para periodo - Primeiro dia do ano ao dia atual
	'data_ini' => $_GET['data_inicial'] ?? date("Y-01-01"),
	'data_fin' => $_GET['data_final'] ?? date("Y-m-d"),
	'id_sel_ent' => 0, // Entidade
	'id_sel_sta' => 0, // Status
	'id_sel_font' => 0, // Fonte
	'id_sel_pri' => 0, // Prioridade
	'id_sel_cat' => 0, // Categoria
	'id_sel_typ' => 0, // Tipo
	'id_sel_due' => 0, // Vencimento
	'id_sel_resolver' => $_GET['sel_resolver'] ?? 0, // Grupo Resolvedor
	'id_sel_operation' => 0, // Operação
	'id_sel_location' => 0, // Localização
	'id_sel_impact' => 0 // impacto
);

// Recebento requisição casa exista alterar os valores
if (!empty($_REQUEST['submit'])) {
	// Date
	$saerch['data_ini'] =  $_REQUEST['data_inicial']; // Data Inicial
	$saerch['data_fin'] = $_REQUEST['data_final']; // Data Final
	// Select
	$saerch['id_sel_ent'] = $_REQUEST['sel_ent']; // Entidade
	$saerch['id_sel_sta'] = $_REQUEST['sel_sta']; // Status
	$saerch['id_sel_font'] = $_REQUEST['sel_font']; // Fonte
	$saerch['id_sel_pri'] = $_REQUEST['sel_pri']; // Prioridade
	$saerch['id_sel_cat'] = $_REQUEST['sel_cat']; // Categoria
	$saerch['id_sel_typ'] = $_REQUEST['sel_typ']; // Tipo
	$saerch['id_sel_due'] = $_REQUEST['sel_due']; // Vencimento
	$saerch['id_sel_resolver'] = $_REQUEST['sel_resolver']; // Grupo Resolvedor
	$saerch['id_sel_operation'] = $_REQUEST['sel_operation']; // Operação
	$saerch['id_sel_location'] = $_REQUEST['sel_location']; // Localização
	$saerch['id_sel_impact'] = $_REQUEST['sel_impact']; // impacto
}

// Beetwen de pesquisa
$saerch["date_beetwen"] = "BETWEEN '" . $saerch['data_ini'] . " 00:00:00' AND '" . $saerch['data_fin'] . " 23:59:59'";

// Objeto da classe de relatório para tickts
$rel_object = new PluginDashboardTicktsReopened($saerch);

$url_limpa = "rel_tickets_reopen.php";

//--------------------------------------- Funções para layout --------------------------------------------------------


function conv_data($data)
{
	if ($data != "") {
		$source = $data;
		$date = new DateTime($source);
		return $date->format('d-m-Y');
	} else {
		return "";
	}
}

function conv_data_hora($data)
{
	if ($data != "") {
		$source = $data;
		$date = new DateTime($source);
		return $date->format('d-m-Y H:i:s');
	} else {
		return "";
	}
}

function dropdown($name, array $options, $selected = null)
{
	/*** begin the select ***/
	$dropdown = '<select style="width: 300px; height: 27px;" autofocus name="' . $name . '" id="' . $name . '">' . "\n";

	$selected = $selected;
	/*** loop over the options ***/
	foreach ($options as $key => $option) {
		/*** assign a selected value ***/
		$select = $selected == $key ? ' selected' : null;
		/*** add each option to the dropdown ***/
		$dropdown .= '<option value="' . $key . '"' . $select . '>' . $option . '</option>' . "\n";
	}
	/*** close the select ***/
	$dropdown .= '</select>' . "\n";

	/*** and return the completed dropdown ***/
	return $dropdown;
}

function margins()
{

	global $DB;
	$query_lay = "SELECT value FROM glpi_plugin_dashboard_config WHERE name = 'layout' AND users_id = " . $_SESSION['glpiID'] . " ";
	$result_lay = $DB->query($query_lay);
	$layout = $DB->result($result_lay, 0, 'value');

	//redirect to index
	if ($layout == '0') {
		// sidebar
		$margin = '0px 5% 0px 5%';
	}

	if ($layout == 1 || $layout == '') {
		//top menu
		$margin = '0px 2% 0px 2%';
	}

	return $margin;
}
// ------------------------------------------------------ Fim das funções para layout -----------------------------------------------------
?>

<html>

<head>
	<title> GLPI - <?php echo __('Tickets', 'dashboard') . ' ' . __('Reabertos', 'dashboard') . ' ' . __('by Group', 'dashboard') . 's'; ?> </title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="content-language" content="en-us" />
	<meta charset="utf-8">

	<link rel="icon" href="../img/dash.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="../img/dash.ico" type="image/x-icon" />
	<link href="../css/styles.css" rel="stylesheet" type="text/css" />
	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="../css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
	<link href="../css/font-awesome.css" type="text/css" rel="stylesheet" />
	<script language="javascript" src="../js/jquery.min.js"></script>
	<link href="../inc/select2/select2.css" rel="stylesheet" type="text/css">
	<script src="../inc/select2/select2.js" type="text/javascript" language="javascript"></script>

	<script src="../js/bootstrap-datepicker.js"></script>
	<link href="../css/datepicker.css" rel="stylesheet" type="text/css">

	<script src="../js/media/js/jquery.dataTables.min.js"></script>
	<link href="../js/media/css/dataTables.bootstrap.css" type="text/css" rel="stylesheet" />
	<script src="../js/media/js/dataTables.bootstrap.js"></script>
	<script src="../js/extensions/Buttons/js/dataTables.buttons.min.js"></script>
	<script src="../js/extensions/Buttons/js/buttons.html5.min.js"></script>
	<script src="../js/extensions/Buttons/js/buttons.bootstrap.min.js"></script>
	<script src="../js/extensions/Buttons/js/buttons.print.min.js"></script>
	<script src="../js/media/pdfmake.min.js"></script>
	<script src="../js/media/vfs_fonts.js"></script>
	<script src="../js/media/jszip.min.js"></script>

	<script src="../js/extensions/Select/js/dataTables.select.min.js"></script>
	<link href="../js/extensions/Select/css/select.dataTables.min.css" type="text/css" rel="stylesheet" />
	<link href="../js/extensions/Select/css/select.bootstrap.css" type="text/css" rel="stylesheet" />

	<style type="text/css">
		select {
			width: 60px;
		}

		table.dataTable {
			empty-cells: show;
		}

		a:link,
		a:visited,
		a:active {
			text-decoration: none;
		}

		a:hover {
			color: #000099;
		}

		.label-md {
			min-width: 45px !important;
			display: inline-block !important
		}

		a.btn>span {
			color: #666;
		}
	</style>

	<?php echo '<link rel="stylesheet" type="text/css" href="../css/style-' . $_SESSION['style'] . '">';  ?>

</head>

<body style="background-color: #e5e5e5; margin-left:0%;">

	<div id='content'>
		<div id='container-fluid' style="margin: <?php echo margins(); ?> ;">
			<div id="charts" class="fluid chart">
				<div id="pad-wrapper">
					<div id="head-rel" class="fluid" style="height: 372px;">
						<a href="../index.php"><i class="fa fa-home home-rel" style="font-size:14pt; margin-left:25px;"></i><span></span></a>
						<div id="titulo_rel"> Chamados Reabertos </div>

						<div id="datas-tec3" class="col-md-12 fluid">
							<form id="form1" name="form1" class="form_rel" method="post" action="rel_tickets_reopen.php" style="margin-left: -12%;">
								<table border="0" cellspacing="0" cellpadding="3" bgcolor="#efefef" class="tab_tickets" width="550">
									<tr>
										<td style="margin-top:2px; width:110px;"><?php echo __('Period'); ?>: </td>
										<td style="width: 200px;">

											<table>
												<tr>
													<td>
														<div class="input-group date" id="dp1" data-date="<?php echo $saerch['data_ini']; ?>" data-date-format="yyyy-mm-dd">
															<input class="col-md-9 form-control" size="13" type="text" name="data_inicial" value="<?php echo $saerch['data_ini']; ?>">
															<span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
														</div>
													</td>
													<td>&nbsp;</td>
													<td>
														<div class="input-group date" id="dp2" data-date="<?php echo $saerch['data_fin']; ?>" data-date-format="yyyy-mm-dd">
															<input class="col-md-9 form-control" size="13" type="text" name="data_final" value="<?php echo $saerch['data_fin']; ?>">
															<span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
														</div>
													</td>
													<td>&nbsp;</td>
												</tr>
											</table>

										</td>
										<td class="separator">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
										<td style="margin-top:2px; width:100px;"><?php echo __('Entity'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// lista de entidades
											$name = 'sel_ent';
											$options = $rel_object->getSelectEntity();
											$selected = $saerch['id_sel_ent'];

											echo dropdown($name, $options, $selected);

											?>

										</td>
										<!-- Input para status -->
										<td class="separator">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
										<td style="margin-top:2px; width:100px;"><?php echo __('Status'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// select Status
											$name = 'sel_sta';
											$options = $rel_object->getSelectStatus();
											$selected = $saerch['id_sel_sta'];

											echo dropdown($name, $options, $selected);

											?>
										</td>
										<!-- Fim input status -->
									</tr>
									<tr>
										<td height="12px"></td>
									</tr>

									<tr>
										<td height="12px"></td>
									</tr>
									<tr>
										<td style="margin-top:2px; width:100px;"><?php echo __('Category'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// Select Categoria
											$name = 'sel_cat';
											$options = $rel_object->getSelectCategory();
											$selected = $saerch['id_sel_cat'];

											echo dropdown($name, $options, $selected);

											?>
										</td>

										<td height="12px"></td>

										<td style="margin-top:2px; width:100px;"><?php echo __('Type'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// Select lista de tipos		
											$name = 'sel_typ';
											$options = $rel_object->getSelectType();
											$selected = $saerch['id_sel_typ'];

											echo dropdown($name, $options, $selected);

											?>

										</td>

										<td height="12px" width="25px"></td>
										<td style="margin-top:2px; width:100px;"><?php echo __('Priority'); ?>: </td>
										<td style="margin-top:2px;">
											<?php
											// SELECT Lista de prioridade
											$name = 'sel_pri';
											$options = $rel_object->getSelectPriority();
											$selected = $saerch['id_sel_pri'];

											echo dropdown($name, $options, $selected);

											?>
										</td>
									</tr>
									<tr>
										<td height="12px"></td>
									</tr>
									<tr>
										<td style="margin-top:2px; width:100px;"><?php echo __('Due Date', 'dashboard'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// Select Vencimento
											$name = 'sel_due';
											$options = $rel_object->getSelectDue();
											$selected = $saerch['id_sel_due'];

											echo dropdown($name, $options, $selected);

											?>

										</td>

										<td height="12px"></td>

										<td style="margin-top:2px; width:165px;"><?php echo __('Source'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// Fonte
											$name = 'sel_font';
											$options = $rel_object->getSelectSource();
											$selected = $saerch['id_sel_font'];

											echo dropdown($name, $options, $selected);

											?>
										</td>
										<!-- Input grupo resolvedor -->
										<td height="12px"></td>
										<td style="margin-top:2px; width:100px;"><?php echo __('Grupo Resolvedor'); ?>: </td>
										<td style="margin-top:2px;">

											<?php

											// Select Resolvedor
											$name = 'sel_resolver';
											$options = $rel_object->getSelectGroupSolver();
											$selected = $saerch['id_sel_resolver'];

											echo dropdown($name, $options, $selected);

											?>

										</td>
										<!-- Fim do input grupo resolvedor -->
									</tr>
									<tr>
										<td height="12px"></td>
									</tr>
									<tr>
										<!-- <td style="margin-top:2px; width:165px;"><?php echo __('Operação'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// Select Operação
											$name = 'sel_operation';
											$options = $rel_object->getSelectOperation();
											$selected = $saerch['id_sel_operation'];

											echo dropdown($name, $options, $selected);

											?>
										</td>
										<td height="12px"></td> -->
										<td style="margin-top:2px; width:100px;"><?php echo __('Location'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// Select localização
											$name = 'sel_location';
											$options = $rel_object->getSelectLocation();
											$selected = $saerch['id_sel_location'];

											echo dropdown($name, $options, $selected);

											?>

										</td>
										<td height="12px"></td>
										<td style="margin-top:2px; width:100px;"><?php echo __('Impact'); ?>: </td>
										<td style="margin-top:2px;">
											<?php

											// Select Impacto
											$name = 'sel_impact';
											$options = $rel_object->getSelectImpact();
											$selected = $saerch['id_sel_impact'];

											echo dropdown($name, $options, $selected);

											?>

										</td>
									</tr>


									<!-- ----------------------- FIM SELECTS ---------------------------------------- -->
									<tr>
										<td height="12px"></td>
									</tr>

									<tr>
										<td colspan="10" align="center" style="padding:15px;">
											<button class="btn btn-primary btn-sm" type="submit" name="submit" value="Atualizar"><i class="fa fa-search"></i>&nbsp; <?php echo __('Consult', 'dashboard'); ?></button>
											<button class="btn btn-primary btn-sm" type="button" name="Limpar" value="Limpar" onclick="location.href='<?php echo $url_limpa ?>'"> <i class="fa fa-trash-o"></i>&nbsp; <?php echo __('Clean', 'dashboard'); ?> </button>
										</td>
										</td>
									</tr>

								</table>
								<?php Html::closeForm(); ?>

								<script language="Javascript">
									$('#dp1').datepicker('update');
									$('#dp2').datepicker('update');
								</script>

						</div>
					</div>
					<div class="well info_box fluid col-md-12 report" style="margin-left: -1px; margin-top: 24px;">
						<?php
						// Quantidade de chamados por status
						$ticket_status_count = $rel_object->getCountState();
						?>

						<table class='fluid' style=' width:100%; font-size: 18px; font-weight:bold;  margin-bottom:25px;  margin-top:20px; ' cellpadding=1px>
							<td style='font-size: 16px; font-weight:bold; vertical-align:middle;'><span style='color:#000;'><?php echo __('Entity', 'dashboard'); ?>: </span><?php echo $rel_object->getSelectEntity($saerch['id_sel_ent']); ?></td>
							<td style='font-size: 16px; font-weight:bold; vertical-align:middle;'><span style='color:#000;'><?php echo __('Tickets', 'dashboard'); ?>: </span><?php echo $ticket_status_count['ticket']; ?></td>
							<td colspan='3' style='font-size: 16px; font-weight:bold; vertical-align:middle; width:200px;'><span style='color:#000;'>
									<?php echo __('Period', 'dashboard'); ?>: </span><?php echo conv_data($saerch['data_ini']) . " a " . conv_data($saerch['data_fin']); ?>
							</td>
							<td>&nbsp;</td>

						</table>

						<table style='font-size: 16px; font-weight:bold; width: 50%;' border=0>
							<tr>
								<td><span style='color: #000;'><?php echo _x('status', 'Novo :'); ?> </span><b><?php echo $ticket_status_count['new']; ?></b></td>
								<td><span style='color: #000;'><?php echo __('Atribuído:'); ?> </span><b><?php echo ($ticket_status_count['assig'] + $ticket_status_count['plan']); ?></b></td>
								<td><span style='color: #000;'><?php echo __('Pendente:'); ?> </span><b><?php echo $ticket_status_count['pend']; ?></b></td>
								<td><span style='color: #000;'><?php echo __('Solucionado:', 'dashboard'); ?> </span><b><?php echo $ticket_status_count['solve']; ?></b></td>
								<td><span style='color: #000;'><?php echo __('Fechado:'); ?> </span><b><?php echo $ticket_status_count['close']; ?></b></td>
								<td><span style='color: #000;'>Porcentagem: </span><b><?php echo number_format($ticket_status_count['porcent'], 2, ".", ",") . '%'; ?></b> <span class="glyphicon glyphicon-question-sign" style="cursor: help;" title="Fórmula = (Chamados reabertos referente a pesquisa / Chamados do tipo Incidente e Requisição com status solucionado ou fechado) * 100"></span></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>

						<table id='ticket' class='display' style='width: 99%; font-size: 11px; font-weight:bold;' cellpadding=2px>
							<thead>
								<tr>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('ID'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Status'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Type'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Source'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Priority'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Category'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Title'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Content'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Requester'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Technician'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Opened', 'dashboard'); ?></th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Closed'); ?> </th>
									<th style='font-size: 12px; text-align: center; cursor:pointer;'><?php echo __('Due Date', 'dashboard'); ?> </th>
								</tr>
							</thead>
							<tbody>

								<?php

								// Tickets a serem consultados
								$tickets = $rel_object->getTickets();

								// While preenchendo tabela
								foreach ($tickets as $ticket) {

									$status = $rel_object->getStatusIcon($ticket['status']);
									$type = $rel_object->getTypeTicketName($ticket['type']);
									$pri = $rel_object->getPriorityName($ticket['priority']);

									$row_req = $rel_object->getOrigemTicket($ticket['id']);
									$row_cat = $rel_object->getCategoryNameTicket($ticket['id']);
									$row_user = $rel_object->getRquerentTicket($ticket['id']);
									$row_tec = $rel_object->getTecnicoTicket($ticket['id']);

									// MONTAGEM DA TABELA
									echo "<tr style='font-weight:normal;'>";
									echo '
										<td style="vertical-align:middle; text-align:center; font-weight:bold;"><a href="' . $CFG_GLPI["url_base"] . '/front/ticket.form.php?id=' . $ticket["id"] . '" target=_blank >' . $ticket["id"] . '</a></td>
										<td style="vertical-align:middle;"><img src="' . $CFG_GLPI["url_base"] . '/pics/' . $status . '.png" title="' . Ticket::getStatus($ticket["status"]) . '" style=" cursor: pointer; cursor: hand;"/>&nbsp;' . Ticket::getStatus($ticket["status"]) . '</td>
										<td style="vertical-align:middle;">' . $type . '</td>
										<td style="vertical-align:middle;">' . $row_req["name"] . '</td>
										<td style="vertical-align:middle;text-align:center;">' . $pri . '</td>
										<td style="vertical-align:middle; max-width:150px;">' . ($row_cat["name"] ?? "Não definido") . '</td>		
										<td style="vertical-align:middle;">' . substr($row_user["title"], 0, 55) . '</td>
										<td style="vertical-align:middle; max-width:550px;">' . html_entity_decode($row_user["content"]) . '</td>
										<td style="vertical-align:middle;">' . $row_user["name"] . ' ' . $row_user["sname"] . '</td>
										<td style="vertical-align:middle;">' . (isset($row_tec["name"]) ? ($row_tec["name"] . ' ' . $row_tec["sname"]) : 'Não atribuido') . '</td>
										<td style="vertical-align:middle; text-align:center;">' . conv_data_hora($ticket["date"]) . '</td>		
										<td style="vertical-align:middle; text-align:center;">' . conv_data_hora($ticket["solvedate"]) . '</td>
										';

									$today = date("Y-m-d H:i:s");

									if ($ticket['solvedate'] > $ticket['time_to_resolve']) {
										echo "<td style='vertical-align:middle; text-align:center; color:red;'> " . conv_data_hora($ticket['time_to_resolve']) . " </td>";
									} else {

										if (!isset($ticket['solvedate']) && $today > $ticket['time_to_resolve']) {
											echo "<td style='vertical-align:middle; text-align:center; color:red;'> " . conv_data_hora($ticket['time_to_resolve']) . " </td>";
										} else {
											echo "<td style='vertical-align:middle; text-align:center; color:green;'> " . conv_data_hora($ticket['time_to_resolve']) . " </td>";
										}
									}

									echo "</tr>";
								}
								// Fim while

								?>

							</tbody>
						</table>
					</div>

					<!-- Script para tabela -->
					<script type="text/javascript" charset="utf-8">
						$('#ticket')
							.removeClass('display')
							.addClass('table table-striped table-bordered table-hover dataTable');

						$(document).ready(function() {
							var table = $('#ticket').dataTable({

								select: true,
								dom: 'Blfrtip',
								filter: false,
								pagingType: "full_numbers",
								sorting: [
									[1, 'desc'],
									[0, 'desc'],
									[2, 'desc'],
									[3, 'desc'],
									[4, 'desc'],
									[5, 'desc'],
									[6, 'desc']
								],
								displayLength: 25,
								lengthMenu: [
									[25, 50, 75, 100],
									[25, 50, 75, 100]
								],
								buttons: [{
										extend: "copyHtml5",
										text: "<?php echo __('Copy'); ?>"
									},
									{
										extend: "collection",
										text: "<?php echo __('Print', 'dashboard'); ?>",
										buttons: [{
												extend: "print",
												autoPrint: true,
												text: "<?php echo __('All', 'dashboard'); ?>",
												message: "<div id='print' class='info_box fluid span12' style='margin-bottom:12px; margin-left: -1px;'></div>"
											},
											{
												extend: "print",
												autoPrint: true,
												text: "<?php echo __('Selected', 'dashboard'); ?>",
												message: "<div id='print' class='info_box fluid span12' style='margin-bottom:12px; margin-left: -1px;'></div>",
												exportOptions: {
													modifier: {
														selected: true
													}
												}
											}
										]
									},
									{
										extend: "collection",
										text: "<?php echo _x('button', 'Export'); ?>",
										buttons: ["csvHtml5", "excelHtml5",
											{
												extend: "pdfHtml5",
												orientation: "landscape",
												message: "<?php echo  __('Period', 'dashboard'); ?> : <?php echo conv_data($saerch['data_ini']); ?> a <?php echo conv_data($saerch['data_fin']); ?>"
											}
										]
									}
								]

							});
						});

						var column = table.column(2);

						$(column.footer()).html(
							column.data().reduce(function(a, b) {
								return a + b;
							})
						);
					</script>

				</div>
			</div>
		</div>
	</div>
	<script type='text/javascript'>
		$(document).ready(function() {
			$("#sel_ent").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_sta").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_font").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_pri").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_cat").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_typ").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_due").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_resolver").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_operation").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_location").select2({
				dropdownAutoWidth: true
			});
		});
		$(document).ready(function() {
			$("#sel_impact").select2({
				dropdownAutoWidth: true
			});
		});
	</script>

</body>

</html>