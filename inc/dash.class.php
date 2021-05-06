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
            $gt = "AND gt.groups_id IN ($groups) AND gt.type =2";
        }

        $query = $DB->query("SELECT
            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            " . $gt . "
            " . $slaid . "
            " . $impact . "
            AND (IF(t.`time_to_resolve` IS NOT NULL
            AND t.`status` <> 4
            AND (t.`solvedate` > t.`time_to_resolve`
            OR (t.`solvedate` IS NULL
            AND t.`time_to_resolve` < NOW())),
               1,
             0) = 0)
            AND t.impact = 5
            ORDER BY t.id DESC LIMIT 1) AS muito_alto,

            (SELECT COUNT(t.id) FROM glpi_tickets as t
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

            (SELECT COUNT(t.id) FROM glpi_tickets as t
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

           (SELECT COUNT(t.id) FROM glpi_tickets as t
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

            (SELECT COUNT(t.id) FROM glpi_tickets as t
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

            (SELECT COUNT(t.id) FROM glpi_tickets as t
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

            (SELECT COUNT(t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$data_inicial 00:00:00' AND ' $data_final 23:59:59'
            AND t.is_deleted = 0
            " . $gt . "
            AND (IF(t.`time_to_resolve` IS NOT NULL
            AND t.`status` <> 4
            AND (t.`solvedate` > t.`time_to_resolve`
            OR (t.`solvedate` IS NULL
            AND t.`time_to_resolve` < NOW())),
                  1,
             0) = 0)
             ORDER BY t.id DESC LIMIT 1) as tickets_total
            
            ");




        while ($rows = $DB->fetch_assoc($query)) {
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
