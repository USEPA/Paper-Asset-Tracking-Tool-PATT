<?php


include "\102\141\163\151\143\105\x6e\x75\x6d\x2e\160\x68\160";
class mo_options_enum_sso_login extends BasicEnum
{
    const Relay_state = "\155\157\137\x73\x61\155\154\x5f\x72\145\154\141\x79\137\x73\x74\x61\x74\145";
    const Redirect_Idp = "\155\157\137\163\141\155\x6c\x5f\x72\x65\147\151\x73\x74\145\162\x65\144\137\157\156\x6c\171\x5f\x61\x63\x63\145\x73\163";
    const Force_authentication = "\x6d\x6f\137\163\141\155\x6c\137\146\x6f\x72\143\145\x5f\x61\x75\x74\150\145\156\164\x69\143\141\x74\151\157\x6e";
    const Enable_access_RSS = "\155\x6f\137\163\141\155\x6c\137\x65\156\141\x62\154\145\137\x72\x73\163\x5f\141\x63\x63\145\x73\163";
    const Auto_redirect = "\155\x6f\137\163\x61\155\x6c\x5f\x65\x6e\141\x62\154\145\x5f\154\x6f\147\151\x6e\x5f\x72\145\x64\151\162\x65\x63\x74";
    const Allow_wp_signin = "\x6d\157\x5f\163\x61\155\x6c\x5f\x61\x6c\154\157\167\137\167\160\137\163\151\147\x6e\x69\x6e";
    const Custom_login_button = "\x6d\x6f\x5f\x73\141\x6d\154\x5f\143\165\163\164\x6f\155\x5f\154\157\x67\151\156\137\x74\145\x78\x74";
    const Custom_greeting_text = "\155\157\137\x73\x61\155\x6c\137\x63\x75\x73\x74\157\x6d\137\x67\162\145\x65\x74\151\x6e\x67\x5f\164\x65\170\x74";
    const Custom_greeting_name = "\x6d\157\137\163\141\155\154\x5f\x67\162\x65\145\164\151\x6e\147\137\156\x61\155\145";
    const Custom_logout_button = "\155\157\x5f\163\141\x6d\154\137\143\165\163\x74\157\x6d\137\154\157\147\157\x75\164\x5f\x74\x65\170\x74";
    const Backdoor_url = "\x6d\157\137\163\x61\x6d\154\x5f\142\141\x63\x6b\144\157\x6f\x72\137\165\x72\x6c";
}
class mo_options_enum_identity_provider extends BasicEnum
{
    const Broker_service = "\155\x6f\x5f\163\141\x6d\x6c\137\145\x6e\x61\x62\x6c\x65\137\143\154\x6f\x75\144\137\142\162\x6f\x6b\x65\162";
    const SP_Base_Url = "\155\157\x5f\163\141\155\x6c\137\x73\160\137\x62\141\x73\x65\137\165\x72\x6c";
    const SP_Entity_ID = "\155\x6f\x5f\163\141\155\x6c\137\x73\x70\137\x65\x6e\164\151\x74\171\x5f\x69\x64";
}
class mo_options_enum_service_provider extends BasicEnum
{
    const Identity_name = "\x73\141\155\154\137\151\x64\145\x6e\164\151\164\171\x5f\x6e\141\x6d\x65";
    const Login_binding_type = "\x73\141\x6d\154\137\154\157\x67\x69\x6e\137\142\151\x6e\144\x69\156\147\137\164\x79\x70\145";
    const Login_URL = "\163\141\155\x6c\x5f\154\x6f\x67\151\x6e\137\165\x72\154";
    const Logout_binding_type = "\x73\x61\155\154\x5f\x6c\x6f\147\157\165\164\x5f\142\x69\156\144\151\156\147\137\x74\171\x70\145";
    const Logout_URL = "\163\x61\x6d\x6c\137\x6c\x6f\147\157\x75\164\x5f\165\x72\154";
    const Issuer = "\163\141\x6d\x6c\x5f\x69\163\163\165\145\x72";
    const X509_certificate = "\x73\x61\x6d\x6c\x5f\x78\65\60\x39\137\143\145\162\164\151\x66\151\x63\141\x74\145";
    const Request_signed = "\163\141\155\x6c\137\x72\145\161\165\145\x73\164\x5f\163\151\x67\156\x65\144";
    const NameID_Format = "\163\141\x6d\x6c\137\x6e\x61\155\x65\x69\144\x5f\x66\x6f\x72\x6d\141\x74";
    const Guide_name = "\x73\x61\155\154\137\151\x64\x65\156\164\151\x74\171\x5f\160\162\157\166\x69\144\x65\x72\137\147\165\x69\144\145\x5f\156\x61\155\145";
    const Is_encoding_enabled = "\x6d\157\137\x73\141\155\x6c\137\145\156\143\x6f\144\151\156\147\x5f\145\x6e\x61\x62\x6c\145\144";
}
class mo_options_test_configuration extends BasicEnum
{
    const SAML_REQUEST = "\115\117\137\x53\101\115\114\x5f\122\105\x51\x55\105\123\124";
    const SAML_RESPONSE = "\115\117\x5f\x53\101\115\114\x5f\122\105\123\120\x4f\116\123\x45";
    const TEST_CONFIG_ERROR_LOG = "\x4d\x4f\137\x53\101\x4d\114\x5f\124\105\x53\x54";
    const TEST_CONFIG_ATTRS = "\155\157\x5f\163\141\x6d\154\x5f\164\145\163\164\137\143\x6f\156\146\x69\x67\x5f\x61\x74\x74\162\x73";
}
class mo_options_enum_attribute_mapping extends BasicEnum
{
    const Attribute_Username = "\163\x61\155\154\137\141\155\x5f\x75\163\145\162\156\x61\155\x65";
    const Attribute_Email = "\x73\141\x6d\154\x5f\x61\x6d\137\145\155\141\x69\x6c";
    const Attribute_First_name = "\163\141\155\x6c\137\141\x6d\x5f\x66\x69\162\x73\164\x5f\156\141\155\x65";
    const Attribute_Last_name = "\x73\141\x6d\154\x5f\x61\x6d\137\x6c\x61\163\x74\137\x6e\x61\155\145";
    const Attribute_Display_name = "\163\141\155\154\137\x61\x6d\137\x64\151\x73\160\x6c\141\x79\x5f\156\141\x6d\145";
    const Attribute_Account_matcher = "\x73\141\x6d\154\137\x61\x6d\137\141\143\143\157\x75\156\x74\x5f\x6d\141\164\143\150\145\162";
}
class mo_options_enum_role_mapping extends BasicEnum
{
    const Role_do_not_update_existing_user = "\x73\141\x6d\x6c\x5f\141\x6d\137\x64\157\x6e\x74\137\165\x70\144\141\164\x65\x5f\145\x78\151\x73\164\151\156\147\x5f\165\163\x65\162\x5f\162\x6f\154\x65";
    const Role_default_role = "\x73\x61\155\x6c\x5f\x61\155\137\144\145\146\141\x75\x6c\164\x5f\165\x73\145\162\x5f\162\x6f\x6c\x65";
}
class mo_options_enum_nameid_formats extends BasicEnum
{
    const EMAIL = "\165\162\x6e\72\x6f\141\x73\151\x73\x3a\156\x61\x6d\x65\163\x3a\164\x63\x3a\123\101\x4d\x4c\72\x31\x2e\x31\72\156\141\x6d\x65\x69\144\55\146\x6f\x72\x6d\x61\x74\72\x65\x6d\141\151\154\101\144\x64\x72\x65\163\x73";
    const UNSPECIFIED = "\165\162\x6e\72\157\x61\163\x69\x73\72\x6e\141\155\x65\x73\72\x74\x63\72\x53\101\115\114\x3a\61\56\x31\72\x6e\141\155\145\151\x64\x2d\x66\157\x72\155\x61\x74\72\x75\x6e\x73\160\x65\143\151\x66\x69\x65\x64";
    const TRANSIENT = "\165\x72\156\x3a\x6f\x61\x73\x69\x73\x3a\x6e\x61\155\145\163\72\164\x63\x3a\x53\101\x4d\114\x3a\62\x2e\60\x3a\x6e\x61\x6d\x65\151\x64\x2d\146\157\162\155\141\164\72\164\x72\141\x6e\x73\151\145\156\164";
    const PERSISTENT = "\x75\x72\156\x3a\157\x61\163\151\x73\x3a\x6e\141\x6d\145\163\x3a\164\x63\72\123\x41\x4d\x4c\72\x32\56\60\72\x6e\141\155\145\151\144\x2d\146\x6f\162\x6d\x61\164\72\160\x65\x72\163\x69\163\x74\x65\x6e\x74";
}
class mo_options_error_constants extends BasicEnum
{
    const Error_no_certificate = "\x55\x6e\141\142\154\x65\x20\x74\157\x20\146\151\x6e\x64\x20\141\x20\143\x65\162\164\x69\146\151\143\x61\164\x65\x20\x2e";
    const Cause_no_certificate = "\x4e\157\40\x73\x69\x67\156\x61\x74\x75\x72\145\40\x66\x6f\x75\x6e\144\40\x69\x6e\x20\123\x41\115\114\40\x52\145\x73\160\157\x6e\x73\x65\40\157\162\40\101\x73\163\145\162\x74\151\157\x6e\x2e\x20\x50\154\145\141\163\x65\x20\163\151\147\x6e\x20\141\164\x20\x6c\x65\141\x73\164\x20\157\156\145\x20\157\146\40\x74\x68\145\x6d\x2e";
    const Error_wrong_certificate = "\125\156\x61\142\154\145\40\164\157\40\x66\151\x6e\x64\x20\x61\40\x63\145\x72\164\x69\x66\x69\143\141\x74\x65\40\155\x61\x74\x63\150\x69\x6e\147\x20\x74\x68\145\x20\143\x6f\156\x66\151\147\165\x72\145\144\x20\146\x69\x6e\147\145\x72\x70\162\151\156\x74\x2e";
    const Cause_wrong_certificate = "\x58\56\65\x30\71\x20\103\145\x72\x74\x69\x66\151\143\x61\164\145\40\146\x69\145\154\x64\40\151\x6e\x20\160\x6c\165\147\x69\156\40\144\x6f\145\163\40\x6e\157\x74\x20\x6d\141\164\143\x68\x20\x74\150\x65\x20\143\x65\162\164\x69\146\x69\143\x61\164\145\x20\146\157\165\156\x64\x20\x69\x6e\x20\x53\x41\x4d\114\40\x52\x65\163\x70\x6f\156\x73\145\56";
    const Error_invalid_audience = "\111\156\166\x61\x6c\151\144\40\x41\x75\x64\x69\145\x6e\143\145\40\125\122\111\56";
    const Cause_invalid_audience = "\x54\150\x65\40\166\x61\154\165\145\x20\157\x66\40\x27\101\165\144\x69\145\156\143\145\x20\125\x52\111\x27\40\x66\151\x65\x6c\144\40\157\156\x20\x49\x64\145\x6e\x74\151\x74\x79\x20\120\162\x6f\166\151\x64\145\x72\47\x73\x20\x73\151\144\145\x20\151\163\x20\x69\x6e\x63\157\x72\x72\145\143\164";
    const Error_issuer_not_verfied = "\111\x73\163\x75\145\x72\40\x63\x61\x6e\x6e\157\164\40\142\145\x20\x76\145\162\x69\146\x69\145\144\x2e";
    const Cause_issuer_not_verfied = "\x49\144\x50\40\x45\x6e\x74\151\164\x79\40\x49\104\x20\143\157\x6e\x66\151\x67\165\x72\x65\x64\40\x61\x6e\144\x20\164\150\x65\40\x6f\x6e\x65\40\x66\x6f\x75\x6e\x64\x20\151\x6e\x20\x53\x41\x4d\114\40\122\x65\163\x70\x6f\156\x73\145\40\x64\157\x20\156\x6f\x74\40\155\141\164\143\150";
}
class mo_options_plugin_constants extends BasicEnum
{
    const CMS_Name = "\x57\x50";
    const Application_Name = "\127\120\x20\155\x69\156\151\x4f\x72\x61\x6e\147\x65\40\x53\101\115\x4c\x20\62\x2e\60\x20\123\x53\117\40\x50\x6c\165\147\x69\156";
    const Application_type = "\x53\101\x4d\x4c";
    const Version = "\x31\66\x2e\x30\x2e\x31";
    const HOSTNAME = "\150\x74\164\x70\163\72\x2f\x2f\x6c\x6f\147\x69\x6e\x2e\x78\x65\x63\x75\x72\151\x66\x79\56\x63\157\155";
    const LICENSE_TYPE = "\127\120\x5f\x53\x41\x4d\x4c\137\x53\120\137\123\x54\101\116\x44\101\122\x44\137\120\114\x55\107\x49\x4e";
    const LICENSE_PLAN_NAME = "\167\x70\x5f\163\x61\x6d\x6c\137\x73\163\x6f\137\x73\x74\x61\156\144\141\x72\144\137\x70\154\x61\x6e";
}
class mo_options_plugin_idp extends BasicEnum
{
    public static $IDP_GUIDES = array("\x41\x44\x46\123" => "\141\144\146\x73", "\117\x6b\164\141" => "\157\153\164\141", "\x53\x61\154\x65\163\x46\157\162\x63\145" => "\x73\141\x6c\145\x73\146\157\162\143\x65", "\x47\157\x6f\x67\x6c\x65\40\x41\x70\x70\163" => "\147\x6f\157\147\x6c\145\x2d\141\160\160\x73", "\101\172\x75\162\145\40\101\104" => "\x61\x7a\165\162\x65\x2d\x61\x64", "\x4f\156\145\x4c\x6f\x67\x69\156" => "\157\x6e\145\x6c\157\x67\151\x6e", "\113\145\x79\143\x6c\x6f\x61\x6b" => "\152\142\157\163\x73\55\x6b\x65\x79\143\x6c\x6f\x61\153", "\115\151\x6e\151\117\x72\x61\x6e\147\x65" => "\155\x69\x6e\151\x6f\162\x61\x6e\x67\145", "\x50\x69\156\147\106\x65\144\145\x72\141\x74\x65" => "\160\151\x6e\147\146\x65\144\x65\162\141\164\x65", "\x50\151\156\147\117\x6e\145" => "\x70\151\x6e\147\x6f\x6e\x65", "\103\x65\x6e\164\162\151\x66\171" => "\x63\145\156\164\162\x69\146\171", "\117\x72\141\143\154\x65" => "\157\162\141\x63\x6c\x65\x2d\145\156\x74\145\162\x70\162\x69\163\x65\55\x6d\141\x6e\x61\147\145\162", "\x42\x69\x74\151\x75\155" => "\142\151\164\x69\165\x6d", "\x53\150\x69\142\142\157\x6c\x65\x74\x68\40\x32" => "\163\150\x69\142\x62\x6f\154\x65\x74\x68\62", "\x53\x68\x69\142\x62\157\154\145\x74\150\40\x33" => "\x73\150\x69\142\x62\157\154\x65\164\x68\x33", "\x53\151\x6d\160\x6c\x65\123\101\115\x4c\160\150\160" => "\163\x69\x6d\x70\154\x65\163\x61\x6d\154", "\x4f\160\145\156\101\115" => "\157\x70\x65\x6e\x61\x6d", "\x41\x75\164\x68\x61\x6e\166\151\x6c" => "\141\165\x74\x68\141\156\x76\151\154", "\101\x75\x74\x68\x30" => "\141\x75\164\150\60", "\103\x41\40\111\x64\145\156\164\151\164\x79" => "\143\x61\x2d\151\144\145\x6e\x74\151\x74\x79", "\x57\123\x4f\x32" => "\167\163\x6f\62", "\122\x53\x41\40\x53\x65\143\x75\162\145\x49\104" => "\162\163\141\55\x73\x65\x63\165\x72\x65\x69\x64", "\117\x74\x68\145\x72" => "\117\x74\150\x65\162");
}
