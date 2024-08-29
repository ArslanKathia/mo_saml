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

$string['auth_mo_samltitle'] = 'miniOrange SAML SSO for moodle';
$string['auth_mo_samldescription'] = 'miniOrange SAML 2.0 Single Sign On (SSO) Plugin enables seamless SSO login into your Moodle sites via authentication through any SAML 2.0 compliant Identity Provider.';

$string['mo_saml_service_provider_metadata'] = 'Service Provider Metadata';
$string['mo_saml_service_provider_metadata_desc'] = "For configuring Moodle on your IdP, you have three options:";
$string['mo_saml_spentityid_name'] = "SP Entity-ID";
$string['mo_saml_spentityid_desc'] = "If you have already shared the below URLs or Metadata with your IdP, do <b>NOT</b> change SP EntityID. It might break your existing login flow.";
$string['mo_saml_spmetadata_url'] = 'SP Metadata URL';
$string['mo_saml_spmetadata_url_help'] = '<a href=\'{$a}\' target="_blank">View Service Provider Metadata</a>
<p>You can provide the metadata URL to your Identity Provider.</p><p>----------------------------------------------- OR -----------------------------------------------</p>';
$string['mo_saml_spmetadata_download'] = 'Download SP Metadata';
$string['mo_saml_spmetadata_download_help'] = '<a href=\'{$a}?download=1\'>Download Service Provider Metadata</a>
<p>You can download the plugin XML metadata and upload it on your Identity Provider.</p><p>----------------------------------------------- OR -----------------------------------------------</p>';

