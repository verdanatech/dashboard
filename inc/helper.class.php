<?php

class PluginVreportsHelper
{
    public $entityRecursive;

    public function __construct()
    {
        $this->entityRecursive = $this->entityRecursive();
    }

    public static function entityRecursive()
    {
        $entity = '';
        $entity =  $_SESSION['glpiactive_entity'];

        //verificando se a entidade atual esta no status de recurcividade
        if ($_SESSION['glpiactive_entity_recursive'] || $_SESSION['glpishowallentities']) {
            $entity = $_SESSION['glpiactiveentities_string'];
        }

        return $entity;
    }
}
