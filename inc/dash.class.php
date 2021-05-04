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
        $prioridade = $dados['prioridade'];

        $data_atual = date("Y-m-d");
        $slaid = "";
        $gt = "";
        $priority = "";
        if ($prioridade != 0) {
            $priority = "AND t.priority LIKE '%$prioridade%'";
        }
        if ($chamado != 0) {
            $slaid = "AND t.type = $chamado";
        }

        if ($group != 0) {
            $groups = implode(",", $group);
            $gt = "AND gt.groups_id IN ($groups)";
        }

        $query = $DB->query("SELECT
            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            " . $priority . "
            AND t.time_to_resolve > t.closedate 
            AND t.priority = 6
            ORDER BY t.id DESC LIMIT 1) AS critico,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            " . $priority . "
            AND t.is_deleted = 0
            AND t.time_to_resolve > t.closedate
            AND t.priority = 3 
            ORDER BY t.id DESC LIMIT 1) AS medio,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            " . $priority . "
            AND t.is_deleted = 0
            AND t.time_to_resolve > t.closedate
            AND t.priority = 4 
           ORDER BY t.id DESC LIMIT 1) AS alto,

           (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            " . $priority . "
            AND t.is_deleted = 0
            AND t.time_to_resolve > t.closedate
            AND t.priority = 2 
           ORDER BY t.id DESC LIMIT 1) AS baixo,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $priority . "
            AND t.type= 2
            AND t.is_deleted = 0
            AND t.time_to_resolve > t.closedate
            ORDER BY t.id DESC LIMIT 1) as requisicao,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $priority . "
            AND t.is_deleted = 0
            AND t.type=1
            AND t.time_to_resolve >  t.closedate
            ORDER BY t.id DESC LIMIT 1) as incidente,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.is_deleted = 0
            AND t.slas_id_ttr = '$sla'
            " . $gt . "
            AND t.time_to_resolve >  t.closedate
            ORDER BY t.id DESC LIMIT 1) as tickets_total
            
            ");




        while ($rows = $DB->fetch_assoc($query)) {
            $row['critico'][] = $rows['critico'];
            $row['medio'][] = $rows['medio'];
            $row['alto'][] = $rows['alto'];
            $row['baixo'][] = $rows['baixo'];
            $row['requisicao'][] = $rows['requisicao'];
            $row['incidente'][] = $rows['incidente'];
            $row['tickets_total'][] = $rows['tickets_total'];
        }


        if (!empty($row)) {
            return $row;
        } else {
            return 0;
        }
    }
}
