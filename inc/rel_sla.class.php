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
        $groups = implode(",", $group);
        $data_inicial_time = strtotime($data_inicial);
        $data_final_time = strtotime($data_final);
        $data_atual = date("Y-m-d");
        $slaid = "";
        $gt = "";
        if ($impact != 0) {
            $slaid = " AND t.impact LIKE '%$impact%'";
        }

        if ($groups != 0) {
            $gt = " AND gt.groups_id IN  ($groups)";
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
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN ' $date_inicial_formater 00:00:00' AND '$date_inicial_formater 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            AND t.time_to_resolve> t.closedate 
            ORDER BY t.id DESC LIMIT 1) AS dentro,
            
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  '$date_inicial_formater 00:00:00' AND '$date_inicial_formater 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            AND t.is_deleted = 0
            AND t.time_to_resolve < t.closedate 
            ORDER BY t.id DESC LIMIT 1) AS fora,
        
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $date_inicial_formater 00:00:00' AND ' $date_inicial_formater 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $gt . "
            " . $slaid . "
            AND t.is_deleted = 0
            AND t.status < 5
            AND t.time_to_resolve > '$data_atual  00:00:00'   
           ORDER BY t.id DESC LIMIT 1) AS NRD,
                        
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $date_inicial_formater 00:00:00' AND ' $date_inicial_formater 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $slaid . "
            " . $gt . "
            AND t.is_deleted = 0
            AND t.status < 5
            AND t.time_to_resolve < '$data_atual  00:00:00'   
            ORDER BY t.id DESC LIMIT 1) as NRF,
            
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $date_inicial_formater 00:00:00' AND ' $date_inicial_formater 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $slaid . "
            " . $gt . "
            AND t.is_deleted = 0
            AND t.status = 4  
            AND t.time_to_resolve > '$data_atual  00:00:00'  
            ORDER BY t.id DESC LIMIT 1) as PED,
            
            (SELECT COUNT(DISTINCT t.id) FROM glpi_tickets as t
            LEFT JOIN glpi_groups_tickets as gt on (t.id = gt.tickets_id)
            WHERE t.date BETWEEN  ' $date_inicial_formater 00:00:00' AND ' $date_inicial_formater 23:59:59'
            AND t.slas_id_ttr = $sla
            " . $slaid . "
            " . $gt . "
            AND t.is_deleted = 0
            AND t.status =4
            AND t.time_to_resolve < '$data_atual  00:00:00'  
            ORDER BY t.id DESC LIMIT 1) as PEF");



            while ($rows = $DB->fetch_assoc($query)) {
                $row['dentro'][] = $rows['dentro'];
                $row['fora'][] = $rows['fora'];
                $row['NRD'][] = $rows['NRD'];
                $row['NRF'][] = $rows['NRF'];
                $row['PED'][] = $rows['PED'];
                $row['PEF'][] = $rows['PEF'];
            }
        }

        if (!empty($row)) {
            return $row;
        } else {
            return 0;
        }
    }
}
