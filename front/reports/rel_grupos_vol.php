<?php

include("../../../../inc/includes.php");
include("../../../../inc/config.php");
include("../../inc/hist_ticket.php");
include "../inc/functions.php";

global $DB, $con;

Session::checkLoginUser();
Session::checkRight("profile", READ);


if (!empty($_REQUEST['submit'])) {
	$data_ini =  $_REQUEST['date1'];
	$data_fin = $_REQUEST['date2'];
} else {
	$data_ini = date("Y-01-01");
	$data_fin = date("Y-m-d");
}

# entity
$sql_e = "SELECT value FROM glpi_plugin_dashboard_config WHERE name = 'entity' AND users_id = " . $_SESSION['glpiID'] . "";
$result_e = $DB->query($sql_e);
$sel_ent = $DB->result($result_e, 0, 'value');

//select entity
if ($sel_ent == '' || $sel_ent == -1) {

	$query_ent1 = "
	SELECT entities_id
	FROM glpi_users
	WHERE id = " . $_SESSION['glpiID'] . " ";

	$res_ent1 = $DB->query($query_ent1);
	$user_ent = $DB->result($res_ent1, 0, 'entities_id');

	$entities = $_SESSION['glpiactiveentities'];
	$ent = implode(",", $entities);

	$entidade = "AND glpi_tickets.entities_id IN (" . $ent . ") ";
} else {
	$entidade = "AND glpi_tickets.entities_id IN (" . $sel_ent . ") ";
}

?>

<html>

<head>
	<title> GLPI - <?php echo __('Tickets', 'dashboard') . '  ' . __('by Group', 'dashboard') . 's'; ?> </title>
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

		a.btn>span {
			color: #666;
		}

		.label-md {
			min-width: 45px !important;
			display: inline-block !important
		}
	</style>

	<?php echo '<link rel="stylesheet" type="text/css" href="../css/style-' . $_SESSION['style'] . '">';  ?>

</head>

