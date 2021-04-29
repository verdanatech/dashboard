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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <title>GLPI - <?php echo __('Metrics', 'dashboard'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        var themeColour = 'black';
    </script>
    <link href="dash.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet">
    <script src="gauge.min.js"></script>
    <link rel="icon" href="../img/dash.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="../img/dash.ico" type="image/x-icon" />
    <script src="../js/jquery.js"></script>
    <script src="moment.js"></script>
    <script src="gauge.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <link href="../css/datepicker.css" rel="stylesheet" type="text/css">
    <link href="../css/font-awesome.css" type="text/css" rel="stylesheet" />
    <link href="../inc/select2/select2.css" rel="stylesheet" type="text/css">
    <script src="../inc/select2/select2.js" type="text/javascript" language="javascript"></script>
    <script src="../js/themes/dark-unica.js" type="text/javascript"></script>
    <script src="../js/modules/no-data-to-display.js" type="text/javascript"></script>

</head>

<body class="black">

    <a href="../index.php"><i class="fa fa-home" style="font-size:14pt;"></i><span></span></a>

    <h3 style="color:white; font-size:40px; margin-bottom: 41px;" align="center">Dashboard por SLA</h3>

    <div class="container" align="center">
        <table border="0" cellspacing="0" cellpadding="3" bgcolor="#efefef" class="tab_tickets" width="550">
            <tr>
                <?php
                $url = $_SERVER['REQUEST_URI'];
                $arr_url = explode("?", $url);
                $url2 = $arr_url[0];
                ?>

                <td style="width: 200px;">
                    <table>
                        <tr>
                            <td style="width: 1x;"><label for=data1>Data Inicial</label>
                                <div class="input-group date" id="dp1" data-date="<?php echo $data_ini; ?>" data-date-format="yyyy-mm-dd">
                                    <input class="col-md-9 form-control" size="13" type="text" name="date1" value="<?php echo $data_ini; ?>">
                                    <span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
                                </div>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <label for=data2>Data Final</label>
                                <div class="input-group date" id="dp2" data-date="<?php echo $data_fin; ?>" data-date-format="yyyy-mm-dd">
                                    <input class="col-md-9 form-control" size="13" type="text" name="date2" value="<?php echo $data_fin; ?>">
                                    <span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
                                </div>
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                </td>

                <td style="margin-top:2px; width:10px;"></td>
                <td>
                    <label for="select_sla">SLA Tempo de Solução</label>
                    <select id="select_sla" name="sel_sla" class="js-example-responsive js-states" style="width: 180px; text-transform: capitalize; margin-left:20px;">
                        <option>Selecione o SLA</option>
                        <?php
                        $sql_loc = "SELECT id, name AS name
                                    FROM glpi_slas
                                    where type = 0
                                    ORDER BY name ASC ";
                        $result_loc = $DB->query($sql_loc);

                        foreach ($result_loc as $value) {
                            echo "<option style='text-transform: capitalize; '   value=" . $value['id'] . ">" . ($value['name']) . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td class="separator">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="margin-left: 10px;">
                    <label for="type_prioridade">Prioridade</label>
                    <select class="js-example-responsive js-states" name="type_prioridade" id="type_prioridade" style="width:180px;">
                        <option>Selecione a Prioridade</option>
                        <option value="0">Todos</option>
                        <option value="6">Crítica</option>
                        <option value="5">Muito Alta</option>
                        <option value="4">Alta</option>
                        <option value="3">Média</option>
                        <option value="2">Baixa</option>
                        <option value="1">Muito Baixa</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td height="12px"></td>
            </tr>


            <tr>
                <td>
                    <label for="select_groups">Grupo Resolvedor</label>
                    <select id="select_groups" name="sel_gr[]" class="js-example-basic-multiple js-states" multiple="multiple" style="width: 308px;margin-top:4px; text-transform: capitalize;">

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
                            echo "<option style='text-transform: capitalize; '   value=" . $value['id_grupo'] . ">" . ($value['name']) . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; </td>
                <td style="margin-left: 10px;">
                    <label for="type_chamado">Tipo de Chamado</label>
                    <select style="width:180px;margin-top:4px;" class="form-control" name="type_chamado" id="type_chamado">
                        <option>Selecione o tipo do chamado</option>
                        <option value="0">Todos</option>
                        <option value="1">Incidente</option>
                        <option value="2">Requisição</option>
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

    <script type="text/javascript">
        $('#dp1').datepicker('update');
        $('#dp2').datepicker('update');
        $(document).ready(function() {

            $("#sel1").select2({
                dropdownAutoWidth: true
            });

            $("#select_groups").select2({
                placeholder: 'Selecione o Grupo Resolvedor',
                dropdownAutoWidth: true
            });
            $("#type_chamado").select2({
                dropdownAutoWidth: true
            });

            $("#select_sla").select2({
                dropdownAutoWidth: true
            });
            $("#type_prioridade").select2({
                dropdownAutoWidth: true
            });
        });
    </script>

    <div id="graficos" class="hidden container">

        <div class="row">

            <!-- Grafico-row-1 01 -->

            <div id="div_grafic01" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p id="graf1"><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Críticos', 'dashboard'); ?></p>
                </header>
                <a style="text-decoration:none" id="graf01" target="_blank">
                    <div class="content cf-gauge1" id="cf-gauge-1">
                        <div class="val-current">
                            <div class="metric" id="cf-gauge-1-m"></div>
                        </div>
                        <div class="canvas">
                            <canvas height="180" width="285" id="cf-gauge-1-g"></canvas>
                        </div>
                        <div class="val-min">
                            <div class="metric-small" id="cf-gauge-1-a"></div>
                        </div>
                        <div class="val-max">
                            <div class="metric-small" id="cf-gauge-1-b"></div>
                        </div>

                    </div>
            </div>
            </a>

            <!-- Grafico-row-1 02 -->

            <div id="div_grafic02" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p id="graf2"><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Médios', 'dashboard'); ?></p>
                </header>
                <a style="text-decoration:none" id="graf02" target="_blank">
                    <div class="content cf-gauge2" id="cf-gauge-2">
                        <div class="val-current">
                            <div class="metric" id="cf-gauge-2-m"></div>
                        </div>
                        <div class="canvas">
                            <canvas height="180" width="285" id="cf-gauge-2-g"></canvas>
                        </div>
                        <div class="val-min">
                            <div class="metric-small" id="cf-gauge-2-a"></div>
                        </div>
                        <div class="val-max">
                            <div class="metric-small"></div>
                        </div>

                    </div>

            </div>
            </a>

            <!-- Grafico-row-1 03 -->

            <div id="div_grafic03" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p id="graf3"><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Alto', 'dashboard'); ?></p>
                </header>
                <a style="text-decoration:none" id="graf03" target="_blank">
                    <div class="content cf-gauge3" id="cf-gauge-3">
                        <div class="val-current">
                            <div class="metric" id="cf-gauge-3-m"></div>
                        </div>
                        <div class="canvas">
                            <canvas height="180" width="285" id="cf-gauge-3-g"></canvas>
                        </div>
                        <div class="val-min">
                            <div class="metric-small" id="cf-gauge-3-a"></div>
                        </div>
                        <div class="val-max">
                            <div class="metric-small"></div>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <div class="row">

            <!-- Grafico-row-2 01 -->

            <div id="div_grafic04" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p id="graf4"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Baixo', 'dashboard'); ?></p>
                </header>
                <a style="text-decoration:none" id="graf04" target="_blank">
                    <div class="content cf-gauge4" id="cf-gauge-4">
                        <div class="val-current">
                            <div class="metric" id="cf-gauge-4-m"></div>
                        </div>
                        <div class="canvas">
                            <canvas height="180" width="285" id="cf-gauge-4-g"></canvas>
                        </div>
                        <div class="val-min">
                            <div class="metric-small" id="cf-gauge-4-a"> </div>
                        </div>
                        <div class="val-max">
                            <div class="metric-small"></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Grafico-row-2 02 -->

            <div id="div_grafic05" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p style="font-size:14px;" id="graf5"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Requisição', 'dashboard'); ?></p>
                </header>
                <a style="text-decoration:none" id="graf05" target="_blank">
                    <div class="content cf-gauge5" id="cf-gauge-5">
                        <div class="val-current">
                            <div class="metric" id="cf-gauge-5-m"></div>
                        </div>
                        <div class="canvas">
                            <canvas height="180" width="285" id="cf-gauge-5-g"></canvas>
                        </div>
                        <div class="val-min">
                            <div class="metric-small" id="cf-gauge-5-a"> </div>
                        </div>
                        <div class="val-max">
                            <div class="metric-small"></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Grafico-row-2 03 -->

            <div id="div_grafic06" style="margin: 2%; cursor: pointer;" class=" col-md-4 cf-item">
                <header>
                    <p style="font-size:14px;" id="graf6"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Incidente ', 'dashboard'); ?></p>
                </header>
                <a style="text-decoration:none" id="graf06" target="_blank">
                    <div class="content cf-gauge6" id="cf-gauge-6">
                        <div class="val-current">
                            <div class="metric" id="cf-gauge-6-m"></div>
                        </div>
                        <div class="canvas">
                            <canvas height="180" width="285" id="cf-gauge-6-g"></canvas>
                        </div>
                        <div class="val-min">
                            <div class="metric-small" id="cf-gauge-6-a"> </div>
                        </div>
                        <div class="val-max">
                            <div class="metric-small"></div>
                        </div>

                    </div>
                </a>
            </div>
        </div>

    </div>

</body>

</html>

<script>
    function recebeDados() {
        var data1 = $("#dp1 input").val();
        var data2 = $("#dp2 input").val();
        var groups = $("#select_groups").val();
        var chamado = $("#type_chamado").val();
        var sla = $("#select_sla").val();
        var prioridade = $("#type_prioridade").val();

        if (groups == " ") {
            groups = 0;
        }
        if (prioridade == "Selecione a Prioridade") {
            prioridade = 0;
        }
        if (sla == "Selecione o SLA") {
            alert("Selecione o SLA");
            return;
        }
        if (chamado == "Selecione o tipo do chamado") {
            chamado = 0;
        }

        buscarDados(data1, data2, groups, sla, chamado, prioridade);
    }

    function buscarDados(data1, data2, groups, sla, chamado, prioridade) {
        $('#graf01').removeAttr('href');
        $('#graf02').removeAttr('href');
        $('#graf03').removeAttr('href');
        $('#graf04').removeAttr('href');
        $('#graf05').removeAttr('href');
        $('#graf06').removeAttr('href');
        $.ajax({
            url: 'ajax/ajax_dash.php',
            type: 'GET',
            data: {
                data1: data1,
                data2: data2,
                groups: groups,
                sla: sla,
                chamado: chamado,
                prioridade: prioridade
            },
            async: false,
            success: function(response) {

                var grupos = `${groups}`;

                $("#graficos").removeClass("hidden");
                res = JSON.parse(response);
                var critico = parseInt(...res["critico"], 10);
                var medio = parseInt(...res["medio"], 10);
                var alto = parseInt(...res["alto"], 10);
                var baixo = parseInt(...res["baixo"], 10);
                var requisicao = parseInt(...res["requisicao"], 10);
                var incidentes = parseInt(...res["incidente"], 10);
                var total = parseInt(...res["tickets_total"], 10);


                if (groups == null && critico != 0) {
                    $("#graf01").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=6&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                } else if (critico != 0) {
                    $("#graf01").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=6&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                }
                if (groups == null && medio != 0) {
                    $("#graf02").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=&&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=3&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                } else if (medio != 0) {
                    $("#graf02").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=3&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                }
                if (groups == null && alto != 0) {
                    $("#graf03").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=&&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=4&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                } else if (alto != 0) {
                    $("#graf03").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=4&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                }
                if (groups == null && baixo != 0) {
                    $("#graf04").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=2&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                } else if (baixo != 0) {
                    $("#graf04").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=2&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                }
                if (groups == null && requisicao != 0) {
                    $("#graf05").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&criteria[7][link]=AND&criteria[7][field]=14&criteria[7][searchtype]=equals&criteria[7][value]=2&search=Pesquisar&itemtype=Ticket&start=0`);
                } else if (requisicao != 0) {
                    $("#graf05").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&criteria[7][link]=AND&criteria[7][field]=14&criteria[7][searchtype]=equals&criteria[7][value]=2&search=Pesquisar&itemtype=Ticket&start=0`);
                }
                if (groups == null && incidentes != 0) {
                    $("#graf06").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&criteria[7][link]=AND&criteria[7][field]=14&criteria[7][searchtype]=equals&criteria[7][value]=1&search=Pesquisar&itemtype=Ticket&start=0`);
                } else if (incidentes != 0) {
                    $("#graf06").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&criteria[7][link]=AND&criteria[7][field]=14&criteria[7][searchtype]=equals&criteria[7][value]=1&search=Pesquisar&itemtype=Ticket&start=0`);
                }
                var opts = {
                    angle: 0.15,
                    lineWidth: 0.44,
                    radiusScale: 0.90,
                    pointer: {
                        length: 0.48,
                        strokeWidth: 0.035,
                        color: '#f2f2f2'
                    },
                    limitMax: false,
                    limitMin: false,
                    colorStart: '#898989',
                    colorStop: '#15094F',
                    strokeColor: '#898989',
                    generateGradient: false,
                    highDpiSupport: false,
                };

                var target = document.getElementById('cf-gauge-1-g');
                var gauge = new Gauge(target).setOptions(opts);
                gauge.maxValue = total;
                gauge.setMinValue(0);
                gauge.animationSpeed = 32;
                gauge.set(critico);
                document.getElementById("cf-gauge-1-m").innerHTML = critico;
                document.getElementById("cf-gauge-1-a").innerHTML = total;
                var target1 = document.getElementById('cf-gauge-2-g');
                var gauge1 = new Gauge(target1).setOptions(opts);
                gauge1.maxValue = total;
                gauge1.setMinValue(0);
                gauge1.animationSpeed = 32;
                gauge1.set(medio);
                document.getElementById("cf-gauge-2-m").innerHTML = medio;
                document.getElementById("cf-gauge-2-a").innerHTML = total;
                var target2 = document.getElementById('cf-gauge-3-g');
                var gauge2 = new Gauge(target2).setOptions(opts);
                gauge2.maxValue = total;
                gauge2.setMinValue(0);
                gauge2.animationSpeed = 32;
                gauge2.set(alto);
                document.getElementById("cf-gauge-3-m").innerHTML = alto;
                document.getElementById("cf-gauge-3-a").innerHTML = total;
                var target3 = document.getElementById('cf-gauge-4-g');
                var gauge3 = new Gauge(target3).setOptions(opts);
                gauge3.maxValue = total;
                gauge3.setMinValue(0);
                gauge3.animationSpeed = 32;
                gauge3.set(baixo);
                document.getElementById("cf-gauge-4-m").innerHTML = baixo;
                document.getElementById("cf-gauge-4-a").innerHTML = total;
                var target4 = document.getElementById('cf-gauge-5-g');
                var gauge4 = new Gauge(target4).setOptions(opts);
                gauge4.maxValue = total;
                gauge4.setMinValue(0);
                gauge4.animationSpeed = 32;
                gauge4.set(requisicao);
                document.getElementById("cf-gauge-5-m").innerHTML = requisicao;
                document.getElementById("cf-gauge-5-a").innerHTML = total;
                var target5 = document.getElementById('cf-gauge-6-g');
                var gauge5 = new Gauge(target5).setOptions(opts);
                gauge5.maxValue = total;
                gauge5.setMinValue(0);
                gauge5.animationSpeed = 32;
                gauge5.set(incidentes);
                document.getElementById("cf-gauge-6-m").innerHTML = incidentes;
                document.getElementById("cf-gauge-6-a").innerHTML = total;


            },
            error: (error) => {
                console.log(JSON.stringify(error));
            }
        });
    }
</script>