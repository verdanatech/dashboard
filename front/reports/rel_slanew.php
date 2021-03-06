<?php

include "../../../../inc/includes.php";
include "../../../../inc/config.php";
include "../inc/functions.php";

global $DB;

Session::checkLoginUser();
Session::checkRight("profile", READ);

if (!empty($_POST['submit'])) {
    $data_ini = $_POST['date1'];
    $data_fin = $_POST['date2'];
} else {
    $data_ini = date("Y-m-01");
    $data_fin = date("Y-m-d");
}
?>

<html>

<head>
    <title> GLPI - <?php echo __('Tickets') . '  ' . __('by SLA', 'dashboard') ?> </title>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <meta http-equiv="content-language" content="en-us" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <script src="../js/highcharts.js" type="text/javascript"></script>
    <script src="../js/highcharts-3d.js" type="text/javascript"></script>

    <script src="../js/extensions/Buttons/js/dataTables.buttons.min.js"></script>
    <script src="../js/extensions/Buttons/js/buttons.html5.min.js"></script>
    <script src="../js/extensions/Buttons/js/buttons.bootstrap.min.js"></script>
    <script src="../js/extensions/Buttons/js/buttons.print.min.js"></script>
    <script src="../js/media/pdfmake.min.js"></script>
    <script src="../js/media/vfs_fonts.js"></script>
    <script src="../js/media/jszip.min.js"></script>


    <script src="../js/extensions/Select/js/dataTables.select.min.js"></script>
    <link href="../js/extensions/Select/css/select.bootstrap.css" type="text/css" rel="stylesheet" />

    <style type="text/css">
        select {
            width: 60px;
        }


        a:link,
        a:visited,
        a:active {
            text-decoration: none;
        }

        a:link,
        a:visited,
        a:active {
            text-decoration: none;
        }

        a:hover {
            color: #000099;
        }

        div#head-lg {
            min-height: 370px;
        }

        #table_painel {
            font-size: 18px;
            font-weight: bold;

        }


        .fa fa-home {
            margin-left: 20px;
        }

        .displayNone {
            display: none;
        }

        label {
            color: #fff;
        }
    </style>

    <?php echo '<link rel="stylesheet" type="text/css" href="../css/style-' . $_SESSION['style'] . '">';  ?>

</head>