$string['mo_saml_sp_entityid'] = 'SP Entity-ID';
$string['mo_saml_sp_entityid_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">{$a}</p>';
$string['mo_saml_acs_url'] = 'ACS URL';
$string['mo_saml_acs_url_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">{$a}</p>';
$string['mo_saml_audience_uri'] = 'Audience URI';
$string['mo_saml_audience_uri_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">{$a}</p>';
$string['mo_saml_nameid_format'] = 'NameID Format';
$string['mo_saml_nameid_format_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
</p>';

$string['mo_saml_service_provider_setup'] = 'Service Provider Setup';
$string['mo_saml_service_provider_setup_desc'] = "To configure IdP metadata, you have two options: you can either fetch the metadata URL or XML directly in the IDP Metadata textbox, or manually configure the values.";
$string['mo_saml_idp_name'] = 'IDP Name';
$string['mo_saml_idp_name_desc'] = 'Identity Provider Name like Azure, Okta, Salesforce';
$string['mo_saml_radio_option_label'] = 'Select the Method';
$string['mo_saml_radio_option_desc'] = 'Select the option how you would like to save the IDP configuration';
$string['mo_saml_idp_config_option1'] = 'Metadata URL/XML';
$string['mo_saml_idp_config_option2'] = 'Manual Configuration';
$string['mo_saml_idp_metadata'] = 'IDP Metadata URL/XML';
$string['mo_saml_idp_metadata_desc'] = "----------------------------------------------- OR -----------------------------------------------";
$string['mo_saml_idp_entityid'] = 'IDP Entity-ID';
$string['mo_saml_idp_entityid_desc'] = 'Identity Provider Entity-ID or Issuer';
$string['mo_saml_login_url'] = 'SAML Login URL';
$string['mo_saml_login_url_desc'] = 'Single Sign-On Service URL of your IdP';
$string['mo_saml_x509_certificate'] = 'X.509 certificate';
$string['mo_saml_x509_certificate_desc'] = '<tr>
<td></td>
<td><b>NOTE:</b> Format of the certificate:<br/>-----BEGIN CERTIFICATE-----<br/>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br/>-----END CERTIFICATE-----<br/></td>
</tr>';

$string['mo_saml_logout_url'] = 'Logout URL';
$string['mo_saml_logout_url_desc'] = 'This feature is available in the Premium Plugin.';

$string['test_configuration'] = 'Test Configuration';
$string['test_configuration_desc'] = '<li><a href=\'{$a}\' target="_blank"> Click here</a> to go to <b>Manage authentication</b> page.</li>
<li>Enable the plugin by clicking on the <i class="fa fa-eye"></i> icon next to the plugins name under the Enable column.</li>
<li> Click on the <b>Test Settings</b> option.</li>';
$string['mo_saml_default_role_option1'] = 'Authenticated user';
$string['mo_saml_default_role_option2'] = 'Manager';
$string['mo_saml_default_role_option3'] = 'Course Creator';
$string['mo_saml_default_role_option4'] = 'Teacher';
$string['mo_saml_default_role_option5'] = 'Non-editing teacher';
$string['mo_saml_default_role_option6'] = 'Student';

$string['mo_saml_attribute_mapping'] = 'Attribute Mapping';
$string['mo_saml_attribute_mapping_desc'] = "The Attribute Mapping feature helps you to map the user attributes sent by the IDP to the Moodle user attributes.";
$string['mo_saml_username'] = 'Username';
$string['mo_saml_username_desc'] = '';
$string['mo_saml_email'] = 'Email';
$string['mo_saml_email_desc'] = '';
$string['mo_saml_firstname'] = 'Firstname';
$string['mo_saml_firstname_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">Available in <b><a href=\'{$a}\' target="_blank"> Premium</a></b></p>';
$string['mo_saml_lastname'] = 'Lastname';
$string['mo_saml_lastname_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">Available in <b><a href=\'{$a}\' target="_blank"> Premium</a></b></p>';
$string['mo_saml_phone'] = 'Phone';
$string['mo_saml_phone_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">Available in <b><a href=\'{$a}\' target="_blank"> Premium</a></b></p>';
$string['mo_saml_department'] = 'Department';
$string['mo_saml_department_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">Available in <b><a href=\'{$a}\' target="_blank"> Premium</a></b></p>';
$string['mo_saml_institution'] = 'Institution';
$string['mo_saml_institution_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">Available in <b><a href=\'{$a}\' target="_blank"> Premium</a></b></p>';
$string['mo_saml_address'] = 'Address';
$string['mo_saml_address_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">Available in <b><a href=\'{$a}\' target="_blank"> Premium</a></b></p>';

$string['mo_saml_role_mapping'] = 'Role Mapping';
$string['mo_saml_role_mapping_desc'] = "The Role Mapping allows you to provide user capabilities based on their IdP attribute Group values[<b><a href='https://plugins.miniorange.com/moodle-single-sign-on-sso/' target='_blank'>Available in Premium only</a></b>].";
$string['mo_saml_default_role'] = 'Default Role';
$string['mo_saml_default_role_desc'] = 'You can assign a default role to the users.';

$string['mo_saml_support_email'] = 'Contact us';
$string['mo_saml_support_email_desc'] = 'If you are facing any issues or would like to know about our Premium Products, you can reach out to us at <a href = "mailto: moodlesupport@xecurify.com">moodlesupport@xecurify.com</a>.';



$string['auth_mo_saml_create_or_update_warning'] = "When auto-provisioning or auto-update is enable,";
$string['auth_mo_saml_empty_required_value'] = "is a required attribute, provide a valid value";
$string['retriesexceeded'] = 'Maximum number of SAML connection retries exceeded  - there must be a problem with the Identity Service.<br />Please try again in a few minutes.';
$string['pluginauthfailed'] = 'The miniOrange SAML authentication plugin failed - user $a disallowed (no user auto-creation?) or dual login disabled.';
$string['pluginauthfailedusername'] = 'The miniOrange SAML authentication plugin failed - user $a disallowed due to invalid username format.';
$string['auth_mo_saml_username_email_error'] = 'The identity provider returned a set of data that does not contain the SAML username/email mapping field. Once of this field is required to login. <br />Please check your Username/Email Address Attribute Mapping configuration.';
$string['enable_multitenant'] = 'Enable multi-tenant support';
$string['enable_multitenant_desc'] = 'Allow users to log in from multiple Azure AD tenants';
$string['multitenant_idps'] = 'Multi-tenant IdP configurations';
$string['multitenant_idps_desc'] = 'JSON configuration for multiple IdPs';
