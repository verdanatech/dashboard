<?php

use Glpi\Http\Firewall;

function plugin_init_dashboard()
{
    global $PLUGIN_HOOKS, $LANG;

    $PLUGIN_HOOKS['csrf_compliant']['dashboard'] = true;
    $PLUGIN_HOOKS['config_page']['dashboard'] = 'public/index.php';

    // Verifica o perfil ativo na sessão
    $isCentralInterface = isset($_SESSION['glpiactiveprofile']['interface'])
        && $_SESSION['glpiactiveprofile']['interface'] === 'central';

    if ($isCentralInterface) {
        $PLUGIN_HOOKS['redefine_menus']['dashboard'] = 'plugin_dashboard_redefine_menus';
    }

    // Apenas para administradores (opcional)
    if ($isCentralInterface && isset($_SESSION['glpiactiveprofile']['id']) && $_SESSION['glpiactiveprofile']['id'] == 11) {
        Plugin::registerClass('PluginDashboardConfig', [
            'addtabon' => ['Entity']
        ]);
    }
}


function plugin_version_dashboard()
{
    global $DB, $LANG;

    return array(
        'name'            => __('ST-Dashboard', 'dashboard'),
        'version'             => '2.0.1',
        'author'            => '<a href="mailto:contato@servicetic.com.br">Stevenes Donato/99NET/Service TIC</b></a>',
        'license'             => 'GPLv2+',
        'homepage'            => 'https://github.com/serviceticst/glpi-plugin-dashboard/releases',
        'minGlpiVersion'    => '11.0'
    );
}


function plugin_dashboard_check_prerequisites()
{
    if (version_compare(GLPI_VERSION, '11.0', '>=')) {
        return true;
    } else {
        echo "GLPI version NOT compatible. Requires GLPI >= 11.0";
    }
}


function plugin_dashboard_check_config($verbose = false)
{
    if ($verbose) {
        echo 'Installed / not configured';
    }
    return true;
}
