<?php

require "helper.class.php";

class PluginDashboardTicktsReopened
{

    // Valores da pesquisa do relatório
    private $saerch = [];

    public function __construct($saerch)
    {
        $this->saerch = $saerch;
    }

    //nome do plugin no menu
    public static function getMenuName()
    {
        return __('Dashboard - Relatório de Chamados Reabertos por Grupo');
    }

    //Define o Nome do Plugin
    public static function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        return ("Dashboard - Relatório de Chamados Reabertos por Grupo");
    }

    // retorna todas as entidades recursiva
    public function entityRecursive()
    {
        return PluginVreportsHelper::entityRecursive();
    }

    // retorna a entidade pesquisada
    public function entitySearch()
    {
        return $this->saerch['id_sel_ent'] == 0 ? $this->entityRecursive() : $this->saerch['id_sel_ent'];
    }

    // Retorna array para select de entidade
    public function getSelectEntity($id = -1)
    {
        $entityRecursive = $this->entityRecursive();
        global $DB;

        $sql_ent = "SELECT id, name, completename AS cname
                    FROM glpi_entities
                    WHERE id IN ({$entityRecursive})
                    ORDER BY cname ASC ";

        $result_ent = $DB->query($sql_ent);

        $arr_ent[0] = "---";
        foreach ($result_ent as $key => $entity) {
            $arr_ent[$entity['id']] = $entity['cname'];
        }

        if ($id > -1) {
            return $arr_ent[$id];
        } else {
            return $arr_ent;
        }
    }

    // Retorna array para select status
    public function getSelectStatus()
    {
        global $DB;

        // lista de status		
        $sql_sta = "SELECT DISTINCT status
                    FROM glpi_tickets
                    ORDER BY status ASC";

        $result_sta = $DB->query($sql_sta);

        $arr_sta[0] = "---";

        foreach ($result_sta as $status) {
            $arr_sta[$status['status']] = Ticket::getStatus($status['status']);
        }

        $arr_sta['notold']    = _x('status', 'Not solved');
        $arr_sta['notclosed'] = _x('status', 'Not closed');

        return $arr_sta;
    }

    // Retorna array para select Categoria
    public function getSelectCategory()
    {
        global $DB;

        // lista de categorias		
        $sql_cat = "SELECT id, completename AS name
                    FROM glpi_itilcategories
                    ORDER BY name ASC ";

        $result_cat = $DB->query($sql_cat);

        $arr_cat = array();
        $arr_cat[0] = "---";

        while ($row_cat = $DB->fetch_assoc($result_cat)) {
            $arr_cat[$row_cat['id']] = $row_cat['name'];
        }

        return $arr_cat;
    }

    // Retorna rray para select Tipo
    public function getSelectType()
    {
        $arr_tip = array();
        $arr_tip[0] = "---";
        $arr_tip[1] = __('Incident');
        $arr_tip[2] = __('Request');
        return $arr_tip;
    }

    // Retorna array para select prioridade
    public function getSelectPriority()
    {

        // lista de prioridade		
        $arr_pri[0] = "---";
        $arr_pri[1] = _x('priority', 'Very low');
        $arr_pri[2] = _x('priority', 'Low');
        $arr_pri[3] = _x('priority', 'Medium');
        $arr_pri[4] = _x('priority', 'High');
        $arr_pri[5] = _x('priority', 'Very high');
        $arr_pri[6] = _x('priority', 'Major');

        return $arr_pri;
    }

    // Retorna array para select Vencimento
    public function getSelectDue()
    {
        // lista vencimento	
        $arr_due[0] = "---";
        $arr_due[1] = __('Overdue', 'dashboard');
        $arr_due[2] = __('Within', 'dashboard');
        return $arr_due;
    }

    // Retorna array para select Fonte
    public function getSelectSource()
    {
        global $DB;

        // lista de origem		
        $sql_req = "SELECT id, name
                    FROM glpi_requesttypes
                    ORDER BY id ASC ";

        $result_req = $DB->query($sql_req);

        $arr_req[0] = "---";

        while ($row_req = $DB->fetch_assoc($result_req)) {
            $arr_req[$row_req['id']] = $row_req['name'];
        }

        return  $arr_req;
    }

    // Retorna array para select Grupo Resolvedor
    public function getSelectGroupSolver()
    {
        global $DB;

        $query_group = "SELECT
                        ticket_group.groups_id AS id_group,
                        grupo.name AS name_group
                        FROM glpi_groups_tickets AS ticket_group
                        INNER JOIN glpi_groups as grupo ON ticket_group.groups_id = grupo.id
                        WHERE ticket_group.type = 2
                        ORDER BY ticket_group.id DESC;";

        $result_group = $DB->query($query_group);

        // lista de tipos		
        $arr_drop[0] = "---";
        foreach ($result_group as $value) {
            $arr_drop[$value['id_group']] = $value['name_group'];
        }

        return $arr_drop;
    }

    // Retorna array para select Operação
    public function getSelectOperation()
    {
        // Query para Select operação
        // $query_operation = "SELECT 
        // 					location.id AS location_id,
        // 					location.name AS location_name
        // 					FROM
        // 						glpi_locations AS location
        // 					INNER JOIN glpi_tickets as ticket ON location.id = ticket.locations_id
        // 					ORDER BY ticket.id DESC";

        // $result_operation = $DB->query($query_operation);

        $arr_operation[0] = "---";

        // foreach ($result_operation as $result) {
        // 	$arr_operation[$result["location_id"]] = $result["location_name"];
        // }

        return $arr_operation;
    }

    // Retorna array para select impacto
    public function getSelectImpact()
    {
        $arr_impact[0] = "---";
        for ($i = 1; $i <= 5; $i++) {
            $arr_impact[$i] = Ticket::getImpactName($i);
        }

        return $arr_impact;
    }

    // Retorna query para where impacto
    public function getQueryImpact()
    {
        if ($this->saerch['id_sel_impact'] != 0) {
            return "AND t.impact = '" . $this->saerch['id_sel_impact'] . "' ";
        }
        return "";
    }



    // Retorna array para select Localização
    public function getSelectLocation()
    {
        global $DB;
        // Query para Select operação
        $query_localizacao = "SELECT 
                                location.id AS location_id,
                                location.name AS location_name
                                FROM
                                    glpi_locations AS location
                                INNER JOIN glpi_tickets as ticket ON location.id = ticket.locations_id
                                ORDER BY ticket.id DESC";

        $result_localizacao = $DB->query($query_localizacao);

        $arr_localizacao[0] = "---";

        foreach ($result_localizacao as $result) {
            $arr_localizacao[$result["location_id"]] = $result["location_name"];
        }

        return $arr_localizacao;
    }

    // nome da icone que vai ser usada no status
    public function getStatusIcon($status)
    {
        if ($status == "1") {
            return "new";
        }
        if ($status == "2") {
            return "assign";
        }
        if ($status == "3") {
            return "plan";
        }
        if ($status == "4") {
            return "waiting";
        }
        if ($status == "5") {
            return "solved";
        }
        if ($status == "6") {
            return "closed";
        }

        return "";
    }

    // Converte id do tipo de chamado em nome
    public function getTypeTicketName($id)
    {
        if ($id == 1) {
            return __('Incident');
        } else {
            return __('Request');
        }
    }

    // Pega requerente do tickt
    public function getRquerentTicket($id)
    {
        global $DB;

        //requerente	
        $sql_user = "SELECT DISTINCT glpi_tickets.id AS id, glpi_tickets.name AS title, glpi_tickets.content AS content, glpi_users.firstname AS name, glpi_users.realname AS sname
                        FROM glpi_tickets_users , glpi_tickets, glpi_users
                        WHERE glpi_tickets.id = glpi_tickets_users.tickets_id
                        AND glpi_tickets.id = {$id}
                        AND glpi_tickets_users.users_id = glpi_users.id
                        AND glpi_tickets_users.type = 1 ";

        $result_user = $DB->query($sql_user);

        return $DB->fetch_assoc($result_user);
    }

    // Pega tecnico do tickt
    public function getTecnicoTicket($id)
    {
        global $DB;
        //tecnico	
        $sql_tec = "SELECT DISTINCT glpi_tickets.id AS id, glpi_users.firstname AS name, glpi_users.realname AS sname
                        FROM glpi_tickets_users , glpi_tickets, glpi_users
                        WHERE glpi_tickets.id = glpi_tickets_users.tickets_id
                        AND glpi_tickets.id = {$id}
                        AND glpi_tickets_users.users_id = glpi_users.id
                        AND glpi_tickets_users.type = 2 ";

        $result_tec = $DB->query($sql_tec);

        return $DB->fetch_assoc($result_tec);
    }

    // Pega origem do ticket
    public function getOrigemTicket($id)
    {
        global $DB;

        //origem	
        $sql_req = "SELECT DISTINCT glpi_tickets.id AS id, glpi_requesttypes.name AS name
                            FROM glpi_tickets , glpi_requesttypes
                            WHERE glpi_tickets.requesttypes_id = glpi_requesttypes.id
                            AND glpi_tickets.id = {$id} ";

        $result_req = $DB->query($sql_req);
        return $DB->fetch_assoc($result_req);
    }

    // Pega a Categoria do ticket
    public function getCategoryTicket($id)
    {
        global $DB;
        //check time_to_resolve	
        $sql_due = "SELECT time_to_resolve, closedate, solvedate 
                            FROM glpi_tickets
                            WHERE glpi_tickets.is_deleted = 0
                            AND glpi_tickets.id = {$id} ";

        $result_due = $DB->query($sql_due);
        return $DB->fetch_assoc($result_due);
    }

    // Pega nome da categoria do ticket
    public function getCategoryNameTicket($id)
    {
        global $DB;
        //categoria	
        $sql_cat = "SELECT DISTINCT glpi_tickets.id AS id, glpi_itilcategories.completename AS name
                             FROM glpi_tickets, glpi_itilcategories
                             WHERE glpi_tickets.itilcategories_id = glpi_itilcategories.id
                             AND glpi_tickets.id = {$id} ";

        $result_cat = $DB->query($sql_cat);
        return $DB->fetch_assoc($result_cat);
    }

    // Converte id do tipo de chamado em nome
    public function getPriorityName($id)
    {
        if ($id == "1") {
            return _x('priority', 'Very low');
        }
        if ($id == "2") {
            return _x('priority', 'Low');
        }
        if ($id == "3") {
            return _x('priority', 'Medium');
        }
        if ($id == "4") {
            return _x('priority', 'High');
        }
        if ($id == "5") {
            return _x('priority', 'Very high');
        }
        if ($id == "6") {
            return _x('priority', 'Major');
        }
    }

    // Retorna o periodo de acordo com o status selecionado na pesquisa
    public function getQueryPeriod()
    {

        if ($this->saerch['id_sel_sta'] == 5) {
            return "AND t.solvedate " . $this->saerch['date_beetwen'] . " ";
        } elseif ($this->saerch['id_sel_sta'] == 6) {
            return "AND t.closedate " . $this->saerch['date_beetwen'] . " ";
        } else {
            return "AND t.date " . $this->saerch['date_beetwen'] . " ";
        }

        return '';
    }

    // Retorna query where para select status
    public function getQueryState()
    {
        if ($this->saerch['id_sel_sta'] == 'notclosed') {

            return "AND t.status <> 6";
        } elseif ($this->saerch['id_sel_sta'] == 'notold') {

            return "AND t.status NOT IN ('5','6')";
        } elseif ($this->saerch['id_sel_sta'] != 0) {

            return "AND t.status = " . $this->saerch['id_sel_sta'];
        }

        return '';
    }

    // Retorna query where para select vencimento
    public function getQueryDue()
    {

        if ($this->saerch['id_sel_due'] == 1) {
            return "AND time_to_resolve < solvedate";
        }
        if ($this->saerch['id_sel_due'] == 2) {
            return "AND time_to_resolve >= solvedate";
        }

        return '';
    }

    // Retorna o query where para select resolvedor
    public function getQueryResolver()
    {

        if ($this->saerch["id_sel_resolver"] != 0) {

            return "AND t.id IN (SELECT DISTINCT tickets_id FROM glpi_groups_tickets WHERE groups_id = '" . $this->saerch['id_sel_resolver'] . "' AND type = 2 ORDER BY id DESC) ";
        }

        return '';
    }

    // Porcentagem de tickets reabertos
    public function getPorcentageTicket($tickets_reopen){

        global $DB;
        $query = "SELECT 
                    COUNT(DISTINCT t.id) AS ticket
                  FROM
                    glpi_tickets AS t
                  WHERE t.is_deleted = 0
                  AND t.status IN ('5','6')
                  ORDER BY t.id DESC";

        $tickets = $DB->result($DB->query($query), 0, 'ticket') + 0;

        if($tickets_reopen >= $tickets){
            return 100;
        }else if($tickets_reopen <= 0){
            return 0;
        }else{
            $value = ($tickets_reopen / $tickets) * 100;
            return $value > 100 ? 100 : $value;
        }

        
    }

    public function getCountState()
    {
        global $DB;

        // Query Where
        $entidade = "AND t.entities_id IN (" . $this->entitySearch() . ")";
        $period = $this->getQueryPeriod(); // Beetwen do periodo referente a pesquisa
        $id_sta = $this->getQueryState(); // Retorna o and para id do status
        $id_due = $this->getQueryDue(); // Retorna and para chamados vencidos
        $id_resolver = $this->getQueryResolver(); // Retorna and para resolvedor
        $id_impact = $this->getQueryImpact();
        // ID Like
        $id_req = $this->saerch['id_sel_font'] == 0 ? '' : $this->saerch['id_sel_font'];
        $id_pri = $this->saerch['id_sel_pri'] == 0 ? '' : $this->saerch['id_sel_pri'];
        $id_cat = $this->saerch['id_sel_cat'] == 0 ? '' : $this->saerch['id_sel_cat'];
        $id_tip = $this->saerch['id_sel_typ'] == 0 ? '' : $this->saerch['id_sel_typ'];
        $id_localizacao = $this->saerch['id_sel_location'] == 0 ? '' : $this->saerch['id_sel_location'];

        //count by status
        $query_stat = "SELECT
                        COUNT(DISTINCT t.id) AS ticket,
                        SUM(case when t.status = 1 then 1 else 0 end) AS new,
                        SUM(case when t.status = 2 then 1 else 0 end) AS assig,
                        SUM(case when t.status = 3 then 1 else 0 end) AS plan,
                        SUM(case when t.status = 4 then 1 else 0 end) AS pend,
                        SUM(case when t.status = 5 then 1 else 0 end) AS solve,
                        SUM(case when t.status = 6 then 1 else 0 end) AS close
                        FROM glpi_tickets AS t
                        INNER JOIN glpi_itilsolutions AS s ON (s.itemtype = 'Ticket' AND s.items_id = t.id)
                        WHERE 
                            t.is_deleted = 0
                            {$entidade} 
                            {$period}
                            {$id_sta}
                            {$id_due}
                            {$id_impact}
                            {$id_resolver}
                            AND t.requesttypes_id LIKE '%{$id_req}'
                            AND t.priority LIKE '%{$id_pri}'
                            AND t.itilcategories_id LIKE '%{$id_cat}'
                            AND t.type LIKE '%{$id_tip}'
                            AND t.locations_id LIKE '%{$id_localizacao}' 
                        ORDER BY t.id DESC ";

        $result_stat = $DB->query($query_stat);

        $tickets = $DB->result($result_stat, 0, 'ticket') + 0;
        $porcent = $this->getPorcentageTicket($tickets);

        return array(
            'ticket' => $tickets,
            'new' => $DB->result($result_stat, 0, 'new') + 0,
            'assig' => $DB->result($result_stat, 0, 'assig') + 0,
            'plan' => $DB->result($result_stat, 0, 'plan') + 0,
            'pend' => $DB->result($result_stat, 0, 'pend') + 0,
            'solve' => $DB->result($result_stat, 0, 'solve') + 0,
            'close' => $DB->result($result_stat, 0, 'close') + 0,
            'porcent' => $porcent
        );
    }

    // Tickets reabertos de acordo com a pesquisa
    public function getTickets()
    {
        global $DB;

        // Query Where
        $entidade = "AND t.entities_id IN (" . $this->entitySearch() . ")";
        $period = $this->getQueryPeriod(); // Beetwen do periodo referente a pesquisa
        $id_sta = $this->getQueryState(); // Retorna o and para id do status
        $id_due = $this->getQueryDue(); // Retorna and para chamados vencidos
        $id_resolver = $this->getQueryResolver(); // Retorna and para resolvedor
        $id_impact = $this->getQueryImpact();
        // ID Like
        $id_req = $this->saerch['id_sel_font'] == 0 ? '' : $this->saerch['id_sel_font'];
        $id_pri = $this->saerch['id_sel_pri'] == 0 ? '' : $this->saerch['id_sel_pri'];
        $id_cat = $this->saerch['id_sel_cat'] == 0 ? '' : $this->saerch['id_sel_cat'];
        $id_tip = $this->saerch['id_sel_typ'] == 0 ? '' : $this->saerch['id_sel_typ'];
        $id_localizacao = $this->saerch['id_sel_location'] == 0 ? '' : $this->saerch['id_sel_location'];

        $sql_cham = "SELECT DISTINCT 
                            t.id,
                            t.entities_id,
                            t.name,
                            t.date,
                            t.closedate,
                            t.solvedate,
                            t.status,
                            t.users_id_recipient,
                            t.requesttypes_id,
                            t.priority,
                            t.itilcategories_id,
                            t.type,
                            t.time_to_resolve 
                        FROM glpi_tickets AS t
                        INNER JOIN glpi_itilsolutions AS s ON (s.itemtype = 'Ticket' AND s.items_id = t.id)
                        WHERE 
                            t.is_deleted = 0
                            {$entidade} 
                            {$period}
                            {$id_sta}
                            {$id_due}
                            {$id_impact}
                            {$id_resolver}
                            AND t.requesttypes_id LIKE '%{$id_req}'
                            AND t.priority LIKE '%{$id_pri}'
                            AND t.itilcategories_id LIKE '%{$id_cat}'
                            AND t.type LIKE '%{$id_tip}'
                            AND t.locations_id LIKE '%{$id_localizacao}'
                        ORDER BY t.id DESC ";

        return $DB->query($sql_cham);
    }
}