<body style="background-color: #e5e5e5; margin-left:0%;">

	<div id='content'>
		<div id='container-fluid' style="margin: <?php echo margins(); ?> ;">
			<div id="charts" class="fluid chart">
				<div id="pad-wrapper">
					<div id="head-rel" class="fluid">
						<a href="../index.php"><i class="fa fa-home home-rel" style="font-size:14pt; margin-left:25px;"></i><span></span></a>
						<div id="titulo_rel"> <?php echo __('Tickets', 'dashboard') . '  ' . __('by Group', 'dashboard') . 's'; ?> </div>
						<div id="datas-tec" class="span12 fluid">
							<form id="form1" name="form1" class="form_rel" method="post" action="rel_grupos_vol.php?con=1" style="margin-left: 37%;">

								<table border="0" cellspacing="0" cellpadding="3" bgcolor="#efefef">
									<tr>
										<td style="width: 310px;">
											<?php
											$url = $_SERVER['REQUEST_URI'];
											$arr_url = explode("?", $url);
											$url2 = $arr_url[0];

											echo '
												<table>
													<tr>
														<td>
														   <div class="input-group date" id="dp1" data-date="' . $data_ini . '" data-date-format="yyyy-mm-dd">
														    	<input class="col-md-9 form-control" size="13" type="text" name="date1" value="' . $data_ini . '" >		    	
														    	<span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>	    	
													    	</div>
														</td>
														<td>&nbsp;</td>
														<td>
													   	<div class="input-group date" id="dp2" data-date="' . $data_fin . '" data-date-format="yyyy-mm-dd">
														    	<input class="col-md-9 form-control" size="13" type="text" name="date2" value="' . $data_fin . '" >		    	
														    	<span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>	    	
													    	</div>
														</td>
														<td>&nbsp;</td>
													</tr>
												</table> ';
											?>

											<script language="Javascript">
												$('#dp1').datepicker('update');
												$('#dp2').datepicker('update');
											</script>
										</td>

										<td style="margin-top:2px;">

										</td>
									</tr>
									<tr>
										<td height="15px"></td>
									</tr>
									<tr>
										<td colspan="2" align="center">
											<button class="btn btn-primary btn-sm" type="submit" name="submit" value="Atualizar"><i class="fa fa-search"></i>&nbsp; <?php echo __('Consult', 'dashboard'); ?> </button>
											<button class="btn btn-primary btn-sm" type="button" name="Limpar" value="Limpar" onclick="location.href='<?php echo $url2 ?>'"><i class="fa fa-trash-o"></i>&nbsp; <?php echo __('Clean', 'dashboard'); ?> </button>
										</td>
									</tr>
								</table>
								<?php Html::closeForm(); ?>
								<!-- </form> -->
						</div>
					</div>

					<?php

					//tecnico2
					if (isset($_GET['con'])) {

						$con = $_GET['con'];

						if ($con == "1") {

							if (!isset($_REQUEST['date1'])) {
								$data_ini2 = $data_ini;
								$data_fin2 = $data_fin;
							} else {
								$data_ini2 = $_REQUEST['date1'];
								$data_fin2 = $_REQUEST['date2'];
							}

							if ($data_ini2 == $data_fin2) {
								$datas2 = "LIKE '" . $data_ini2 . "%'";
							} else {
								$datas2 = "BETWEEN '" . $data_ini2 . " 00:00:00' AND '" . $data_fin2 . " 23:59:59'";
							}

							//status
							$status_pending = "('4')";
							$status_solved_and_closed = "('5', '6')";
							$status_all = "('1','2','3','4','5','6')";

							//actors - 1 - req, 2 - tec, 3 - observer
							$actors = "";
							$actors_req = "('1')";
							$actors_tec = "('2')";
							$actors_all = "('1','2','3')";

							if (isset($_GET['actor'])) {

								if ($_GET['actor'] == "req") {
									$actors = $actors_req;
								} elseif ($_GET['actor'] == "tec") {
									$actors = $actors_tec;
								} else {
									$actors = $actors_all;
								}
							} else {
								$actors = $actors_all;
							}


							//select groups with tickets
							$sql_tec =
								"SELECT count(glpi_tickets.id) AS total, glpi_groups.name AS name, glpi_groups.id AS id
								 FROM `glpi_groups_tickets`, glpi_tickets, glpi_groups
								 WHERE glpi_groups_tickets.`groups_id` = glpi_groups.id
								 AND glpi_groups_tickets.`tickets_id` = glpi_tickets.id
								 AND glpi_tickets.is_deleted = 0
								 AND glpi_tickets.date " . $datas2 . "
								 " . $entidade . "
								 AND glpi_groups_tickets.type IN " . $actors . "
								 GROUP BY name
								 ORDER BY total DESC ";

							$result_tec = $DB->query($sql_tec);
							$conta_cons = $DB->numrows($result_tec);

							echo "<div class='well info_box fluid col-md-12 report' style='margin-left: -1px;'>";
							echo "
		<table class='col-md-12 right' align='right' style='margin-bottom:20px;'>
				<tr>			
					<td> 
						" . __('Actor') . " : &nbsp;
						<button class='btn btn-primary btn-sm' type='button' name='requerente' value='Requerentes' onclick='location.href=\"rel_grupos_vol.php?con=1&actor=req&date1=" . $data_ini2 . "&date2=" . $data_fin2 . "\"' <i class='icon-white icon-trash'></i> " . __('Requester', 'dashboard') . " </button>
						<button class='btn btn-primary btn-sm' type='button' name='tecnico' value='Técnicos' onclick='location.href=\"rel_grupos_vol.php?con=1&actor=tec&date1=" . $data_ini2 . "&date2=" . $data_fin2 . "\"' <i class='icon-white icon-trash'></i> " . __('Technician', 'dashboard') . " </button>
						<button class='btn btn-primary btn-sm' type='button' name='todos' value='Todos' onclick='location.href=\"rel_grupos_vol.php?con=1&date1=" . $data_ini2 . "&date2=" . $data_fin2 . "\"' <i class='icon-white icon-trash'></i> " . __('All', 'dashboard') . " </button>				
					</td>
				</tr>
		</table> ";

							echo "
			<table id='tec' class='display' style='font-size: 13px; font-weight:bold;' cellpadding = 2px >
				<thead>
					<tr>
						<th style='text-align:center; cursor:pointer;'> " . _n('Group', 'Groups', 2) . " </th>
						<th style='text-align:center; '>" . __('Backlog', 'dashboard') . " </th>
						<th style='text-align:center; cursor:pointer;'> " . __('Backlog DP', 'dashboard') . "</th>
						<th style='text-align:center; cursor:pointer;'> " . __('Backlog FP', 'dashboard') . "</th>
						<th style='text-align:center; cursor:pointer;'> " . __('Pendentes', 'dashboard') . "</th>	
						<th style='text-align:center; cursor:pointer;'> " . __('Solved', 'dashboard') . "</th>									
						<th style='text-align:center; '>" . __('Reabertos', 'dashboard') . "</th> 

						";

							echo "</tr>
				</thead>
			<tbody>";


							while ($id_grp = $DB->fetch_assoc($result_tec)) {

								// Tickets Reabertos
								$sql_tickts_reopened = "SELECT count( glpi_tickets.id ) AS total
											 FROM glpi_groups_tickets, glpi_tickets, glpi_itilsolutions
											 WHERE glpi_tickets.id = glpi_groups_tickets.tickets_id
											 AND glpi_itilsolutions.itemtype = 'Ticket'
											 AND glpi_itilsolutions.items_id = glpi_tickets.id
											 AND glpi_groups_tickets.tickets_id = glpi_tickets.id
											 AND glpi_tickets.is_deleted = 0
											 AND glpi_groups_tickets.groups_id = " . $id_grp['id'] . "
											 AND glpi_tickets.date " . $datas2 . "
											 " . $entidade . " 
											 ORDER BY glpi_tickets.id DESC LIMIT 1";

								$result_tickts_reopened = $DB->query($sql_tickts_reopened) or die("erro_tickt");
								$reabertos = $DB->result($result_tickts_reopened, 0, 'total') + 0;


								//chamados Pendente
								$sql_pending = "SELECT count( glpi_tickets.id ) AS total
											 	 FROM glpi_groups_tickets, glpi_tickets
												 WHERE glpi_tickets.id = glpi_groups_tickets.tickets_id
												 AND glpi_groups_tickets.tickets_id = glpi_tickets.id
												 AND glpi_tickets.is_deleted = 0
												 AND glpi_tickets.status IN " . $status_pending . "
												 AND glpi_groups_tickets.groups_id = " . $id_grp['id'] . "
												 AND glpi_tickets.date " . $datas2 . "
												 " . $entidade . "  
												 ORDER BY glpi_tickets.id DESC LIMIT 1";

								$result_pending = $DB->query($sql_pending) or die("erro_pending");
								$pending = $DB->result($result_pending, 0, 'total') + 0;


								//chamados solucionados
								$sql_sol = "SELECT count( glpi_tickets.id ) AS total
											 FROM glpi_groups_tickets, glpi_tickets
											 WHERE glpi_tickets.id = glpi_groups_tickets.tickets_id
											 AND glpi_groups_tickets.tickets_id = glpi_tickets.id
											 AND glpi_tickets.is_deleted = 0
											 AND glpi_tickets.status IN {$status_solved_and_closed}
											 AND glpi_groups_tickets.groups_id = " . $id_grp['id'] . "
											 AND glpi_tickets.date " . $datas2 . "
											 " . $entidade . " 
											 ORDER BY glpi_tickets.id DESC LIMIT 1";

								$result_sol = $DB->query($sql_sol) or die("erro_ab");
								$solucionados = $DB->result($result_sol, 0, 'total') + 0;


								// backlog geral ----------------------------------------------------------------------------------
								$sql_salved_and_closed = "SELECT count( glpi_tickets.id ) AS total
											FROM glpi_groups_tickets, glpi_tickets
											WHERE glpi_tickets.id = glpi_groups_tickets.tickets_id
											AND glpi_groups_tickets.tickets_id = glpi_tickets.id
											AND glpi_tickets.is_deleted = 0
											AND glpi_tickets.status NOT IN {$status_solved_and_closed}
											AND glpi_groups_tickets.groups_id = " . $id_grp['id'] . "
											" . $entidade . "			
											AND glpi_tickets.date " . $datas2 . "
											ORDER BY glpi_tickets.id DESC LIMIT 1";

								$result_salved_and_closed = $DB->query($sql_salved_and_closed) or die("erro_ab");
								$data_salved_and_closed = $DB->result($result_salved_and_closed, 0, 'total') + 0;


								// Dia de hj para verificar os com tempo de solução em dia ou excedido
								$day = date("Y-m-d H:i:s");


								// backlog DP ---------------------------------------------------------------------------------
								$sql_salved_and_closed_dp = "SELECT count( glpi_tickets.id ) AS total
											FROM glpi_groups_tickets, glpi_tickets
											WHERE glpi_tickets.id = glpi_groups_tickets.tickets_id
											AND glpi_groups_tickets.tickets_id = glpi_tickets.id
											AND glpi_tickets.is_deleted = 0
											AND glpi_tickets.status NOT IN {$status_solved_and_closed}
											AND glpi_tickets.time_to_resolve > '{$day}'
											AND glpi_groups_tickets.groups_id = " . $id_grp['id'] . "
											" . $entidade . "			
											AND glpi_tickets.date " . $datas2 . "
											ORDER BY glpi_tickets.id DESC LIMIT 1";

								$result_salved_and_closed_dp = $DB->query($sql_salved_and_closed_dp) or die("erro_ab");
								$data_salved_and_closed_dp = $DB->result($result_salved_and_closed_dp, 0, 'total') + 0;

								// backlog FP -----------------------------------------------------------------------------------------
								$sql_salved_and_closed_fp = "SELECT count( glpi_tickets.id ) AS total
											FROM glpi_groups_tickets, glpi_tickets
											WHERE glpi_tickets.id = glpi_groups_tickets.tickets_id
											AND glpi_groups_tickets.tickets_id = glpi_tickets.id
											AND glpi_tickets.is_deleted = 0
											AND glpi_tickets.status NOT IN {$status_solved_and_closed}
											AND glpi_tickets.time_to_resolve < '{$day}'
											AND glpi_groups_tickets.groups_id = " . $id_grp['id'] . "
											" . $entidade . "			
											AND glpi_tickets.date " . $datas2 . " 
											ORDER BY glpi_tickets.id DESC LIMIT 1";

								$result_salved_and_closed_fp = $DB->query($sql_salved_and_closed_fp) or die("erro_ab");
								$data_salved_and_closed_fp = $DB->result($result_salved_and_closed_fp, 0, 'total') + 0;

								// MONTAGEM DA TABELA
								echo "
				<tr>
					<td style='vertical-align:middle; text-align:left;'><a href='rel_tecnicos.php?con=1&sel_group=" . $id_grp['id'] . "&date1=" . $data_ini . "&date2=" . $data_fin . "' target='_blank' >" . $id_grp['name'] . ' (' . $id_grp['id'] . ")</a></td>
					<td style='vertical-align:middle; text-align:center;'><a href='" . $CFG_GLPI['url_base'] . "/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=" . $id_grp['id'] . "&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=" . $data_ini . " 00:00:00&criteria[1][value]=" . $data_ini . " 00:00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=" . $data_fin . " 23:59:59&criteria[2][value]=" . $data_fin . " 23:59:59&criteria[3][link]=AND&criteria[3][field]=12&criteria[3][searchtype]=equals&criteria[3][value]=notold&criteria[4][link]=AND&criteria[4][field]=12&criteria[4][searchtype]=equals&criteria[4][value]=notclosed&search=Pesquisar&itemtype=Ticket' target='_blank'>" . $data_salved_and_closed . "</a></td>
					<td style='vertical-align:middle; text-align:center;'><a href='" . $CFG_GLPI['url_base'] . "/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=" . $id_grp['id'] . "&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=" . $data_ini . " 00:00:00&criteria[1][value]=" . $data_ini . " 00:00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=" . $data_fin . " 23:59:59&criteria[2][value]=" . $data_fin . " 23:59:59&criteria[3][link]=AND&criteria[3][field]=82&criteria[3][searchtype]=equals&criteria[3][value]=0&criteria[4][link]=AND&criteria[4][field]=12&criteria[4][searchtype]=equals&criteria[4][value]=notclosed&criteria[5][link]=AND&criteria[5][field]=12&criteria[5][searchtype]=equals&criteria[5][value]=notold&search=Pesquisar&itemtype=Ticket' target='_blank'>" . $data_salved_and_closed_dp . "</a></td>			
					<td style='vertical-align:middle; text-align:center;'><a href='" . $CFG_GLPI['url_base'] . "/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=" . $id_grp['id'] . "&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=" . $data_ini . " 00:00:00&criteria[1][value]=" . $data_ini . " 00:00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=" . $data_fin . " 23:59:59&criteria[2][value]=" . $data_fin . " 23:59:59&criteria[3][link]=AND&criteria[3][field]=82&criteria[3][searchtype]=equals&criteria[3][value]=1&criteria[4][link]=AND&criteria[4][field]=12&criteria[4][searchtype]=equals&criteria[4][value]=notclosed&criteria[5][link]=AND&criteria[5][field]=12&criteria[5][searchtype]=equals&criteria[5][value]=notold&search=Pesquisar&itemtype=Ticket' target='_blank'>" . $data_salved_and_closed_fp . "</a></td>	
					<td style='vertical-align:middle; text-align:center;'><a href='" . $CFG_GLPI['url_base'] . "/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=" . $id_grp['id'] . "&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=" . $data_ini . " 00:00:00&criteria[1][value]=" . $data_ini . " 00:00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=" . $data_fin . " 23:59:59&criteria[2][value]=" . $data_fin . " 23:59:59&criteria[3][link]=AND&criteria[3][field]=12&criteria[3][searchtype]=equals&criteria[3][value]=4&criteria[4][link]=AND&criteria[4][field]=12&criteria[4][searchtype]=equals&criteria[4][value]=notclosed&criteria[5][link]=AND&criteria[5][field]=12&criteria[5][searchtype]=equals&criteria[5][value]=notold&search=Pesquisar&itemtype=Ticket' target='_blank'>" . $pending . "</a></td>
					<td style='vertical-align:middle; text-align:center;'><a href='" . $CFG_GLPI['url_base'] . "/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=" . $id_grp['id'] . "&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=" . $data_ini . " 00:00:00&criteria[1][value]=" . $data_ini . " 00:00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=" . $data_fin . " 23:59:59&criteria[2][value]=" . $data_fin . " 23:59:59&criteria[3][link]=AND&criteria[3][field]=12&criteria[3][searchtype]=equals&criteria[3][value]=old&search=Pesquisar&itemtype=Ticket' target='_blank'>" . $solucionados . "</a></td>			
					<td style='vertical-align:middle; text-align:center;'><a href='" . $CFG_GLPI['url_base'] . "/plugins/dashboard/front/reports/rel_tickets_reopen.php?sel_resolver=" . $id_grp['id'] . "&data_inicial=$data_ini&data_final=$data_fin' target='_blank'>" . $reabertos . "</a></td>					
				   ";

								echo "</tr>";

								//fim while1
							}

							echo "</tbody>
				</table>
				</div>";
							//fim $con
						}
					}

					?>

					<script type="text/javascript" charset="utf-8">
						$('#tec')
							.removeClass('display')
							.addClass('table table-striped table-bordered table-hover dataTable');

						$(document).ready(function() {
							var table = $('#tec').dataTable({

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
												message: "<?php echo  __('Period', 'dashboard'); ?> : <?php echo conv_data($data_ini2); ?> a <?php echo conv_data($data_fin2); ?>"
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

</body>

</html>