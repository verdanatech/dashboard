<?php

class NewDashboard
{
    public function buscarTickets($dados)
    {

        global $DB;
        $row = array();
        $data_inicial = $dados['data1'];
        $data_final = $dados['data2'];
        $sla = $dados['sla'];
        $group = $dados['groups'];
        $chamado = $dados['chamado'];
        $impacto = $dados['impacto'];
        $groups = implode(",", $group);

        $data_atual = date("Y-m-d");
        $slaid = "";
        $gt = "";
        $impact = "";
        if ($impacto != 0) {
            $impact = "AND t.impact LIKE '%$impacto%'";
        }
        if ($chamado != 0) {
            $slaid = "AND t.type = $chamado";
        }

        if ($groups != 0) {
            $gt = "AND gt.groups_id IN ($groups)";
        }

        $query = $DB->query("SELECT
            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date_creation BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.time_to_resolve > t.closedate 
            AND t.priority = 6
            ORDER BY t.id DESC LIMIT 1) AS critico,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date_creation BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.is_deleted = 0
            AND t.time_to_resolve > t.closedate
            AND t.priority = 3 
            ORDER BY t.id DESC LIMIT 1) AS medio,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date_creation BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.is_deleted = 0
            AND t.time_to_resolve > t.closedate
            AND t.priority = 4 
           ORDER BY t.id DESC LIMIT 1) AS alto,

           (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date_creation BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.is_deleted = 0
            AND t.time_to_resolve > t.closedate
            AND t.priority = 2 
           ORDER BY t.id DESC LIMIT 1) AS baixo,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date_creation BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $impact . "
            AND t.type=2
            AND t.is_deleted = 0
            AND t.time_to_resolve > t.closedate
            ORDER BY t.id DESC LIMIT 1) as requisicao,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date_creation BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $impact . "
            AND t.is_deleted = 0
            AND t.type=1
            AND t.time_to_resolve >  t.closedate
            ORDER BY t.id DESC LIMIT 1) as incidente");




        while ($rows = $DB->fetch_assoc($query)) {
            $row['critico'][] = $rows['critico'];
            $row['medio'][] = $rows['medio'];
            $row['alto'][] = $rows['alto'];
            $row['baixo'][] = $rows['baixo'];
            $row['requisicao'][] = $rows['requisicao'];
            $row['incidente'][] = $rows['incidente'];
        }


        if (!empty($row)) {
            return $row;
        } else {
            return 0;
        }
    }
}