<?php

class PluginDashboardHistoricTicket
{

    public static function getHistoricTicket($ticket_id)
    {

        /**
         * STATUS
         * 1 - Novo
         * 20 - Novo
         * 2 - Processando (Atribuido)
         * 3 - Processando (planejado)
         * 4 - Pendente
         * 5 - Solucionado
         * 6 - Fechado
         */

        $log = new Log();

        $result = $log->find(["itemtype = 'Ticket'", "items_id = {$ticket_id}"], ["id desc"]);

        foreach ($result as $key => $value) {

            if ($value['linked_action'] == 20 || $value['itemtype_link'] == 0) {

                $value['label'] = 'Novo';

                $row[] = $value;
            }

            if (empty($value['linked_action']) && $value['id_search_option'] != 150) {

                if ($value['new_value'] == 1) {
                    $value['label'] = 'Novo';
                    $row[] = $value;
                }
                if ($value['new_value'] == 2) {
                    $value['label'] = 'Processando (Atribuido)';
                    $row[] = $value;
                }
                if ($value['new_value'] == 3) {
                    $value['label'] = 'Processando (Planejado)';
                    $row[] = $value;
                }
                if ($value['new_value'] == 4) {
                    $value['label'] = 'Pendente';
                    $row[] = $value;
                }
                if ($value['new_value'] == 5) {
                    $value['label'] = 'Solucionado';
                    $row[] = $value;
                }
                if ($value['new_value'] == 6) {
                    $value['label'] = 'Fechado';
                    $row[] = $value;
                }
            }
        }
        // echo json_encode($row);
        return $row;
    }
}
