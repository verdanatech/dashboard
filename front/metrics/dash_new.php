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
                    <label for="select_sla">Sla Tempo de Solução</label>
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
                    <label for="type_impacto">Impacto</label>
                    <select class="js-example-responsive js-states" name="type_impacto" id="type_impacto" style="width:180px;">
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
            <tr>
                <td height="12px"></td>
            </tr>


            <tr>
                <td>
                    <label for="select_groups">Grupo Resolvedor</label>
                    <select id="select_groups" name="sel_gr[]" class="js-example-basic-multiple js-states" multiple="multiple" style="width: 308px;margin-top:4px; text-transform: capitalize;">
                        <option value="0">Todos</option>
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
            $("#type_impacto").select2({
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
                <div class="content cf-gauge1" id="cf-gauge-1">
                    <div class="val-current">
                        <div class="metric" id="cf-gauge-1-m"></div>
                    </div>
                    <div class="canvas">
                        <canvas height="180" width="285" id="cf-gauge-1-g"></canvas>
                    </div>
                    <div class="val-min">
                        <div class="metric-small"></div>
                    </div>
                    <div class="val-max">
                        <div class="metric-small"></div>
                    </div>

                </div>
            </div>

            <!-- Grafico-row-1 02 -->
            <div id="div_grafic02" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p id="graf2"><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Médios', 'dashboard'); ?></p>
                </header>
                <div class="content cf-gauge2" id="cf-gauge-2">
                    <div class="val-current">
                        <div class="metric" id="cf-gauge-2-m"></div>
                    </div>
                    <div class="canvas">
                        <canvas height="180" width="285" id="cf-gauge-2-g"></canvas>
                    </div>
                    <div class="val-min">
                        <div class="metric-small"></div>
                    </div>
                    <div class="val-max">
                        <div class="metric-small"></div>
                    </div>

                </div>
            </div>

            <!-- Grafico-row-1 03 -->
            <div id="div_grafic03" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p id="graf3"><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Alto', 'dashboard'); ?></p>
                </header>
                <div class="content cf-gauge3" id="cf-gauge-3">
                    <div class="val-current">
                        <div class="metric" id="cf-gauge-3-m"></div>
                    </div>
                    <div class="canvas">
                        <canvas height="180" width="285" id="cf-gauge-3-g"></canvas>
                    </div>
                    <div class="val-min">
                        <div class="metric-small"></div>
                    </div>
                    <div class="val-max">
                        <div class="metric-small"></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">

            <!-- Grafico-row-2 01 -->
            <div id="div_grafic04" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p id="graf4"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Baixo', 'dashboard'); ?></p>
                </header>
                <div class="content cf-gauge4" id="cf-gauge-4">
                    <div class="val-current">
                        <div class="metric" id="cf-gauge-4-m"></div>
                    </div>
                    <div class="canvas">
                        <canvas height="180" width="285" id="cf-gauge-4-g"></canvas>
                    </div>
                    <div class="val-min">
                        <div class="metric-small"></div>
                    </div>
                    <div class="val-max">
                        <div class="metric-small"></div>
                    </div>
                </div>
            </div>

            <!-- Grafico-row-2 02 -->
            <div id="div_grafic05" style="margin: 2%; cursor: pointer;" class="col-md-4 cf-item">
                <header>
                    <p style="font-size:14px;" id="graf5"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Requisição', 'dashboard'); ?></p>
                </header>
                <div class="content cf-gauge5" id="cf-gauge-5">
                    <div class="val-current">
                        <div class="metric" id="cf-gauge-5-m"></div>
                    </div>
                    <div class="canvas">
                        <canvas height="180" width="285" id="cf-gauge-5-g"></canvas>
                    </div>
                    <div class="val-min">
                        <div class="metric-small"></div>
                    </div>
                    <div class="val-max">
                        <div class="metric-small"></div>
                    </div>
                </div>
            </div>

            <!-- Grafico-row-2 03 -->
            <div id="div_grafic06" style="margin: 2%; cursor: pointer;" class=" col-md-4 cf-item">
                <header>
                    <p style="font-size:14px;" id="graf6"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Incidente ', 'dashboard'); ?></p>
                </header>
                <div class="content cf-gauge6" id="cf-gauge-6">
                    <div class="val-current">
                        <div class="metric" id="cf-gauge-6-m"></div>
                    </div>
                    <div class="canvas">
                        <canvas height="180" width="285" id="cf-gauge-6-g"></canvas>
                    </div>
                    <div class="val-min">
                        <div class="metric-small"></div>
                    </div>
                    <div class="val-max">
                        <div class="metric-small"></div>
                    </div>

                </div>
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
        var impacto = $("#type_impacto").val();

        if (!groups) {
            alert("Selecione a opção de grupos");
            groups.focus();

            return;
        }
        if (impacto == "Selecione o Impacto") {
            impacto = 0;
        }
        if (sla == "Selecione o SLA") {
            alert("Selecione o SLA");
            sla.focus();
            return;
        }
        if (chamado == "Selecione o tipo do chamado") {
            chamado = 0;
        }

        buscarDados(data1, data2, groups, sla, chamado, impacto);
    }

    function buscarDados(data1, data2, groups, sla, chamado, impacto) {

        $.ajax({
            url: 'ajax/ajax_dash.php',
            type: 'GET',
            data: {
                data1: data1,
                data2: data2,
                groups: groups,
                sla: sla,
                chamado: chamado,
                impacto: impacto
            },
            async: true,
            success: function(response) {



                $("#graficos").removeClass("hidden");
                res = JSON.parse(response);
                var critico = parseInt(...res["critico"], 10);
                var medio = parseInt(...res["medio"], 10);
                var alto = parseInt(...res["alto"], 10);
                var baixo = parseInt(...res["baixo"], 10);
                var requisicao = parseInt(...res["requisicao"], 10);
                var incidentes = parseInt(...res["incidente"], 10);
                var total = parseInt(...res["tickets_total"], 10);

                initGauge(0, total, critico);
                $('#div_grafic01').click(function() {
                    window.open(`<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=6&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                });

                initGauge2(0, total, medio);
                $('#div_grafic02').click(function() {
                    window.open(`<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=3&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                });

                initGauge3(0, total, alto);
                $('#div_grafic03').click(function() {
                    window.open(`<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=4&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                });

                initGauge4(0, total, baixo);
                $('#div_grafic04').click(function() {
                    window.open(`<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=2&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0`);
                });

                initGauge5(0, total, requisicao);
                $('#div_grafic05').click(function() {
                    window.open(`<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&criteria[7][link]=AND&criteria[7][field]=14&criteria[7][searchtype]=equals&criteria[7][value]=2&search=Pesquisar&itemtype=Ticket&start=0`);
                });

                initGauge6(0, total, incidentes);
                $('#div_grafic06').click(function() {
                    window.open(`<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&criteria[7][link]=AND&criteria[7][field]=14&criteria[7][searchtype]=equals&criteria[7][value]=1&search=Pesquisar&itemtype=Ticket&start=0`);
                });

            },
            error: (error) => {
                console.log(JSON.stringify(error));
            }
        });
    }
</script>