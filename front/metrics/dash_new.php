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

    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="dash.css" rel="stylesheet">
    <script>
        var themeColour = 'black';
    </script>

    <script src="gauge.min.js"></script>
    <link rel="icon" href="../img/dash.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="../img/dash.ico" type="image/x-icon" />

    <script src="../js/jquery.js"></script>
    <script src="moment.js"></script>
    <script src="jquery.easypiechart.js"></script>

    <script src="dash.css"></script>
    <script src="chart.js"></script>
    <script src="jquery-sparkline.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="controlfrog-plugins.js"></script>
    <script src="gauge.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <link href="../css/datepicker.css" rel="stylesheet" type="text/css">
    <link href="../css/font-awesome.css" type="text/css" rel="stylesheet" />
    <link href="../inc/select2/select2.css" rel="stylesheet" type="text/css">
    <script src="../inc/select2/select2.js" type="text/javascript" language="javascript"></script>

    <script src="../js/themes/dark-unica.js" type="text/javascript"></script>
    <script src="../js/modules/no-data-to-display.js" type="text/javascript"></script>
    <script src="reload.js"></script>
    <script src="reload_param.js"></script>


</head>

<body class="black">

    <a href="../index.php"><i class="fa fa-home" style="font-size:14pt;"></i><span></span></a>

    <h3 align="center" style="color:white; font-size:40px; margin-bottom: 41px;">Dashboard por SLA</h3>

    <div class="container" align="center">
        <table style="border-collapse: separate; border-spacing: 14px 14px;" bgcolor="#efefef">
            <tr>
                <td style="width: 310px;">
                    <?php
                    $url = $_SERVER['REQUEST_URI'];
                    $arr_url = explode("?", $url);
                    $url2 = $arr_url[0];
                    ?>

                    <table>
                        <tr>
                            <td>
                                <div class="input-group date" id="dp1" data-date="<?php echo $data_ini; ?>" data-date-format="yyyy-mm-dd">
                                    <input class="col-md-9 form-control" size="13" type="text" name="date1" value="<?php echo $data_ini; ?>">
                                    <span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
                                </div>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <div class="input-group date" id="dp2" data-date="<?php echo $data_fin; ?>" data-date-format="yyyy-mm-dd">
                                    <input class="col-md-9 form-control" size="13" type="text" name="date2" value="<?php echo $data_fin; ?>">
                                    <span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
                                </div>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>

                    <script language="Javascript">
                        $('#dp1').datepicker('update');
                        $('#dp2').datepicker('update');
                    </script>
                </td>
                <td style="margin-top:2px;">
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
                <td style="margin-top:2px;margin-left:20px;">
                    <select class="form-control" name="type_impacto" id="type_impacto">
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
                <td style="margin-top:4px;margin-left:180px;">
                    <select id="select_groups" name="sel_gr[]" class="js-example-basic-multiple js-states" multiple=" multiple" style="width: 308px;margin-top:4px; text-transform: capitalize;">
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

                            $selected = (isset($_POST['sel_gr']) && $_POST['sel_gr'] == $value['id_grupo']) ? 'selected' : '';
                            echo "<option style='text-transform: capitalize; ' $selected  value=" . $value['id_grupo'] . ">" . ($value['name']) . "</option>";
                        }
                        ?>
                    </select>
                <td style="margin-top:4px;">
                    <select style="width:180px;margin-top:4px;" class="form-control" name="type_chamado" id="type_chamado">
                        <option>Selecione o tipo do chamado</option>
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
            <div style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p id="graf1"><span></span><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Críticos', 'dashboard'); ?> - </p>
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
            <div style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p id="graf2"><span></span><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Médios', 'dashboard'); ?> - </p>
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
            <div style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p id="graf3"><span></span><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Alto', 'dashboard'); ?> - </p>
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
            <div style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p id="graf4"><span></span><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Baixo', 'dashboard'); ?> - </p>
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
            <div style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p style="font-size:14px;" id="graf5"><span></span><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Requisição', 'dashboard'); ?> - </p>
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
            <div style="margin: 2%;" class=" col-md-4 cf-item">
                <header>
                    <p style="font-size:14px;" id="graf6"><span></span><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Incidente ', 'dashboard'); ?> - </p>
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
            document.location.reload(true)
        }
        if (impacto == "Selecione o Impacto") {
            impacto = 0;
        }
        if (sla == "Selecione o SLA") {
            alert("Selecione o SLA");
            document.location.reload(true)
        }
        if (chamado == "Selecione o tipo do chamado") {
            chamado = 0;
        }



        buscarDados(data1, data2, groups, sla, chamado, impacto);
    }



    function buscarDados(data1, data2, groups, sla, chamado, impacto) {
        $("#graf1 a").empty();
        $("#graf2 a").empty()
        $("#graf3 a").empty()
        $("#graf4 a").empty()
        $("#graf5 a").empty()
        $("#graf6 a").empty()
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
            async: false,
            success: function(response) {
                $("#graficos").removeClass("hidden");
                res = JSON.parse(response);

                res["critico"] = res["critico"].map(i => Number(i));
                res['medio'] = res['medio'].map(i => Number(i));;
                res['alto'] = res['alto'].map(i => Number(i));
                res['baixo'] = res['baixo'].map(i => Number(i));
                res['requisicao'] = res['requisicao'].map(i => Number(i));
                res['incidente'] = res['incidente'].map(i => Number(i));
                var html = "";
                initGauge(0, 1000, ...res["critico"]);
                html = `<a href="<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php??is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=6&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0'
                " target='_blank'style="font-size:10px">Abrir Chamados</a>`;
                $(`#graf1`).append(html);
                initGauge2(0, 1000, ...res["medio"]);
                html = `<a href="<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php??is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=3&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0'
                " target='_blank'style="font-size:10px">Abrir Chamados</a>`;
                $(`#graf2`).append(html);
                initGauge3(0, 1000, ...res["alto"])
                html = `<a href="<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php??is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=4&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0'
                " target='_blank'style="font-size:10px">Abrir Chamados</a>`;
                $(`#graf3`).append(html);
                initGauge4(0, 1000, ...res["baixo"])
                html = `<a href="<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php??is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[5][link]=AND&criteria[5][field]=3&criteria[5][searchtype]=equals&criteria[5][value]=2&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&search=Pesquisar&itemtype=Ticket&start=0'
                " target='_blank'style="font-size:10px">Abrir Chamados</a>`;
                $(`#graf4`).append(html);
                initGauge5(0, 1000, ...res["requisicao"])
                html = `<a href="<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&criteria[7][link]=AND&criteria[7][field]=14&criteria[7][searchtype]=equals&criteria[7][value]=2&search=Pesquisar&itemtype=Ticket&start=0'
                " target='_blank'style="font-size:10px">Abrir Chamados</a>`;
                $(`#graf5`).append(html);
                initGauge6(0, 1000, ...res["incidente"])
                html = `<a href="<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=8&criteria[0][searchtype]=equals&criteria[0][value]=${groups}&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=morethan&_select_criteria[1][value]=0&_criteria[1][value]=${data1}}+00:00&criteria[1][value]=${data1}+00:00&criteria[2][link]=AND&criteria[2][field]=15&criteria[2][searchtype]=lessthan&_select_criteria[2][value]=0&_criteria[2][value]=${data2}+23:55&criteria[2][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=30&criteria[3][searchtype]=equals&criteria[3][value]=${sla}&criteria[4][link]=AND&criteria[4][field]=82&criteria[4][searchtype]=equals&criteria[4][value]=0&criteria[6][link]=AND&criteria[6][field]=12&criteria[6][searchtype]=equals&criteria[6][value]=6&criteria[7][link]=AND&criteria[7][field]=14&criteria[7][searchtype]=equals&criteria[7][value]=1&search=Pesquisar&itemtype=Ticket&start=0'
                " target='_blank'style="font-size:10px">Abrir Chamados</a>`;
                $(`#graf6`).append(html);




            },
            error: (error) => {
                console.log(JSON.stringify(error));
            }
        });
    }
</script>