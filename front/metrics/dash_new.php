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
    $data_ini = date("Y-01-01");
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
                            <td>
                                <label for=data1>Data Inicial</label>
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


                <td class="separator">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="margin-left: 10px;">
                    <label for="type_impacto">Impacto</label>
                    <select class="form-control" name="type_impacto" id="type_impacto" style="width:180px;">
                        <option>Selecione o Impacto</option>
                        <option value=" 0">Todos</option>
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
            $("#graficos").hide();


            $("#select_groups").select2({
                placeholder: 'Selecione o Grupo Resolvedor',
                dropdownAutoWidth: true
            });
            $("#type_chamado").select2({
                dropdownAutoWidth: true
            });


            $("#type_impacto").select2({
                dropdownAutoWidth: true
            });
        });
    </script>

    <div id="graficos" class="container">

        <div class="row">

            <div id="div_grafic01" style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p id="graf1"><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Muito Alto', 'dashboard'); ?></p>
                </header>

                <div class="content cf-gauge1" id="cf-gauge-1">

                    <div class="val-current">
                        <a style="text-decoration:none;" id="graf01" target="_blank">
                            <div class="metric" style="font-size: 3.5em;" id="cf-gauge-1-m"></div>
                        </a>
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


            <div id="div_grafic03" style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p id="graf3"><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Alto', 'dashboard'); ?></p>
                </header>

                <div class="content cf-gauge3" id="cf-gauge-3">
                    <div class="val-current">
                        <a style="text-decoration:none" id="graf03" target="_blank">
                            <div class="metric" style="font-size: 3.5em;" id="cf-gauge-3-m"></div>
                        </a>
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

            </div>

            <div id="div_grafic02" style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p id="graf2"><?php echo _n('', 'Total', 2) . " " . __(' de Chamados Médios', 'dashboard'); ?></p>
                </header>

                <div class="content cf-gauge2" id="cf-gauge-2">
                    <div class="val-current">
                        <a style="text-decoration:none" id="graf02" target="_blank">
                            <div class="metric" style="font-size: 3.5em;" id="cf-gauge-2-m"></div>
                        </a>
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




        </div>

        <div class="row">

            <div id="div_grafic04" style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p id="graf4"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Baixo', 'dashboard'); ?></p>
                </header>

                <div class="content cf-gauge4" id="cf-gauge-4">
                    <div class="val-current">
                        <a style="text-decoration:none" id="graf04" target="_blank">
                            <div class="metric" style="font-size: 3.5em;" id="cf-gauge-4-m"></div>
                        </a>
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

            </div>


            <div id="div_grafic05" style="margin: 2%;" class="col-md-4 cf-item">
                <header>
                    <p style="font-size:14px;" id="graf5"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Requisição', 'dashboard'); ?></p>
                </header>

                <div class="content cf-gauge5" id="cf-gauge-5">
                    <div class="val-current">
                        <a style="text-decoration:none" id="graf05" target="_blank">
                            <div class="metric" style="font-size: 3.5em;" id="cf-gauge-5-m"></div>
                        </a>
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

            </div>

            <div id="div_grafic06" style="margin: 2%;" class=" col-md-4 cf-item">
                <header>
                    <p style="font-size:14px;" id="graf6"><?php echo _n('', 'Total Geral', 2) . " " . __(' de Chamados Incidente ', 'dashboard'); ?></p>
                </header>

                <div class="content cf-gauge6" id="cf-gauge-6">
                    <div class="val-current">
                        <a style="text-decoration:none" id="graf06" target="_blank">
                            <div class="metric" style="font-size: 3.5em;" id="cf-gauge-6-m"></div>
                        </a>
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

            </div>
        </div>

    </div>



    <script>
        function recebeDados() {
            var data1 = $("#dp1 input").val();
            var data2 = $("#dp2 input").val();
            var groups = $("#select_groups").val();
            var chamado = $("#type_chamado").val();
            var impacto = $("#type_impacto").val();

            if (groups == null || groups == 0) {
                groups = 0;
            }
            if (impacto == "Selecione o Impacto") {
                impacto = 0;
            }

            if (chamado == "Selecione o tipo do chamado") {
                chamado = 0;
            }

            buscarDados(data1, data2, groups, chamado, impacto);
        }

        function buscarDados(data1, data2, groups, chamado, impacto) {
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
                    chamado: chamado,
                    impacto: impacto
                },
                async: false,
                success: function(response) {
                    // Criando URL para lista de grupos
                    let link_muito_alto = "";
                    let link_medio = "";
                    let link_alto = "";
                    let link_baixo = "";
                    let link_requisicao = "";
                    let link_incidente = "";

                    if (groups != 0) {

                        $.each(groups, (index, grupo) => {
                            link_muito_alto += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                            link_medio += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                            link_alto += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                            link_baixo += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                            link_requisicao += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                            link_incidente += `${(index == 0 ? "" : "&")}criteria[${index}][link]=${(index == 0 ? "AND" : "OR")}&`;
                            if (chamado == 0 & groups != 0) {
                                link_muito_alto += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=11&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=5`;
                                link_medio += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=11&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=3`;
                                link_alto += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=11&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=4`;
                                link_baixo += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=11&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=2`;
                                link_requisicao += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=14&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=2`
                                link_incidente += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=14&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=1`
                            } else if (chamado != 0 && groups != 0) {
                                link_muito_alto += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=11&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=5&criteria[${index}][criteria][9][field]=14&criteria[${index}][criteria][9][searchtype]=equals&criteria[${index}][criteria][9][value]=${chamado}`;
                                link_medio += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=11&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=3&criteria[${index}][criteria][9][field]=14&criteria[${index}][criteria][9][searchtype]=equals&criteria[${index}][criteria][9][value]=${chamado}`;
                                link_alto += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=11&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=4&criteria[${index}][criteria][9][field]=14&criteria[${index}][criteria][9][searchtype]=equals&criteria[${index}][criteria][9][value]=${chamado}`;
                                link_baixo += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:0&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=11&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=2&criteria[${index}][criteria][9][field]=14&criteria[${index}][criteria][9][searchtype]=equals&criteria[${index}][criteria][9][value]=${chamado}`;
                                link_requisicao += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=14&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=2`;
                                link_incidente += `criteria[${index}][criteria][1][link]=AND&criteria[${index}][criteria][1][field]=15&criteria[${index}][criteria][1][searchtype]=morethan&criteria[${index}][criteria][1][value]=${data1}+00:00&criteria[${index}][criteria][3][link]=AND&criteria[${index}][criteria][3][field]=82&criteria[${index}][criteria][3][searchtype]=equals&criteria[${index}][criteria][3][value]=0&criteria[${index}][criteria][5][link]=AND&criteria[${index}][criteria][5][field]=8&criteria[${index}][criteria][5][searchtype]=equals&criteria[${index}][criteria][5][value]=${grupo}&criteria[${index}][criteria][7][link]=AND&criteria[${index}][criteria][7][field]=15&criteria[${index}][criteria][7][searchtype]=lessthan&criteria[${index}][criteria][7][value]=${data2}+23:55&criteria[${index}][criteria][8][link]=AND&criteria[${index}][criteria][8][field]=14&criteria[${index}][criteria][8][searchtype]=equals&criteria[${index}][criteria][8][value]=1`;
                            }
                        });
                    }

                    $("#graficos").show();
                    res = JSON.parse(response);
                    var muito_alto = parseInt(...res["muito_alto"], 10);
                    var medio = parseInt(...res["medio"], 10);
                    var alto = parseInt(...res["alto"], 10);
                    var baixo = parseInt(...res["baixo"], 10);
                    var requisicao = parseInt(...res["requisicao"], 10);
                    var incidentes = parseInt(...res["incidente"], 10);
                    var total = parseInt(...res["tickets_total"], 10);


                    if (chamado != 0 && groups == 0) {
                        $("#graf01").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${data1}+00:00&criteria[0][value]=${data1}+00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${data2}+23:55&criteria[1][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=82&criteria[3][searchtype]=equals&criteria[3][value]=0&criteria[4][link]=AND&criteria[4][field]=11&criteria[4][searchtype]=equals&criteria[4][value]=5&criteria[6][link]=AND&criteria[6][field]=14&criteria[6][searchtype]=equals&criteria[6][value]=${chamado}&search=Pesquisar&itemtype=Ticket&start=0`);
                    } else if (chamado == 0 && groups == 0) {
                        $("#graf01").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][criteria][1][link]=AND&criteria[0][criteria][1][field]=15&criteria[0][criteria][1][searchtype]=morethan&_select_criteria[0][criteria][1][value]=0&_criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][3][link]=AND&criteria[0][criteria][3][field]=82&criteria[0][criteria][3][searchtype]=equals&criteria[0][criteria][3][value]=0&criteria[0][criteria][7][link]=AND&criteria[0][criteria][7][field]=15&criteria[0][criteria][7][searchtype]=lessthan&_select_criteria[0][criteria][7][value]=0&_criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][8][link]=AND&criteria[0][criteria][8][field]=11&criteria[0][criteria][8][searchtype]=equals&criteria[0][criteria][8][value]=5&search=Pesquisar&itemtype=Ticket&start=0`);
                    } else {
                        $("#graf01").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?${link_muito_alto}`);
                    }
                    if (chamado != 0 && groups == 0) {
                        $("#graf02").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${data1}+00:00&criteria[0][value]=${data1}+00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${data2}+23:55&criteria[1][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=82&criteria[3][searchtype]=equals&criteria[3][value]=0&criteria[4][link]=AND&criteria[4][field]=11&criteria[4][searchtype]=equals&criteria[4][value]=3&criteria[6][link]=AND&criteria[6][field]=14&criteria[6][searchtype]=equals&criteria[6][value]=${chamado}&search=Pesquisar&itemtype=Ticket&start=0`);
                    } else if (chamado == 0 && groups == 0) {
                        $("#graf02").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][criteria][1][link]=AND&criteria[0][criteria][1][field]=15&criteria[0][criteria][1][searchtype]=morethan&_select_criteria[0][criteria][1][value]=0&_criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][3][link]=AND&criteria[0][criteria][3][field]=82&criteria[0][criteria][3][searchtype]=equals&criteria[0][criteria][3][value]=0&criteria[0][criteria][7][link]=AND&criteria[0][criteria][7][field]=15&criteria[0][criteria][7][searchtype]=lessthan&_select_criteria[0][criteria][7][value]=0&_criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][8][link]=AND&criteria[0][criteria][8][field]=11&criteria[0][criteria][8][searchtype]=equals&criteria[0][criteria][8][value]=3&search=Pesquisar&itemtype=Ticket&start=0`);
                    } else {
                        $("#graf02").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?${link_medio}`);
                    }
                    if (chamado != 0 && groups == 0) {
                        $("#graf03").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${data1}+00:00&criteria[0][value]=${data1}+00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${data2}+23:55&criteria[1][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=82&criteria[3][searchtype]=equals&criteria[3][value]=0&criteria[4][link]=AND&criteria[4][field]=11&criteria[4][searchtype]=equals&criteria[4][value]=4&criteria[6][link]=AND&criteria[6][field]=14&criteria[6][searchtype]=equals&criteria[6][value]=${chamado}&search=Pesquisar&itemtype=Ticket&start=0`)
                    } else if (chamado == 0 && groups == 0) {
                        $("#graf03").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][criteria][1][link]=AND&criteria[0][criteria][1][field]=15&criteria[0][criteria][1][searchtype]=morethan&_select_criteria[0][criteria][1][value]=0&_criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][3][link]=AND&criteria[0][criteria][3][field]=82&criteria[0][criteria][3][searchtype]=equals&criteria[0][criteria][3][value]=0&criteria[0][criteria][7][link]=AND&criteria[0][criteria][7][field]=15&criteria[0][criteria][7][searchtype]=lessthan&_select_criteria[0][criteria][7][value]=0&_criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][8][link]=AND&criteria[0][criteria][8][field]=11&criteria[0][criteria][8][searchtype]=equals&criteria[0][criteria][8][value]=4&search=Pesquisar&itemtype=Ticket&start=0`)
                    } else {
                        $("#graf03").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?${link_alto}`);
                    }
                    if (chamado != 0 && groups == 0) {
                        $("#graf04").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][field]=15&criteria[0][searchtype]=morethan&_select_criteria[0][value]=0&_criteria[0][value]=${data1}+00:00&criteria[0][value]=${data1}+00:00&criteria[1][link]=AND&criteria[1][field]=15&criteria[1][searchtype]=lessthan&_select_criteria[1][value]=0&_criteria[1][value]=${data2}+23:55&criteria[1][value]=${data2}+23:55:00&criteria[3][link]=AND&criteria[3][field]=82&criteria[3][searchtype]=equals&criteria[3][value]=0&criteria[4][link]=AND&criteria[4][field]=11&criteria[4][searchtype]=equals&criteria[4][value]=2&criteria[6][link]=AND&criteria[6][field]=14&criteria[6][searchtype]=equals&criteria[6][value]=${chamado}&search=Pesquisar&itemtype=Ticket&start=0`);
                    } else if (chamado == 0 && groups == 0) {
                        $("#graf04").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][criteria][1][link]=AND&criteria[0][criteria][1][field]=15&criteria[0][criteria][1][searchtype]=morethan&_select_criteria[0][criteria][1][value]=0&_criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][3][link]=AND&criteria[0][criteria][3][field]=82&criteria[0][criteria][3][searchtype]=equals&criteria[0][criteria][3][value]=0&criteria[0][criteria][7][link]=AND&criteria[0][criteria][7][field]=15&criteria[0][criteria][7][searchtype]=lessthan&_select_criteria[0][criteria][7][value]=0&_criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][8][link]=AND&criteria[0][criteria][8][field]=11&criteria[0][criteria][8][searchtype]=equals&criteria[0][criteria][8][value]=2&search=Pesquisar&itemtype=Ticket&start=0`);
                    } else {
                        $("#graf04").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?${link_baixo}`);
                    }
                    if (groups == 0) {
                        $("#graf05").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][criteria][1][link]=AND&criteria[0][criteria][1][field]=15&criteria[0][criteria][1][searchtype]=morethan&_select_criteria[0][criteria][1][value]=0&_criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][3][link]=AND&criteria[0][criteria][3][field]=82&criteria[0][criteria][3][searchtype]=equals&criteria[0][criteria][3][value]=0&criteria[0][criteria][7][link]=AND&criteria[0][criteria][7][field]=15&criteria[0][criteria][7][searchtype]=lessthan&_select_criteria[0][criteria][7][value]=0&_criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][8][link]=AND&criteria[0][criteria][8][field]=11&criteria[0][criteria][8][searchtype]=equals&criteria[0][criteria][8][value]=${(impacto == 0 ? 11 : impacto)}&criteria[0][criteria][6][link]=AND&criteria[0][criteria][6][field]=14&criteria[0][criteria][6][searchtype]=equals&criteria[0][criteria][6][value]=2&search=Pesquisar&itemtype=Ticket&start=0`);
                    } else {
                        $("#graf05").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?${link_requisicao}`);
                    }
                    if (groups == 0) {
                        $("#graf06").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?is_deleted=0&as_map=0&criteria[0][link]=AND&criteria[0][criteria][1][link]=AND&criteria[0][criteria][1][field]=15&criteria[0][criteria][1][searchtype]=morethan&_select_criteria[0][criteria][1][value]=0&_criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][1][value]=${data1}+00:00&criteria[0][criteria][3][link]=AND&criteria[0][criteria][3][field]=82&criteria[0][criteria][3][searchtype]=equals&criteria[0][criteria][3][value]=0&criteria[0][criteria][7][link]=AND&criteria[0][criteria][7][field]=15&criteria[0][criteria][7][searchtype]=lessthan&_select_criteria[0][criteria][7][value]=0&_criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][7][value]=${data2}+23:55&criteria[0][criteria][8][link]=AND&criteria[0][criteria][8][field]=11&criteria[0][criteria][8][searchtype]=equals&criteria[0][criteria][8][value]=${(impacto == 0 ? 11 : impacto)}&criteria[0][criteria][6][link]=AND&criteria[0][criteria][6][field]=14&criteria[0][criteria][6][searchtype]=equals&criteria[0][criteria][6][value]=1&search=Pesquisar&itemtype=Ticket&start=0`);
                    } else {
                        $("#graf06").attr("href", `<?php echo $CFG_GLPI['url_base'] ?>/front/ticket.php?${link_incidente}`);
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
                    gauge.set(muito_alto);
                    document.getElementById("cf-gauge-1-m").innerHTML = muito_alto;
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
</body>

</html>