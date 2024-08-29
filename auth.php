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

 defined('MOODLE_INTERNAL') || die();
 global $CFG;

 require_once($CFG->libdir.'/authlib.php');

 /**
 * This class contains authentication plugin method
 *
 * @package    mo_saml
 * @category   authentication
 * @copyright  2020 miniOrange
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_mo_saml extends auth_plugin_base {

    public function __construct() {
        $this->authtype = 'mo_saml';
        $this->config = get_config('auth_mo_saml');
    }


    public function user_login($username, $password) {
        global $SESSION;
        if (isset($SESSION->mo_saml_attributes)) {
            return true;
        }
        return false;
    }

    public function obtain_roles() {
        $roles = 'user';
        if (isset($this->config->defaultrolemap) && !empty($this->config->defaultrolemap)) {
            $roles = $this->config->defaultrolemap;
        }
        return $roles;
    }

    public function sync_roles($user) {
        global $CFG, $DB;
        $defaultrole = $this->obtain_roles();

        if ('siteadmin' == $defaultrole) {

            $siteadmins = explode(',', $CFG->siteadmins);
            if (!in_array($user->id, $siteadmins)) {
                $siteadmins[] = $user->id;
                $newadmins = implode(',', $siteadmins);
                set_config('siteadmins', $newadmins);
            }
        }

		//consider $roles as the groups returned from IdP

		$checkrole = false;

		if($checkrole == false){
			$syscontext = context_system::instance();
			$assignedrole = $DB->get_record('role', array('shortname' => $defaultrole), '*', MUST_EXIST);
			role_assign($assignedrole->id, $user->id, $syscontext);
        }
    }


    public function get_userinfo($username = null) {
        global $SESSION;
        $samlattributes = $SESSION->mo_saml_attributes;
        
        // Reading saml attributes from session varible assigned before.
        $nameid = $SESSION->mo_saml_nameID; // $SESSION->mo_saml_nameID has been set to NameID returned of user
        $mapping = $this->get_attributes();
        // Plugin attributes mapped values coming from get_attributes method of this class.
        if (empty($samlattributes)) {
            $username = $nameid;
            $email = $username;
        } else {
            // If saml is not empty.
            $usernamemapping = $mapping['username'];
            $mailmapping = $mapping['email'];
            if (!empty($usernamemapping) && isset($samlattributes[$usernamemapping]) && !empty($samlattributes[$usernamemapping][0])) {
                $username = $samlattributes[$usernamemapping][0];
            }
            if (!empty($mailmapping) && isset($samlattributes[$mailmapping]) && !empty($samlattributes[$mailmapping][0])) {
                $email = $samlattributes[$mailmapping][0];
            }
        }
        $user = array();
        // This array contain and return the value of attributes which are mapped.
        if (!empty($username)) {
            $user['username'] = $username;
        }
        if (!empty($email)) {
            $user['email'] = $email;
        }

		$pluginconfig = get_config('auth/mo_saml');
        $accountmatcher = "username";

        if (empty($accountmatcher)) {
            // Saml account matcher define which attribute is responsible for account creation.
            $accountmatcher = 'username';
            // Saml matcher is email if not selected.
        }
        if (($accountmatcher == 'username' && empty($user['username']) ||
            ($accountmatcher == 'email' && empty($user['email'])))) {
            $user = false;
        }

        return $user;
    }

    public function get_attributes() 
    {

        if(isset($this->config->usernamemap))
        {
            $username = $this->config->usernamemap;
        }
        else
        {
            $username = '';
        }
        if(isset($this->config->emailmap))
        {
            $email = $this->config->emailmap;
        }
        else
        {
            $email = '';
        }

        $attributes = array (
            "username" =>$username,
            "email" => $email,

        );
        return $attributes;
    }


 // Hook for overriding behaviour of login page.

    public function loginpage_idp_list($wantsurl) {
        global $CFG;

        $idplist = [];

        $config = get_config('auth_mo_saml');

        $idpurl = $CFG->wwwroot.'/auth/mo_saml/index.php';
        $idpname = $config->identityname;

        if(!empty($idpname)){

        $idpiconurl = null;
    
        $idpicon = new pix_icon('i/user', 'Login');

        $idplist[] = [
            'url'  => $idpurl,
            'icon' => $idpicon,
            'iconurl' => $idpiconurl,
            'name' => $idpname,
        ];

        }

        return $idplist;
    }

    public function test_settings() {
        global $CFG;
        $config = get_config('auth_mo_saml');
        ?>
        <table style="width: 690px; height: 70px;">
        <tr > 
            <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Identity Providers</th>
            <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Test Configuation</th>
        </tr>
        <tr>
            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                <?php  if ($config->identityname) { echo $config->identityname;} ?> 
            </td>
            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;" >
                <input type="button" class="button button-primary button-large" name="test"
                title="You can only test your Configuration after saving your Service Provider Settings."
                onclick="show_test_window();"  value="Test configuration"/>
            </td>
        </tr>
        <script>
            function show_test_window() {
            var myWindow = window.open("<?php echo $CFG->wwwroot."/auth/mo_saml/index.php".'/?option=testConfig'; ?>",
            "TEST SAML IDP", "scrollbars=1, width=800, height=600");
            }
        </script>
    <?php
    }

}