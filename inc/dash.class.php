<?php

class NewDashboard
{
    public function buscarTickets($dados)
    {

        global $DB;
        $row = array();
        $data_inicial = $dados['data1'];
        $data_final = $dados['data2'];
        $group = $dados['groups'];
        $chamado = $dados['chamado'];
        $impacto = $dados['impacto'];
        $slaid = "";
        $gt = "";
        $impact = "";
        if ($impacto != 0) {
            $impact = "AND t.impact LIKE '%$impacto%'";
        }
        if ($chamado != 0) {
            $slaid = "AND t.type = $chamado";
        }

        if ($group != 0) {
            $groups = implode(",", $group);
            $gt = "AND gt.groups_id IN ($groups) AND gt.type = 2";
        }

        $query = $DB->query("SELECT
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.is_deleted = 0 
            AND (IF(t.`time_to_resolve` IS NOT NULL
            AND t.`status` <> 4
            AND (t.`solvedate` > t.`time_to_resolve`
            OR (t.`solvedate` IS NULL
            AND t.`time_to_resolve` < NOW())),
               1,
             0) = 0)
            AND t.impact = 5
            ORDER BY t.id DESC LIMIT 1) AS muito_alto,

            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.is_deleted = 0            
            AND t.impact = 3
            AND (IF(t.`time_to_resolve` IS NOT NULL
            AND t.`status` <> 4
            AND (t.`solvedate` > t.`time_to_resolve`
            OR (t.`solvedate` IS NULL
            AND t.`time_to_resolve` < NOW())),
            1,
              0) = 0)
            ORDER BY t.id DESC LIMIT 1) AS medio,

            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.is_deleted = 0
            AND t.impact = 4 
            AND (IF(t.`time_to_resolve` IS NOT NULL
            AND t.`status` <> 4
            AND (t.`solvedate` > t.`time_to_resolve`
            OR (t.`solvedate` IS NULL
            AND t.`time_to_resolve` < NOW())),
            1,
             0) = 0)
           ORDER BY t.id DESC LIMIT 1) AS alto,

           (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
             WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.is_deleted = 0
            AND t.impact = 2 
            AND (IF(t.`time_to_resolve` IS NOT NULL
            AND t.`status` <> 4
            AND (t.`solvedate` > t.`time_to_resolve`
            OR (t.`solvedate` IS NULL
            AND t.`time_to_resolve` < NOW())),
              1,
              0) = 0)
            ORDER BY t.id DESC LIMIT 1) AS baixo,

            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
            " . $impact . "
            AND t.type= 2
            AND t.is_deleted = 0
            AND (IF(t.`time_to_resolve` IS NOT NULL
            AND t.`status` <> 4
            AND (t.`solvedate` > t.`time_to_resolve`
            OR (t.`solvedate` IS NULL
            AND t.`time_to_resolve` < NOW())),
             1,
             0) = 0)
            ORDER BY t.id DESC LIMIT 1) as requisicao,

            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
            " . $impact . "
            AND t.is_deleted = 0
            AND t.type=1
            AND (IF(t.`time_to_resolve` IS NOT NULL
            AND t.`status` <> 4
            AND (t.`solvedate` > t.`time_to_resolve`
            OR (t.`solvedate` IS NULL
            AND t.`time_to_resolve` < NOW())),
            1,
            0) = 0)
            ORDER BY t.id DESC LIMIT 1) as incidente,

            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.is_deleted = 0
            " . $gt . "
                ORDER BY t.id DESC LIMIT 1) as tickets_total,

                (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
             LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
              WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
              " . $gt . "
              " . $slaid . "
              " . $impact . "
              " . $gt . "
             AND t.impact = 5
             AND t.is_deleted = 0
             ORDER BY t.id DESC LIMIT 1) AS total_muito_alto,
    
             (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
             LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
             WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
             " . $gt . "
            " . $slaid . "
            " . $impact . "
             AND t.impact = 4
             AND t.is_deleted = 0
             ORDER BY t.id DESC LIMIT 1) AS total_alto,

             (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND '$data_final 23:59:59'
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.impact = 3
            AND t.is_deleted = 0
            ORDER BY t.id DESC LIMIT 1) AS total_medio,

            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND t.impact = 2
            AND t.is_deleted = 0
            ORDER BY t.id DESC LIMIT 1) AS total_baixo,
            
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND '$data_final 23:59:59'
            " . $gt . "
            " . $impact . "
            AND t.type = 1
            AND t.is_deleted = 0
            ORDER BY t.id DESC LIMIT 1) AS total_incidente,
            
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
                      " . $impact . "
            AND t.type = 2
            AND t.is_deleted = 0
            ORDER BY t.id DESC LIMIT 1) AS total_requisicao");



        while ($rows = $DB->fetch_assoc($query)) {
            if ($rows['muito_alto'] != 0) {
                $row['muito_alto_percent'] = $rows['muito_alto'] * 100 / $rows['total_muito_alto'];
                $row['muito_alto_percent'] =  number_format($row['muito_alto_percent'], 2);
            } else {
                $row['muito_alto_percent'] = 0;
            }
            if ($rows['alto'] != 0) {
                $row['alto_percent'] = $rows['alto'] * 100 / $rows['total_alto'];
                $row['alto_percent'] =  number_format($row['alto_percent'], 2);
            } else {
                $row['alto_percent'] = 0;
            }
            if ($rows['medio'] != 0) {
                $row['medio_percent'] = $rows['medio'] * 100 / $rows['total_medio'];
                $row['medio_percent'] =  number_format($row['medio_percent'], 2);
            } else {
                $row['medio_percent'] = 0;
            }
            if ($rows['baixo'] != 0) {
                $row['baixo_percent'] = $rows['baixo'] * 100 / $rows['total_baixo'];
                $row['baixo_percent'] =  number_format($row['baixo_percent'], 2);
            } else {
                $row['baixo_percent'] = 0;
            }
            if ($rows['incidente'] != 0) {
                $row['incidente_percent'] = $rows['incidente'] * 100 / $rows['total_incidente'];
                $row['incidente_percent'] =  number_format($row['incidente_percent'], 2);
            } else {
                $row['incidente_percent'] = 0;
            }
            if ($rows['requisicao'] != 0) {
                $row['requisicao_percent'] = $rows['requisicao'] * 100 / $rows['total_requisicao'];
                $row['requisicao_percent'] =  number_format($row['requisicao_percent'], 2);
            } else {
                $row['requisicao_percent'] = 0;
            }
            $row['total_requisicao'] = $rows['total_requisicao'];
            $row['total_incidente'] = $rows['total_incidente'];
            $row['total_muito_alto'][] = $rows['total_muito_alto'];
            $row['total_medio'][] = $rows['total_medio'];
            $row['total_alto'][] = $rows['total_alto'];
            $row['total_baixo'][] = $rows['total_baixo'];

            $row['muito_alto'][] = $rows['muito_alto'];
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
