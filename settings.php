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

use auth_mo_saml\admin\setting_button;
use auth_mo_saml\admin\setting_textonly;
use auth_mo_saml\admin\setting_idp_metadata;
use auth_mo_saml\admin\setting_fetch_values;

defined('MOODLE_INTERNAL') || die();


if ($hassiteconfig) {

  global $CFG;

  $config = get_config('auth_mo_saml');

  require_once($CFG->dirroot . '/auth/mo_saml/locallib.php');

  $settings->add(
    new admin_setting_heading(
      'auth_mo_saml/pluginname',
      '',
      new lang_string('auth_mo_samldescription', 'auth_mo_saml')
    )
  );

  // Service Provider Metadata Tab

  $settings->add(
    new admin_setting_heading(
      'auth_mo_saml/service_provider_metadata',
      new lang_string('mo_saml_service_provider_metadata', 'auth_mo_saml'),
      new lang_string('mo_saml_service_provider_metadata_desc', 'auth_mo_saml')
    )
  );

  $settings->add(
    new admin_setting_configtext(
      'auth_mo_saml/spentityid',
      get_string('mo_saml_spentityid_name', 'auth_mo_saml'),
      get_string('mo_saml_spentityid_desc', 'auth_mo_saml'),
      $CFG->wwwroot,
      PARAM_RAW_TRIMMED
    )
  );

  $settings->add(new setting_textonly(
    'auth_mo_saml/spm_url',
    get_string('mo_saml_spmetadata_url', 'auth_mo_saml'),
    get_string('mo_saml_spmetadata_url_help', 'auth_mo_saml', $CFG->wwwroot . '/auth/mo_saml/serviceprovider/spmetadata.php')
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/spm_xml',
    get_string('mo_saml_spmetadata_download', 'auth_mo_saml'),
    get_string('mo_saml_spmetadata_download_help', 'auth_mo_saml', $CFG->wwwroot . '/auth/mo_saml/serviceprovider/spmetadata.php')
  ));

  if (isset($config->spentityid) && !empty($config->spentityid)) {
    $spentityid = $config->spentityid;
  } else {
    $spentityid = $CFG->wwwroot;
  }

  $settings->add(new setting_textonly(
    'auth_mo_saml/spm_entityid',
    get_string('mo_saml_sp_entityid', 'auth_mo_saml'),
    get_string('mo_saml_sp_entityid_desc', 'auth_mo_saml', $spentityid)
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/spm_acsurl',
    get_string('mo_saml_acs_url', 'auth_mo_saml'),
    get_string('mo_saml_acs_url_desc', 'auth_mo_saml', $CFG->wwwroot . '/auth/mo_saml/index.php')
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/spm_audienceuri',
    get_string('mo_saml_audience_uri', 'auth_mo_saml'),
    get_string('mo_saml_audience_uri_desc', 'auth_mo_saml', $CFG->wwwroot)
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/spm_nameidformat',
    get_string('mo_saml_nameid_format', 'auth_mo_saml'),
    get_string('mo_saml_nameid_format_desc', 'auth_mo_saml', $CFG->wwwroot)
  ));


  // Service Provider Setup Tab

  $settings->add(
    new admin_setting_heading(
      'auth_mo_saml/service_provider_setup',
      new lang_string('mo_saml_service_provider_setup', 'auth_mo_saml'),
      new lang_string('mo_saml_service_provider_setup_desc', 'auth_mo_saml')
    )
  );

  $settings->add(
    new admin_setting_configtext(
      'auth_mo_saml/identityname',
      get_string('mo_saml_idp_name', 'auth_mo_saml'),
      get_string('mo_saml_idp_name_desc', 'auth_mo_saml'),
      '',
      PARAM_RAW_TRIMMED
    )
  );

  $settings->add(new admin_setting_configselect(
    'auth_mo_saml/idpconfigoption',
    get_string('mo_saml_radio_option_label', 'auth_mo_saml'),
    get_string('mo_saml_radio_option_desc', 'auth_mo_saml'),
    'mo_saml_idp_config_option2',
    array(
      'Manual Configuration' => get_string('mo_saml_idp_config_option2', 'auth_mo_saml'),
      'Metadata URL' => get_string('mo_saml_idp_config_option1', 'auth_mo_saml'),
    )
  ));

  $settings->add(
    new setting_idp_metadata(
      'auth_mo_saml/idpmetadata',
      get_string('mo_saml_idp_metadata', 'auth_mo_saml'),
      get_string('mo_saml_idp_metadata_desc', 'auth_mo_saml'),
      '',
      PARAM_RAW,
      80,
      5
    )
  );

  $settings->add(
    new admin_setting_configtext(
      'auth_mo_saml/samlissuer',
      get_string('mo_saml_idp_entityid', 'auth_mo_saml'),
      get_string('mo_saml_idp_entityid_desc', 'auth_mo_saml'),
      '',
      PARAM_RAW_TRIMMED
    )
  );

  $settings->add(
    new admin_setting_configtext(
      'auth_mo_saml/loginurl',
      get_string('mo_saml_login_url', 'auth_mo_saml'),
      get_string('mo_saml_login_url_desc', 'auth_mo_saml'),
      '',
      PARAM_RAW_TRIMMED
    )
  );

  $settings->add(
    new admin_setting_configtextarea(
      'auth_mo_saml/samlxcertificate',
      get_string('mo_saml_x509_certificate', 'auth_mo_saml'),
      get_string('mo_saml_x509_certificate_desc', 'auth_mo_saml'),
      '',
      PARAM_RAW_TRIMMED
    )
  );

  // $settings->add(new setting_fetch_values(
  //     'auth_mo_saml/certificate_note',
  //     get_string('mo_saml_certificate_note', 'auth_mo_saml'),
  //     get_string('mo_saml_certificate_note_desc', 'auth_mo_saml')
  //     )
  // );

  $settings->add(
    new setting_fetch_values(
      'auth_mo_saml/testconfiguration',
      get_string('test_configuration', 'auth_mo_saml'),
      get_string('test_configuration_desc', 'auth_mo_saml', $CFG->wwwroot . '/admin/settings.php?section=manageauths')
    )
  );

  // Attribute Mapping

  $settings->add(
    new admin_setting_heading(
      'auth_mo_saml/attribute_mapping',
      new lang_string('mo_saml_attribute_mapping', 'auth_mo_saml'),
      new lang_string('mo_saml_attribute_mapping_desc', 'auth_mo_saml')
    )
  );

  $settings->add(
    new admin_setting_configtext(
      'auth_mo_saml/usernamemap',
      get_string('mo_saml_username', 'auth_mo_saml'),
      get_string('mo_saml_username_desc', 'auth_mo_saml'),
      '',
      PARAM_RAW_TRIMMED
    )
  );

  $settings->add(
    new admin_setting_configtext(
      'auth_mo_saml/emailmap',
      get_string('mo_saml_email', 'auth_mo_saml'),
      get_string('mo_saml_email_desc', 'auth_mo_saml'),
      '',
      PARAM_RAW_TRIMMED
    )
  );

  $settings->add(new setting_textonly(
    'auth_mo_saml/firstname',
    get_string('mo_saml_firstname', 'auth_mo_saml'),
    get_string('mo_saml_firstname_desc', 'auth_mo_saml', 'https://plugins.miniorange.com/moodle-single-sign-on-sso/')
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/lastname',
    get_string('mo_saml_lastname', 'auth_mo_saml'),
    get_string('mo_saml_lastname_desc', 'auth_mo_saml', 'https://plugins.miniorange.com/moodle-single-sign-on-sso/')
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/institution',
    get_string('mo_saml_institution', 'auth_mo_saml'),
    get_string('mo_saml_institution_desc', 'auth_mo_saml', 'https://plugins.miniorange.com/moodle-single-sign-on-sso/')
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/department',
    get_string('mo_saml_department', 'auth_mo_saml'),
    get_string('mo_saml_department_desc', 'auth_mo_saml', 'https://plugins.miniorange.com/moodle-single-sign-on-sso/')
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/phone',
    get_string('mo_saml_phone', 'auth_mo_saml'),
    get_string('mo_saml_phone_desc', 'auth_mo_saml', 'https://plugins.miniorange.com/moodle-single-sign-on-sso/')
  ));

  $settings->add(new setting_textonly(
    'auth_mo_saml/address',
    get_string('mo_saml_address', 'auth_mo_saml'),
    get_string('mo_saml_address_desc', 'auth_mo_saml', 'https://plugins.miniorange.com/moodle-single-sign-on-sso/')
  ));


  // Role Mapping

  $settings->add(
    new admin_setting_heading(
      'auth_mo_saml/role_mapping',
      new lang_string('mo_saml_role_mapping', 'auth_mo_saml'),
      new lang_string('mo_saml_role_mapping_desc', 'auth_mo_saml', 'https://plugins.miniorange.com/moodle-single-sign-on-sso/')
    )
  );

  $default_role = new admin_setting_configselect(
    'auth_mo_saml/defaultrolemap',
    get_string('mo_saml_default_role', 'auth_mo_saml'),
    get_string('mo_saml_default_role_desc', 'auth_mo_saml'),
    'mo_saml_default_role_option1',
    array(
      'user' => get_string('mo_saml_default_role_option1', 'auth_mo_saml'),
      'manager' => get_string('mo_saml_default_role_option2', 'auth_mo_saml'),
      'coursecreator' => get_string('mo_saml_default_role_option3', 'auth_mo_saml'),
      'editingteacher' => get_string('mo_saml_default_role_option4', 'auth_mo_saml'),
      'teacher' => get_string('mo_saml_default_role_option5', 'auth_mo_saml'),
      'student' => get_string('mo_saml_default_role_option6', 'auth_mo_saml'),
    )
  );
  $settings->add($default_role);

  $settings->add(
    new admin_setting_heading(
      'auth_mo_saml/support',
      new lang_string('mo_saml_support_email', 'auth_mo_saml'),
      new lang_string('mo_saml_support_email_desc', 'auth_mo_saml')
    )
  );

  // Add a setting to enable/disable multi-tenant support
  $settings->add(new admin_setting_configcheckbox(
    'auth_mo_saml/enable_multitenant',
    get_string('enable_multitenant', 'auth_mo_saml'),
    get_string('enable_multitenant_desc', 'auth_mo_saml'),
    0
  ));

  // Add a setting to configure multiple IdPs
  $settings->add(new admin_setting_configtextarea(
    'auth_mo_saml/multitenant_idps',
    get_string('multitenant_idps', 'auth_mo_saml'),
    get_string('multitenant_idps_desc', 'auth_mo_saml'),
    '',
    PARAM_RAW
  ));
}
