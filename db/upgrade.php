<?php
// This file is part of miniOrange moodle plugin - http://moodle.org/
//
// This Plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This Program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   auth_mo_saml
 * @copyright   2020  miniOrange
 * @category    document
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later, see license.txt
 */

 function xmldb_auth_mo_saml_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();



    if ($oldversion < 2023091200) {
       
        $currentconfig = (array)get_config('auth_mo_saml');
        $oldconfig = $DB->get_records('config_plugins', ['plugin' => 'auth/mo_saml']);

        // Convert old config items to new.
        foreach ($oldconfig as $item) {
            $DB->delete_records('config_plugins', array('id' => $item->id));
            set_config($item->name, $item->value, 'auth_mo_saml');
        }

        // Overwrite with any config that was created in the new format.
        foreach ($currentconfig as $key => $value) {
            set_config($key, $value, 'auth_mo_saml');
        }



        $config = get_config('auth_mo_saml');

        if(!isset($config->spentityid))
            set_config('spentityid',$CFG->wwwroot,'auth_mo_saml');
        set_config('idpconfigoption','Manual Configuration','auth_mo_saml');
        set_config('idpmetadata','','auth_mo_saml');
        set_config('accountmatcher','username','auth_mo_saml');

        $identity_providers = array();

        $identity_providers[0] = array();
        if(isset($config->idp_name))
            $identity_providers[0]['idp_name'] = $config->idp_name;
        if(isset($config->loginurl))
            $identity_providers[0]['saml_loginurl'] = $config->loginurl;
        if(isset($config->samlissuer))
            $identity_providers[0]['Idp_entityid'] = $config->samlissuer;
        if(isset($config->samlxcertificate))
            $identity_providers[0]['x509_certificate'] = $config->samlxcertificate;

        set_config('identity_providers',json_encode($identity_providers), 'auth_mo_saml');

        upgrade_plugin_savepoint(true, 2023091200, 'auth', 'mo_saml');
    }


    return true;
 }