<body style="background-color: #e5e5e5;">

    <div id='content'>
        <div id='container-fluid' style="margin: <?php echo margins(); ?> ;">

            <div id="charts" class="fluid chart">
                <div id="pad-wrapper">
                    <div id="head-lg" class="container-fluid">

                        <a href="../index.php"><i class="fa fa-home" style="font-size:14pt;"></i><span></span></a>

                        <div id="titulo_rel">
                            <?php echo __('Relatório', 'dashboard') . '  ' . __('by SLA', 'dashboard') ?> - <?php echo __('Time to resolve'); ?>
                        </div>
                        <div class="container" align="center">

                            <table border="0" cellspacing="0" cellpadding="3" bgcolor="#efefef" class="tab_tickets" width="550">
                                <tr>


                                    <?php
                                    $url = $_SERVER['REQUEST_URI'];
                                    $arr_url = explode("?", $url);
                                    $url2 = $arr_url[0];
                                    ?>
                                    <td style="width: 320px;">
                                        <table>
                                            <tr>
                                                <td>
                                                    <label for=" dp1">Data Inicial</label>
                                                    <div class="input-group date" id="dp1" data-date="<?php echo $data_ini; ?>" data-date-format="yyyy-mm-dd">
                                                        <input class="col-md-9 form-control" size="9" type="text" name="date1" value="<?php echo $data_ini; ?>">
                                                        <span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </td>
                                                <td>&nbsp;</td>
                                                <td>
                                                    <label for="dp2">Data Final</label>
                                                    <div class="input-group date" id="dp2" data-date="<?php echo $data_fin; ?>" data-date-format="yyyy-mm-dd">
                                                        <input class="col-md-9 form-control" size="9" type="text" name="date2" value="<?php echo  $data_fin; ?>">
                                                        <span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>

                                    <script language="Javascript">
                                        $('#dp1').datepicker('update');
                                        $('#dp2').datepicker('update');
                                    </script>

                                    <td style="margin-top:2px; width:10px;"></td>
                                    <td>
                                        <label for="select_sla">SLA Tempo de Solução</label>
                                        <select id="select_sla" name="sel_sla" class="js-example-basic-multiple js-states" style="width: 180px; text-transform: capitalize; margin-top:20px;">
                                            <option>Selecione o SLA</option>
                                            <?php
                                            $sql_loc = "SELECT id, name AS name
                                                        FROM glpi_slas
                                                        where type = 0
                                                        ORDER BY name ASC ";
                                            $result_loc = $DB->query($sql_loc);

                                            foreach ($result_loc as $value) {
                                                echo "<option style='text-transform: capitalize; ' $selected  value=" . $value['id'] . ">" . ($value['name']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="12px"></td>
                                </tr>

                                <tr>
                                    <td style="width: 280px">
                                        <label for="select_groups">Grupo Resolvedor</label>
                                        <select id="select_groups" name="sel_gr[]" class="js-example-basic-multiple js-states" multiple="multiple" style="width: 300px; text-transform: capitalize; margin-top:20px;">
                                            <?php
                                            $sql_tecgrup = "SELECT g.name,g.id as id_grupo
                                                            from glpi_tickets as t
                                                            LEFT JOIN
                                                            glpi_groups_tickets AS gt on (t.id = gt.tickets_id)
                                                            LEFT JOIN glpi_groups AS g on (gt.groups_id = g.id)
                                                            where gt.type = 2
                                                            group by g.id";
                                            $result_group = $DB->query($sql_tecgrup);

                                            foreach ($result_group as $value) {

                                                $selected = (isset($_POST['sel_gr']) && $_POST['sel_gr'] == $value['id_grupo']) ? 'selected' : '';
                                                echo "<option style='text-transform: capitalize; ' $selected  value=" . $value['id_grupo'] . ">" . ($value['name']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td>
                                        <label for="type_impacto">Impacto</label>
                                        <select style="width:180px;margin-top:4px" class="form-control" name="type_impacto" id="type_impacto">
                                            <option>Selecione o Impacto</option>
                                            <option value="0">Todos</option>
                                            <option value="5">Muito Alto</option>
                                            <option value="4">Alto</option>
                                            <option value="3">Médio</option>
                                            <option value="2">Baixo</option>
                                            <option value="1">Muito Baixo</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div style="margin: 10px 0px 40px 0px;">
                            <table style="margin: auto; border-collapse: separate; border-spacing: 10px 5px;">
                                <tr>
                                    <td>
                                        <button class="btn btn-primary btn-sm" type="submit" onclick="recebeDados()">Consultar</button>
                                    </td>
                                    <td>
                                        <button class=" btn btn-primary btn-sm" type="button" name="Limpar" value="Limpar" onclick="location.href='<?php echo $url2 ?>'"> <i class="fa fa-trash-o"></i>&nbsp; <?php echo __('Clean', 'dashboard'); ?> </button>
                                    </td>
                                </tr>
                            </table>
                        </div>






                    </div>
                </div>
            </div>
            <br><br>
            <div id="cont-table" class='well info_box fluid col-md-12 col-sm-12 report displayNone' style='margin-left: -1px; margin-top: -2.5%;'>

                <h2 style="text-align: center; margin-top: 20px; margin-bottom: 30px;">Informações Gráficas de SLAs</h2>

                <div id="grafico"></div>

                <hr style="margin: 60px 0;">

                <h2 style="text-align: center; margin-top: 20px; margin-bottom: 40px;">Informações Tabelada de SLAs</h2>
                <div id="total_cham"></div>
                <table id="table_painel" class='display' style='font-size: 12px; font-weight:bold;'>
                    <thead style="background-color: #373b40; color: #fff;">
                        <th style="text-align: center; vertical-align: middle;">Data</th>
                        <th style="text-align: center; vertical-align: middle;"><b>Dentro do SLA<b></th>
                        <th style="text-align: center; vertical-align: middle;"><b>Fora do SLA<b></th>
                        <th style="text-align: center; vertical-align: middle;"><b>Não Resolvido Dentro do SLA<b></th>
                        <th style="text-align: center; vertical-align: middle;"><b>Não Resolvido Fora do SLA<b></th>
                        <th style="text-align: center; vertical-align: middle;"><b>Pendência Externa<b></th>
                        <th style="text-align: center; vertical-align: middle;"><b>Total de Chamados<b></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>



            <script type="text/javascript">
                $(document).ready(function() {



                    $("#select_groups").select2({
                        placeholder: 'Selecione o Grupo Resolvedor',
                        dropdownAutoWidth: true
                    });
                    $("#select_sla").select2({
                        dropdownAutoWidth: true
                    });

                    $("#type_impacto").select2({

                        dropdownAutoWidth: true
                    });

                    $('#table_painel').hide();

                });

                function recebeDados() {
                    var data1 = $("#dp1 input").val();
                    var data2 = $("#dp2 input").val();
                    var groups = $("#select_groups").val();
                    var sla = $("#select_sla").val();
                    var impacto = $("#type_impacto").val();
                    if (impacto == "Selecione o Impacto") {
                        impacto = 0;
                    }
                    if (groups == "") {
                        groups = 0;
                    }
                    if (impacto == "Selecione o Impacto") {
                        impacto = 0;
                    }
                    if (sla == "Selecione o SLA") {
                        alert("Selecione o SLA");
                        return
                    }

                    buscarDados(data1, data2, groups, impacto, sla);
                }

                function buscarDados(data1, data2, groups, impact, sla) {
                    $('#table_painel').DataTable().destroy();
                    $('#table_painel tbody').empty().append(html);

                    var html = ""


                    $.ajax({
                        url: 'ajax_relatorio.php',
                        type: 'GET',
                        data: {
                            data1: data1,
                            data2: data2,
                            groups: groups,
                            impact: impact,
                            sla: sla
                        },
                        async: false,
                        success: function(response) {

                            res = JSON.parse(response);

                            $('#table_painel').show();
                            $("#cont-table").removeClass("displayNone");
                            $('#total_cham').empty();
                            html += '<h3 style="text-align: center; margin-top: 20px; margin-bottom: 40px;">' + 'Total de Chamados - ' + res['totalcham'] + '</h3>';
                            $(`#total_cham`).append(html);
                            $.each(res["label"], function(i, val) {

                                let data = val.replace("/", "-") + "-" + data1.split("-")[0];

                                var newdate = data.split("-").reverse().join("-");


                                let html = `<tr id="${i}">`;


                                html += `<td style="text-align: center; vertical-align: middle;">${val}</td>`;

                                if (groups != 0) {

                                    // Criando URL para criterios em grupos
                                    let url_criterios_01 = "";
                                    let url_criterios_02 = "";
                                    let url_criterios_03 = "";
                                    let url_criterios_04 = "";
                                    let url_criterios_05 = "";
                                    let url_criterios_07 = "";

                                    $.each(groups, (index, grupo) => {



                                        // Iniciando criterio por laço
                                        url_criterios_01 += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                                        url_criterios_02 += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                                        url_criterios_03 += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                                        url_criterios_04 += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                                        url_criterios_05 += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                                        url_criterios_07 += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;

                                        // -----------------------------------------------------------------------------------------------------

                                        // Criterios para SLA dentro do prazo
                                        url_criterios_01 += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&_select_criteria[${index}][criteria][1][value]=0&_criteria[${index}][criteria][1][value]=${newdate}+00:00&criteria[${index}][criteria][1][value]=${newdate}+00:00:00&criteria[${index}][criteria][2][link]=AND&criteria[${index}][criteria][2][field]=30&criteria[${index}][criteria][2][searchtype]=equals&criteria[${index}][criteria][2][value]=${sla}&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][4][link]=AND&criteria[${index}][criteria][4][field]=15&criteria[${index}][criteria][4][searchtype]=lessthan&_select_criteria[${index}][criteria][4][value]=0&_criteria[${index}][criteria][4][value]=${newdate}+23:59:59&criteria[${index}][criteria][4][value]=${newdate}+23:59:59&search=Pesquisar&itemtype=Ticket&start=0`;

                                        // Criterios para SLA fora do prazo
                                        url_criterios_02 += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&_select_criteria[${index}][criteria][1][value]=0&_criteria[${index}][criteria][1][value]=${newdate}+00:00&criteria[${index}][criteria][1][value]=${newdate}+00:00:00&criteria[${index}][criteria][2][link]=AND&criteria[${index}][criteria][2][field]=30&criteria[${index}][criteria][2][searchtype]=equals&criteria[${index}][criteria][2][value]=${sla}&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=1&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][4][link]=AND&criteria[${index}][criteria][4][field]=15&criteria[${index}][criteria][4][searchtype]=lessthan&_select_criteria[${index}][criteria][4][value]=0&_criteria[${index}][criteria][4][value]=${newdate}+23:59:59&criteria[${index}][criteria][4][value]=${newdate}+23:59:59&search=Pesquisar&itemtype=Ticket&start=0`;

                                        // Criterios para SLA não resolvidos dentro da SLA
                                        url_criterios_03 += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&_select_criteria[${index}][criteria][1][value]=0&_criteria[${index}][criteria][1][value]=${newdate}+00:00&criteria[${index}][criteria][1][value]=${newdate}+00:00:00&criteria[${index}][criteria][2][link]=AND&criteria[${index}][criteria][2][field]=30&criteria[${index}][criteria][2][searchtype]=equals&criteria[${index}][criteria][2][value]=${sla}&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][6][link]=AND&criteria[${index}][criteria][6][field]=8&criteria[${index}][criteria][6][searchtype]=equals&criteria[${index}][criteria][6][value]=${grupo}&criteria[${index}][criteria][4][link]=AND&criteria[${index}][criteria][4][field]=15&criteria[${index}][criteria][4][searchtype]=lessthan&_select_criteria[${index}][criteria][4][value]=0&_criteria[${index}][criteria][4][value]=${newdate}+23:59&criteria[${index}][criteria][4][value]=${newdate}+23:59:59&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=12&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=notold&search=Pesquisar&itemtype=Ticket&start=0`;

                                        // Criterios não resolvido fora do SLA
                                        url_criterios_04 += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&_select_criteria[${index}][criteria][1][value]=0&_criteria[${index}][criteria][1][value]=${newdate}+00:00&criteria[${index}][criteria][1][value]=${newdate}+00:00:00&criteria[${index}][criteria][2][link]=AND&criteria[${index}][criteria][2][field]=30&criteria[${index}][criteria][2][searchtype]=equals&criteria[${index}][criteria][2][value]=${sla}&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=1&criteria[${index}][criteria][6][link]=AND&criteria[${index}][criteria][6][field]=8&criteria[${index}][criteria][6][searchtype]=equals&criteria[${index}][criteria][6][value]=${grupo}&criteria[${index}][criteria][4][link]=AND&criteria[${index}][criteria][4][field]=15&criteria[${index}][criteria][4][searchtype]=lessthan&_select_criteria[${index}][criteria][4][value]=0&_criteria[${index}][criteria][4][value]=${newdate}+23:59&criteria[${index}][criteria][4][value]=${newdate}+23:59:59&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=12&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=notold&search=Pesquisar&itemtype=Ticket&start=0`;

                                        // Criterios Pendência Externa dentro do SLA
                                        url_criterios_05 += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&_select_criteria[${index}][criteria][1][value]=0&_criteria[${index}][criteria][1][value]=${newdate}+00:00:00&criteria[${index}][criteria][1][value]=${newdate}+00:00:00&criteria[${index}][criteria][2][link]=AND&criteria[${index}][criteria][2][field]=30&criteria[${index}][criteria][2][searchtype]=equals&criteria[${index}][criteria][2][value]=${sla}&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][4][link]=AND&criteria[${index}][criteria][4][field]=12&criteria[${index}][criteria][4][searchtype]=equals&criteria[${index}][criteria][4][value]=4&criteria[${index}][criteria][6][link]=AND&criteria[${index}][criteria][6][field]=15&criteria[${index}][criteria][6][searchtype]=lessthan&_select_criteria[${index}][criteria][6][value]=0&_criteria[${index}][criteria][6][value]=${newdate}+23:59:59&criteria[${index}][criteria][6][value]=${newdate}+23:59:59&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=8&criteria[${index}][criteria][7][searchtype]=equals&criteria[${index}][criteria][7][value]=${grupo}&search=Pesquisar&itemtype=Ticket&start=0`;


                                        // Criterios Total de Chamados
                                        url_criterios_07 += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&_select_criteria[${index}][criteria][1][value]=0&_criteria[${index}][criteria][1][value]=${newdate}+00:00:00&criteria[${index}][criteria][1][value]=${newdate}+00:00:00&criteria[${index}][criteria][2][link]=AND&criteria[${index}][criteria][2][field]=30&criteria[${index}][criteria][2][searchtype]=equals&criteria[${index}][criteria][2][value]=${sla}&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=8&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=${grupo}&criteria[${index}][criteria][6][link]=AND&criteria[${index}][criteria][6][field]=15&criteria[${index}][criteria][6][searchtype]=lessthan&_select_criteria[${index}][criteria][6][value]=0&_criteria[${index}][criteria][6][value]=${newdate}+23:59:59&criteria[${index}][criteria][6][value]=${newdate}+23:59:59&search=Pesquisar&itemtype=Ticket&start=0`;

                                    });

                                    // Validação para SLA dentro do prazo
                                    html += `<td style="text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?${url_criterios_01}&search=Pesquisar&itemtype=Ticket" target="_blank"> ${res["dentro"][i]} </a></td>`;

                                    // Validação para SLA fora do prazo
                                    html += `<td style="text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?${url_criterios_02}&search=Pesquisar&itemtype=Ticket" target='_blank'> ${res["fora"][i]} </a></td>`;

                                    // Validação para SLA não resolvidos dentro da SLA
                                    html += `<td style="text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?${url_criterios_03}&search=Pesquisar&itemtype=Ticket" target='_blank'> ${res["NRD"][i]} </a></td>`;

                                    // Validação não resolvido fora do SLA
                                    html += `<td style="text-align: center; vertical-align: middle;"> <a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?${url_criterios_04}&search=Pesquisar&itemtype=Ticket" target='_blank'> ${res["NRF"][i]} </a></td>`;

                                    // Validação Pendência Externa dentro do SLA
                                    html += `<td style="text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?${url_criterios_05}&search=Pesquisar&itemtype=Ticket" target='_blank'> ${res["PED"][i]} </a></td>`;

                                    // Validação Total de Chamados
                                    html += `<td style = "text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?${url_criterios_07}&search=Pesquisar&itemtype=Ticket" target='_blank'> ${res["total"][i]} </a></td>`;


                                } else {
                                    // Validação para SLA dentro do prazo

                                    html += `<td style="text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${newdate}+00:00&criteria[0][value]=${newdate}+00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${newdate}+23:59&criteria[1][value]=${newdate}+23:59&criteria[2][link]=AND&criteria[2][field]=30&criteria[2][searchtype]=equals&criteria[2][value]=${sla}&criteria[3][link]=AND&criteria[3][field]=82&criteria[3][searchtype]=equals&criteria[3][value]=0&search=Pesquisar&itemtype=Ticket&start=0" target="_blank"> ${res["dentro"][i] == null ? 0 : res["dentro"][i] }   </a></td>`;

                                    // Validação para SLA fora do prazo
                                    html += `<td style="text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${newdate}+00:00&criteria[0][value]=${newdate}+00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${newdate}+23:59&criteria[1][value]=${newdate}+23:59&criteria[2][link]=AND&criteria[2][field]=30&criteria[2][searchtype]=equals&criteria[2][value]=${sla}&criteria[3][link]=AND&criteria[3][field]=82&criteria[3][searchtype]=equals&criteria[3][value]=1&search=Pesquisar&itemtype=Ticket&start=0" target="_blank"> ${res["fora"][i]} </a></td>`;

                                    // Validação para SLA não resolvidos dentro da SLA
                                    html += `<td style="text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${newdate}+00:00:00&criteria[0][value]=${newdate}+00:00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${newdate}+23:59:59&criteria[1][value]=${newdate}+23:59:59&criteria[2][link]=AND&criteria[2][field]=82&criteria[2][searchtype]=equals&criteria[2][value]=0&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=12&criteria[4][searchtype]=equals&criteria[4][value]=notold&search=Pesquisar&itemtype=Ticket&start=0" target='_blank'> ${res["NRD"][i]} </a></td>`;

                                    // Validação não resolvido fora do SLA
                                    html += `<td style="text-align: center; vertical-align: middle;"> <a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${newdate}+00:00:00&criteria[0][value]=${newdate}+00:00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${newdate}+23:59:59&criteria[1][value]=${newdate}+23:59:59&criteria[2][link]=AND&criteria[2][field]=82&criteria[2][searchtype]=equals&criteria[2][value]=1&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=12&criteria[4][searchtype]=equals&criteria[4][value]=notold&search=Pesquisar&itemtype=Ticket&start=0" target='_blank'> ${res["NRF"][i]} </a></td>`;

                                    // Validação Pendência Externa dentro do SLA
                                    html += `<td style="text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${newdate}+00:00:00&criteria[0][value]=${newdate}+00:00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${newdate}+23:59:59&criteria[1][value]=${newdate}+23:59:59&criteria[2][link]=AND&criteria[2][field]=30&criteria[2][searchtype]=equals&criteria[2][value]=${sla}&criteria[3][link]=AND&criteria[3][field]=12&criteria[3][searchtype]=equals&criteria[3][value]=4&search=Pesquisar&itemtype=Ticket&start=0" target='_blank'> ${res["PED"][i]} </a></td>`;

                                    // Validação Total de Chamados
                                    html += `<td style = "text-align: center; vertical-align: middle;"><a href="<?php echo $CFG_GLPI['url_base']; ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${newdate}+00:00:00&criteria[0][value]=${newdate}+00:00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${newdate}+23:59:59&criteria[1][value]=${newdate}+23:59:59&criteria[2][link]=AND&criteria[2][field]=30&criteria[2][searchtype]=equals&criteria[2][value]=${sla}&search=Pesquisar&itemtype=Ticket&start=0" target='_blank'> ${res["total"][i]} </a></td>`;
                                }

                                html += `</tr>`;
                                $(`#table_painel`).append(html);
                            });

                            $('#table_painel')
                                .removeClass('display')
                                .addClass('table table-striped table-bordered table-hover dataTable');

                            jQuery.extend(jQuery.fn.dataTableExt.oSort, {
                                "date-br-pre": function(a) {
                                    if (a == null || a == "") {
                                        return 0;
                                    }
                                    var brDatea = a.split('/');
                                    return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
                                },

                                "date-br-asc": function(a, b) {
                                    return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                                },

                                "date-br-desc": function(a, b) {
                                    return ((a < b) ? 1 : ((a > b) ? -1 : 0));
                                }
                            });
                            $('#table_painel').DataTable({
                                select: false,
                                dom: 'Blfrtip',
                                filter: false,
                                pagingType: "full_numbers",
                                deferRender: true,
                                sorting: [
                                    [0, 'asc'],

                                ],
                                displayLength: 10,
                                lengthMenu: [
                                    [10, 25, 50, 100],
                                    [10, 25, 50, 100]
                                ],
                                columnDefs: [{
                                    type: 'date-br',
                                    targets: 0
                                }],
                                language: {
                                    url: '../lib/portuguese-datatable.json'
                                },
                                buttons: [{
                                        extend: "collection",
                                        text: "<?php echo __('Print', 'dashboard'); ?>",
                                        buttons: [{
                                            extend: "print",
                                            autoPrint: true,
                                            text: "<?php echo __('All', 'dashboard'); ?>",
                                        }]
                                    },
                                    {
                                        extend: "collection",
                                        text: "<?php echo _x('button', 'Export'); ?>",
                                        buttons: ["csvHtml5", "excelHtml5",
                                            {
                                                extend: "pdfHtml5",
                                                orientation: "landscape",
                                                message: "Relatório por SLA - Tempo para solução",
                                            }
                                        ]
                                    }
                                ]

                            });

                            res["dentro_sla"] = res["dentro"].map(i => Number(i));
                            res["fora_sla"] = res["fora"].map(i => Number(i));
                            res["nrd_sla"] = res["NRD"].map(i => Number(i));
                            res["nrf_sla"] = res["NRF"].map(i => Number(i));
                            res["ped_sla"] = res["PED"].map(i => Number(i));


                            Highcharts.chart('grafico', {

                                title: {
                                    text: 'SLAS'
                                },

                                subtitle: {
                                    text: 'Chamados Por SLA'
                                },

                                yAxis: {
                                    title: {
                                        text: 'Número de Chamados'
                                    }
                                },

                                xAxis: {


                                    categories: [...res["label"]]

                                },

                                legend: {
                                    layout: 'vertical',
                                    align: 'right',
                                    verticalAlign: 'middle'
                                },

                                plotOptions: {
                                    series: {}
                                },

                                series: [{

                                    name: 'Dentro do SLA',
                                    data: [...res["dentro_sla"]]
                                }, {
                                    name: 'Fora do SLA',
                                    data: [...res["fora_sla"]]
                                }, {
                                    name: 'Não Resolvido Dentro do SLA',
                                    data: [...res["nrd_sla"]]
                                }, {
                                    name: 'Não Resolvido Fora do SLA',
                                    data: [...res["nrf_sla"]]

                                }, {
                                    name: 'Pendência Externa',
                                    data: [...res["ped_sla"]]
                                }],

                                responsive: {
                                    rules: [{
                                        condition: {
                                            maxWidth: 500
                                        },
                                        chartOptions: {
                                            legend: {
                                                layout: 'horizontal',
                                                align: 'center',
                                                verticalAlign: 'bottom'
                                            }
                                        }
                                    }]
                                }

                            });



                        }
                    });
                };
            </script>
        </div>
    </div>

</body>

</html>