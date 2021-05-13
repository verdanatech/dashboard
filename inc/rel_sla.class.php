<?php

class Relatorio_SLA
{


    public function ticketsSla($dados)
    {
        global $DB;
        $row = array();

        $data_inicial = $dados['data1'];
        $data_final = $dados['data2'];
        $impact = $dados['impact'];
        $group = $dados['groups'];
        $sla = $dados['sla'];
        $data_inicial_time = strtotime($data_inicial);
        $data_final_time = strtotime($data_final);
        $data_atual = date("Y-m-d");
        $impacto = "";
        $gt = "";
        if ($impact != 0) {
            $impacto = " AND t.impact LIKE '%$impact%'";
        }

        if ($group != 0) {
            $groups = implode(",", $group);
            $gt = "AND gt.groups_id IN ($groups)
            AND gt.type=2";
        }
        if ($data_inicial_time > $data_final_time) {
            return false;
        }

        $total_dias = (($data_final_time - $data_inicial_time) / 86400); // Segundos em um dia

        if ($total_dias > 730) { // Total de dias em um ano
            return false;
        }



        for ($dia = 0; $dia <= $total_dias; $dia++) {

            $date_inicial_formater  = date("Y-m-d", strtotime("+{$dia} days", $data_inicial_time));
            $row['label'][]  = date("d/m", strtotime("+{$dia} days", $data_inicial_time));

            //Consulta
            $query = $DB->query("SELECT 
            (SELECT DISTINCT count(glpi_tickets.id) from glpi_tickets 
            LEFT JOIN
                     glpi_groups_tickets AS gt ON (glpi_tickets.id = gt.tickets_id)
              WHERE
             `glpi_tickets`.`is_deleted` = 0
                
               AND glpi_tickets.slas_id_ttr = $sla
                 AND (IF(`glpi_tickets`.`time_to_resolve` IS NOT NULL
                     AND `glpi_tickets`.`status` <> 4
                     AND (`glpi_tickets`.`solvedate` > `glpi_tickets`.`time_to_resolve`
                     OR (`glpi_tickets`.`solvedate` IS NULL
                     AND `glpi_tickets`.`time_to_resolve` < NOW())),
                 1,
                 0) = 0)
                 
                 AND (`glpi_tickets`.`date` > '$date_inicial_formater 00:00:00'
                 AND `glpi_tickets`.`date` < '$date_inicial_formater 23:59:00')
                 " . $gt . "
                 " . $impacto . " )AS dentro,
            
                 (SELECT DISTINCT count(glpi_tickets.id) from glpi_tickets 
                 LEFT JOIN
                          glpi_groups_tickets AS gt ON (glpi_tickets.id = gt.tickets_id)
                   WHERE
                  `glpi_tickets`.`is_deleted` = 0
                     
                    AND glpi_tickets.slas_id_ttr = $sla
                      AND (IF(`glpi_tickets`.`time_to_resolve` IS NOT NULL
                          AND `glpi_tickets`.`status` <> 4
                          AND (`glpi_tickets`.`solvedate` > `glpi_tickets`.`time_to_resolve`
                          OR (`glpi_tickets`.`solvedate` IS NULL
                          AND `glpi_tickets`.`time_to_resolve` < NOW())),
                      1,
                      0) = 1)
                      
                      AND (`glpi_tickets`.`date` > '$date_inicial_formater 00:00:00'
                      AND `glpi_tickets`.`date` < '$date_inicial_formater 23:59:00')
                      " . $gt . "
                      " . $impacto . " )AS fora,
        
                      (SELECT DISTINCT count(glpi_tickets.id) from glpi_tickets 
                      LEFT JOIN
                               glpi_groups_tickets AS gt ON (glpi_tickets.id = gt.tickets_id)
                        WHERE
                       `glpi_tickets`.`is_deleted` = 0
                          
                         AND glpi_tickets.slas_id_ttr = $sla
                           AND (IF(`glpi_tickets`.`time_to_resolve` IS NOT NULL
                               AND `glpi_tickets`.`status` <> 4
                               AND (`glpi_tickets`.`solvedate` > `glpi_tickets`.`time_to_resolve`
                               OR (`glpi_tickets`.`solvedate` IS NULL
                               AND `glpi_tickets`.`time_to_resolve` < NOW())),
                           1,
                           0) = 0)
                           AND `glpi_tickets`.status < 5
                           AND (`glpi_tickets`.`date` > '$date_inicial_formater 00:00:00'
                           AND `glpi_tickets`.`date` < '$date_inicial_formater 23:59:00')
                           " . $gt . "
                           " . $impacto . " )AS NRD,
                        
                           (SELECT DISTINCT count(glpi_tickets.id) from glpi_tickets 
                           LEFT JOIN
                                    glpi_groups_tickets AS gt ON (glpi_tickets.id = gt.tickets_id)
                             WHERE
                            `glpi_tickets`.`is_deleted` = 0
                               
                              AND glpi_tickets.slas_id_ttr = $sla
                                AND (IF(`glpi_tickets`.`time_to_resolve` IS NOT NULL
                                    AND `glpi_tickets`.`status` <> 4
                                    AND (`glpi_tickets`.`solvedate` > `glpi_tickets`.`time_to_resolve`
                                    OR (`glpi_tickets`.`solvedate` IS NULL
                                    AND `glpi_tickets`.`time_to_resolve` < NOW())),
                                1,
                                0) = 1)
                                AND `glpi_tickets`.status < 5
                                AND (`glpi_tickets`.`date` > '$date_inicial_formater 00:00:00'
                                AND `glpi_tickets`.`date` < '$date_inicial_formater 23:59:00')
                                " . $gt . "
                                " . $impacto . " )AS NRF,
            
                                (SELECT DISTINCT count(glpi_tickets.id) from glpi_tickets 
                                LEFT JOIN
                                         glpi_groups_tickets AS gt ON (glpi_tickets.id = gt.tickets_id)
                                  WHERE
                                 `glpi_tickets`.`is_deleted` = 0
                                    
                                   AND glpi_tickets.slas_id_ttr = $sla
                                     AND (IF(`glpi_tickets`.`time_to_resolve` IS NOT NULL
                                         AND `glpi_tickets`.`status` <> 4
                                         AND (`glpi_tickets`.`solvedate` > `glpi_tickets`.`time_to_resolve`
                                         OR (`glpi_tickets`.`solvedate` IS NULL
                                         AND `glpi_tickets`.`time_to_resolve` < NOW())),
                                     1,
                                     0) = 0)
                                     AND `glpi_tickets`.status = 4
                                     AND (`glpi_tickets`.`date` > '$date_inicial_formater 00:00:00'
                                     AND `glpi_tickets`.`date` < '$date_inicial_formater 23:59:00')
                                     " . $gt . "
                                     " . $impacto . " )AS PED,
            
                                    (SELECT DISTINCT count(`glpi_tickets`.`id`)
                                    FROM `glpi_tickets`
                                    LEFT JOIN
                                         glpi_groups_tickets AS gt ON (glpi_tickets.id = gt.tickets_id)
                                    WHERE `glpi_tickets`.`is_deleted` = 0
                                    " . $gt . "
                                    " . $impacto . " 
                                    AND glpi_tickets.slas_id_ttr = $sla
                                    AND ( `glpi_tickets`.`date` > '$date_inicial_formater 00:00:00'
                                    AND `glpi_tickets`.`date` < '$date_inicial_formater 23:59:59')) as total");


            while ($rows = $DB->fetch_assoc($query)) {
                $row['dentro'][] = $rows['dentro'] == null ? 0 : $rows['dentro'];
                $row['fora'][] = $rows['fora'] == null ? 0 : $rows['fora'];
                $row['NRD'][] = $rows['NRD'] == null ? 0 : $rows['NRD'];
                $row['NRF'][] = $rows['NRF'] == null ? 0 : $rows['NRF'];
                $row['PED'][] = $rows['PED'] == null ? 0 : $rows['PED'];
                $row['total'][] = $rows['total'] == null ? 0 : $rows['total'];
            }
        }


        if (!empty($row)) {
            return $row;
        } else {
            return 0;
        }
    }
}
