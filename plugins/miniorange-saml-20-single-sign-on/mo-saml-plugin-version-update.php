<?php


require_once dirname(__FILE__) . "\57\x69\x6e\143\154\165\x64\145\163\57\x6c\151\x62\57\155\x6f\55\157\160\164\x69\x6f\156\163\x2d\145\x6e\x75\155\x2e\160\150\160";
add_action("\141\144\155\151\x6e\x5f\151\x6e\151\164", "\x6d\x6f\x5f\x73\141\x6d\x6c\x5f\x75\160\x64\x61\164\x65");
class mo_saml_update_framework
{
    private $current_version;
    private $update_path;
    private $plugin_slug;
    private $slug;
    private $plugin_file;
    private $new_version_changelog;
    public function __construct($pC, $qa = "\57", $xA = "\x2f")
    {
        $this->current_version = $pC;
        $this->update_path = $qa;
        $this->plugin_slug = $xA;
        list($BB, $Z6) = explode("\57", $xA);
        $this->slug = $BB;
        $this->plugin_file = $Z6;
        add_filter("\160\x72\x65\x5f\x73\x65\x74\x5f\163\x69\164\x65\137\164\x72\x61\156\163\x69\145\x6e\164\x5f\165\x70\144\x61\164\145\137\160\x6c\165\x67\x69\156\x73", array(&$this, "\155\x6f\137\x73\141\155\x6c\x5f\143\150\x65\x63\x6b\x5f\165\160\144\141\164\x65"));
        add_filter("\160\154\x75\x67\x69\x6e\163\137\141\x70\x69", array(&$this, "\x6d\x6f\137\x73\141\155\154\137\x63\150\145\x63\153\137\151\x6e\x66\157"), 10, 3);
    }
    public function mo_saml_check_update($Hz)
    {
        if (!empty($Hz->checked)) {
            goto kR;
        }
        return $Hz;
        kR:
        $Gi = $this->getRemote();
        if ($Gi["\x73\164\141\164\x75\163"] == "\123\x55\x43\103\105\x53\x53") {
            goto DD;
        }
        if (!($Gi["\x73\164\x61\164\165\x73"] == "\x44\105\x4e\111\105\x44")) {
            goto ov;
        }
        if (!version_compare($this->current_version, $Gi["\x6e\145\x77\126\145\162\163\151\x6f\x6e"], "\74")) {
            goto F5;
        }
        $Lm = new stdClass();
        $Lm->slug = $this->slug;
        $Lm->new_version = $Gi["\156\145\167\126\x65\162\163\151\x6f\x6e"];
        $Lm->url = "\x68\164\164\160\x73\x3a\x2f\x2f\x6d\x69\x6e\151\157\162\141\156\147\x65\56\143\157\x6d";
        $Lm->plugin = $this->plugin_slug;
        $Lm->tested = $Gi["\143\155\163\x43\157\x6d\x70\141\164\x69\142\151\154\x69\164\x79\x56\145\x72\x73\x69\x6f\156"];
        $Lm->icons = array("\x31\x78" => $Gi["\151\143\x6f\156"]);
        $Lm->status_code = $Gi["\163\164\x61\x74\165\x73"];
        $Lm->license_information = $Gi["\154\151\x63\x65\x6e\x73\145\111\156\x66\157\x72\155\x61\x74\151\157\x6e"];
        update_option("\x6d\157\x5f\163\141\155\154\x5f\154\x69\x63\145\156\163\x65\137\145\x78\160\x69\162\x79\x5f\144\141\x74\145", $Gi["\154\151\x63\145\156\145\105\170\160\x69\x72\x79\104\x61\x74\145"]);
        $Hz->response[$this->plugin_slug] = $Lm;
        $Ik = true;
        update_option("\155\x6f\137\x73\141\x6d\x6c\x5f\x73\154\145", $Ik);
        set_transient("\165\160\x64\x61\x74\x65\x5f\x70\154\165\147\151\156\163", $Hz);
        return $Hz;
        F5:
        ov:
        goto Wg;
        DD:
        $Ik = false;
        update_option("\155\157\x5f\163\141\155\154\137\163\154\x65", $Ik);
        if (!version_compare($this->current_version, $Gi["\x6e\145\x77\126\145\x72\163\x69\157\x6e"], "\x3c")) {
            goto Ia;
        }
        ini_set("\x6d\x61\170\137\145\x78\145\143\165\164\151\157\156\137\164\x69\x6d\145", 600);
        ini_set("\155\x65\155\157\x72\171\x5f\154\x69\x6d\151\x74", "\x31\60\x32\x34\115");
        $this->mo_saml_create_backup_dir();
        $Lj = $this->getAuthToken();
        $Qb = round(microtime(true) * 1000);
        $Qb = number_format($Qb, 0, '', '');
        $Lm = new stdClass();
        $Lm->slug = $this->slug;
        $Lm->new_version = $Gi["\x6e\145\167\126\x65\x72\163\151\157\156"];
        $Lm->url = "\150\x74\x74\160\163\x3a\57\57\x6d\151\x6e\151\157\162\x61\x6e\x67\145\56\x63\157\x6d";
        $Lm->plugin = $this->plugin_slug;
        $Lm->package = mo_options_plugin_constants::HOSTNAME . "\x2f\x6d\x6f\141\x73\x2f\x70\x6c\x75\147\151\156\57\x64\x6f\167\x6e\154\157\141\144\55\165\x70\x64\141\164\145\77\160\154\165\147\x69\x6e\123\154\x75\147\x3d" . $this->plugin_slug . "\x26\154\151\x63\x65\156\x73\x65\120\154\141\156\116\x61\x6d\145\x3d" . mo_options_plugin_constants::LICENSE_PLAN_NAME . "\x26\143\165\x73\x74\157\x6d\x65\162\111\144\x3d" . get_option("\155\x6f\x5f\163\x61\155\x6c\x5f\x61\144\155\x69\x6e\x5f\143\165\163\164\x6f\x6d\145\162\x5f\x6b\145\x79") . "\46\154\151\143\x65\156\x73\x65\x54\171\x70\x65\x3d" . mo_options_plugin_constants::LICENSE_TYPE . "\46\x61\x75\x74\x68\x54\157\153\x65\x6e\75" . $Lj . "\46\157\x74\x70\124\157\153\x65\156\75" . $Qb;
        $Lm->tested = $Gi["\x63\x6d\x73\x43\x6f\x6d\160\x61\x74\x69\142\151\154\x69\164\171\126\x65\x72\163\x69\x6f\x6e"];
        $Lm->icons = array("\61\x78" => $Gi["\x69\143\157\x6e"]);
        $Lm->new_version_changelog = $Gi["\x63\150\x61\156\147\x65\x6c\157\x67"];
        $Lm->status_code = $Gi["\163\164\141\x74\x75\163"];
        update_option("\155\157\x5f\x73\x61\155\154\137\154\151\x63\145\156\163\x65\x5f\145\x78\x70\151\x72\171\x5f\144\x61\164\145", $Gi["\154\151\143\145\x6e\x65\x45\170\160\151\x72\x79\104\141\x74\x65"]);
        $Hz->response[$this->plugin_slug] = $Lm;
        set_transient("\165\x70\144\141\164\145\x5f\x70\x6c\165\147\x69\156\163", $Hz);
        return $Hz;
        Ia:
        Wg:
        return $Hz;
    }
    public function mo_saml_check_info($Lm, $WB, $WG)
    {
        if (!(($WB == "\x71\x75\145\162\x79\x5f\x70\x6c\x75\x67\x69\156\163" || $WB == "\160\x6c\x75\147\x69\156\x5f\x69\156\x66\157\162\x6d\141\164\x69\157\156") && isset($WG->slug) && ($WG->slug === $this->slug || $WG->slug === $this->plugin_file))) {
            goto wx;
        }
        $jL = $this->getRemote();
        remove_filter("\x70\154\x75\147\151\x6e\163\137\x61\160\x69", array($this, "\x6d\157\137\163\141\155\154\x5f\143\150\x65\143\153\137\151\x6e\x66\157"));
        $WP = plugins_api("\160\x6c\165\x67\x69\156\x5f\151\x6e\146\157\x72\x6d\141\x74\x69\x6f\156", array("\x73\x6c\x75\x67" => $this->slug, "\x66\x69\x65\154\144\163" => array("\x61\143\x74\151\x76\x65\x5f\151\x6e\x73\164\x61\154\154\x73" => true, "\x6e\x75\x6d\x5f\x72\x61\164\151\156\x67\163" => true, "\162\x61\164\151\156\147" => true, "\x72\x61\164\151\156\147\x73" => true, "\x72\x65\166\151\x65\x77\x73" => true)));
        $BI = false;
        $Bp = false;
        $vi = false;
        $xQ = false;
        $M3 = '';
        $CI = '';
        if (is_wp_error($WP)) {
            goto em;
        }
        $BI = $WP->active_installs;
        $Bp = $WP->rating;
        $vi = $WP->ratings;
        $xQ = $WP->num_ratings;
        $M3 = $WP->sections["\144\x65\x73\143\x72\151\x70\x74\151\157\x6e"];
        $CI = $WP->sections["\162\145\x76\x69\145\x77\163"];
        em:
        add_filter("\160\x6c\165\147\151\156\163\x5f\x61\160\151", array($this, "\x6d\x6f\137\163\x61\x6d\154\x5f\x63\150\x65\x63\x6b\137\x69\x6e\146\157"), 10, 3);
        if ($jL["\163\x74\x61\x74\165\163"] == "\x53\x55\103\x43\105\x53\x53") {
            goto IO;
        }
        if (!($jL["\163\x74\141\164\x75\x73"] == "\x44\x45\116\111\105\104")) {
            goto XK;
        }
        if (!version_compare($this->current_version, $jL["\156\x65\x77\126\x65\162\163\x69\157\156"], "\x3c")) {
            goto sM;
        }
        $TR = new stdClass();
        $TR->slug = $this->slug;
        $TR->plugin = $this->plugin_slug;
        $TR->name = $jL["\x70\154\x75\147\x69\x6e\x4e\x61\155\x65"];
        $TR->version = $jL["\156\145\x77\126\145\x72\163\x69\157\x6e"];
        $TR->new_version = $jL["\156\145\x77\x56\145\162\163\151\x6f\x6e"];
        $TR->tested = $jL["\x63\155\163\x43\x6f\155\x70\141\x74\x69\142\x69\154\x69\x74\x79\x56\145\x72\163\151\157\x6e"];
        $TR->requires = $jL["\x63\155\163\x4d\151\x6e\126\145\x72\x73\x69\157\x6e"];
        $TR->requires_php = $jL["\x70\x68\160\x4d\151\156\x56\145\162\x73\x69\157\156"];
        $TR->compatibility = array($jL["\x63\x6d\163\x43\x6f\x6d\x70\141\164\151\x62\x69\x6c\x69\164\171\x56\x65\x72\x73\x69\x6f\x6e"]);
        $TR->url = $jL["\x63\x6d\x73\x50\154\165\147\151\156\x55\162\154"];
        $TR->author = $jL["\x70\x6c\x75\147\151\156\x41\x75\164\150\157\162"];
        $TR->author_profile = $jL["\160\x6c\165\x67\151\156\101\165\164\x68\157\162\x50\x72\157\x66\151\x6c\x65"];
        $TR->last_updated = $jL["\154\x61\x73\x74\x55\x70\x64\x61\x74\x65\x64"];
        $TR->banners = array("\x6c\x6f\167" => $jL["\142\x61\x6e\x6e\x65\x72"]);
        $TR->icons = array("\61\170" => $jL["\151\x63\157\156"]);
        $TR->sections = array("\143\150\x61\156\x67\145\154\157\147" => $jL["\x63\x68\141\156\147\x65\154\157\147"], "\x6c\151\x63\145\156\163\145\137\151\156\146\157\x72\155\141\x74\x69\x6f\x6e" => _x($jL["\x6c\x69\x63\145\156\163\145\x49\156\146\x6f\x72\155\x61\x74\151\x6f\156"], "\120\x6c\x75\x67\151\x6e\x20\x69\x6e\x73\164\x61\x6c\x6c\x65\162\40\163\145\x63\164\151\157\156\x20\164\x69\x74\154\x65"), "\x64\x65\x73\143\162\151\x70\x74\151\157\156" => $M3, "\122\145\x76\x69\x65\x77\x73" => $CI);
        $TR->external = '';
        $TR->homepage = $jL["\150\157\x6d\x65\160\x61\x67\145"];
        $TR->reviews = true;
        $TR->active_installs = $BI;
        $TR->rating = $Bp;
        $TR->ratings = $vi;
        $TR->num_ratings = $xQ;
        update_option("\155\157\137\163\x61\x6d\x6c\x5f\154\x69\x63\x65\x6e\163\145\x5f\145\x78\x70\151\162\x79\x5f\x64\141\x74\145", $jL["\154\x69\143\x65\x6e\145\105\170\160\151\162\x79\104\141\x74\145"]);
        return $TR;
        sM:
        XK:
        goto fz;
        IO:
        $Ik = false;
        update_option("\155\x6f\137\163\141\x6d\154\137\163\x6c\145", $Ik);
        if (!version_compare($this->current_version, $jL["\x6e\x65\167\x56\x65\162\x73\x69\157\156"], "\x3c\x3d")) {
            goto Sn;
        }
        $TR = new stdClass();
        $TR->slug = $this->slug;
        $TR->name = $jL["\x70\154\165\147\151\156\116\141\155\145"];
        $TR->plugin = $this->plugin_slug;
        $TR->version = $jL["\x6e\x65\167\126\145\162\x73\x69\x6f\156"];
        $TR->new_version = $jL["\156\x65\x77\126\145\162\163\151\x6f\x6e"];
        $TR->tested = $jL["\143\155\x73\103\x6f\x6d\x70\141\164\151\x62\x69\154\x69\164\x79\126\x65\162\x73\151\x6f\x6e"];
        $TR->requires = $jL["\143\x6d\163\115\151\156\126\145\x72\163\151\157\156"];
        $TR->requires_php = $jL["\x70\x68\160\115\151\156\x56\145\x72\x73\x69\x6f\x6e"];
        $TR->compatibility = array($jL["\143\x6d\x73\x43\x6f\x6d\160\x61\x74\x69\142\x69\154\x69\164\x79\x56\x65\162\163\151\x6f\x6e"]);
        $TR->url = $jL["\x63\x6d\x73\120\x6c\165\x67\x69\x6e\x55\162\154"];
        $TR->author = $jL["\160\154\x75\x67\151\156\x41\165\164\x68\x6f\x72"];
        $TR->author_profile = $jL["\160\154\x75\147\151\156\101\x75\x74\x68\x6f\162\120\162\x6f\x66\x69\x6c\145"];
        $TR->last_updated = $jL["\x6c\141\163\164\125\160\x64\x61\164\x65\x64"];
        $TR->banners = array("\x6c\157\167" => $jL["\142\x61\x6e\156\x65\162"]);
        $TR->icons = array("\61\x78" => $jL["\x69\x63\x6f\x6e"]);
        $TR->sections = array("\143\150\x61\156\147\x65\x6c\157\x67" => $jL["\x63\150\141\x6e\147\145\x6c\157\x67"], "\x6c\151\x63\145\x6e\x73\145\x5f\151\x6e\146\157\x72\x6d\141\x74\151\x6f\156" => _x($jL["\154\x69\143\x65\156\163\145\111\x6e\146\x6f\162\x6d\x61\x74\x69\157\x6e"], "\x50\154\165\x67\x69\x6e\x20\151\x6e\163\x74\x61\x6c\154\145\162\x20\x73\x65\x63\164\151\157\156\x20\x74\x69\164\154\145"), "\144\145\x73\x63\x72\x69\x70\x74\x69\x6f\156" => $M3, "\x52\x65\x76\x69\x65\x77\x73" => $CI);
        $Lj = $this->getAuthToken();
        $Qb = round(microtime(true) * 1000);
        $Qb = number_format($Qb, 0, '', '');
        $TR->download_link = mo_options_plugin_constants::HOSTNAME . "\57\x6d\157\x61\x73\57\x70\154\165\x67\x69\x6e\x2f\144\157\x77\x6e\x6c\x6f\141\144\x2d\x75\x70\x64\x61\164\145\x3f\160\154\x75\147\151\x6e\123\x6c\165\147\x3d" . $this->plugin_slug . "\x26\154\151\x63\145\x6e\163\x65\x50\154\141\156\x4e\x61\x6d\145\x3d" . mo_options_plugin_constants::LICENSE_PLAN_NAME . "\46\143\x75\163\164\157\x6d\x65\162\x49\144\75" . get_option("\x6d\157\137\x73\x61\155\154\x5f\x61\x64\x6d\151\x6e\x5f\x63\x75\163\x74\157\155\x65\162\137\153\x65\x79") . "\x26\x6c\151\x63\x65\x6e\163\145\x54\171\x70\x65\75" . mo_options_plugin_constants::LICENSE_TYPE . "\x26\x61\165\164\150\124\x6f\153\145\156\75" . $Lj . "\x26\157\164\x70\x54\x6f\153\x65\x6e\75" . $Qb;
        $TR->package = $TR->download_link;
        $TR->external = '';
        $TR->homepage = $jL["\150\x6f\155\145\x70\x61\147\145"];
        $TR->reviews = true;
        $TR->active_installs = $BI;
        $TR->rating = $Bp;
        $TR->ratings = $vi;
        $TR->num_ratings = $xQ;
        update_option("\x6d\157\137\x73\141\x6d\154\137\x6c\151\x63\x65\x6e\x73\145\137\145\170\160\151\x72\x79\x5f\x64\141\x74\145", $jL["\x6c\x69\143\x65\156\145\105\170\x70\x69\x72\x79\104\141\x74\145"]);
        return $TR;
        Sn:
        fz:
        wx:
        return $Lm;
    }
    private function getRemote()
    {
        $zs = get_option("\155\157\137\163\x61\155\154\x5f\141\144\x6d\x69\x6e\x5f\143\x75\x73\164\157\155\145\162\137\153\145\171");
        $Ht = get_option("\155\157\x5f\x73\141\155\154\x5f\x61\144\155\x69\156\x5f\x61\x70\151\137\153\x65\x79");
        $Qb = round(microtime(true) * 1000);
        $do = $zs . number_format($Qb, 0, '', '') . $Ht;
        $Lj = hash("\163\150\141\65\61\x32", $do);
        $Qb = number_format($Qb, 0, '', '');
        $HS = array("\160\x6c\x75\x67\x69\x6e\123\x6c\165\147" => $this->plugin_slug, "\x6c\x69\x63\x65\x6e\163\x65\120\x6c\141\156\x4e\x61\x6d\x65" => mo_options_plugin_constants::LICENSE_PLAN_NAME, "\143\x75\163\x74\157\x6d\145\162\111\144" => $zs, "\154\x69\x63\x65\156\x73\145\x54\x79\x70\x65" => mo_options_plugin_constants::LICENSE_TYPE);
        $po = array("\x68\x65\x61\x64\x65\x72\x73" => array("\x43\x6f\156\164\145\x6e\164\55\124\171\160\145" => "\141\160\160\x6c\151\x63\141\164\151\x6f\156\57\x6a\163\157\156\73\x20\143\150\141\x72\163\x65\x74\x3d\165\x74\146\x2d\x38", "\103\x75\163\x74\x6f\x6d\145\162\x2d\x4b\145\x79" => $zs, "\x54\x69\155\145\x73\x74\141\155\x70" => $Qb, "\101\165\164\150\x6f\x72\151\x7a\141\164\151\157\156" => $Lj), "\x62\157\x64\x79" => json_encode($HS), "\x6d\145\164\150\157\x64" => "\120\117\x53\124", "\144\x61\164\141\x5f\146\157\x72\155\x61\x74" => "\142\157\144\171", "\163\163\154\x76\145\162\151\x66\x79" => false);
        $CS = wp_remote_post($this->update_path, $po);
        if (!(!is_wp_error($CS) || wp_remote_retrieve_response_code($CS) === 200)) {
            goto k1;
        }
        $lO = json_decode($CS["\x62\157\144\x79"], true);
        return $lO;
        k1:
        return false;
    }
    private function getAuthToken()
    {
        $zs = get_option("\155\157\x5f\163\x61\x6d\x6c\137\141\144\155\x69\x6e\137\143\165\x73\164\157\155\145\x72\x5f\153\145\x79");
        $Ht = get_option("\155\157\137\163\141\x6d\154\137\x61\x64\155\151\156\137\141\x70\x69\x5f\x6b\145\x79");
        $Qb = round(microtime(true) * 1000);
        $do = $zs . number_format($Qb, 0, '', '') . $Ht;
        $Lj = hash("\x73\150\x61\65\x31\x32", $do);
        return $Lj;
    }
    function mo_saml_plugin_update_message($Q3, $CS)
    {
        if (array_key_exists("\x73\164\141\x74\165\x73\x5f\143\157\144\145", $Q3)) {
            goto My;
        }
        return;
        My:
        if ($Q3["\163\164\x61\x74\165\x73\137\x63\157\x64\145"] == "\123\125\x43\x43\x45\123\123") {
            goto TT;
        }
        if (!($Q3["\163\x74\x61\x74\165\163\137\143\x6f\x64\145"] == "\104\x45\x4e\111\105\104")) {
            goto Gc;
        }
        echo sprintf(__($Q3["\154\151\x63\145\156\x73\x65\137\x69\156\146\x6f\x72\x6d\x61\x74\x69\x6f\156"]));
        Gc:
        goto Jg;
        TT:
        $bA = wp_upload_dir();
        $Id = $bA["\142\141\163\145\144\x69\x72"];
        $bA = rtrim($Id, "\x2f");
        $R4 = $bA . DIRECTORY_SEPARATOR . "\x62\141\143\x6b\165\160";
        $vy = "\155\151\156\151\x6f\162\141\x6e\x67\145\55\163\x61\x6d\x6c\x2d\62\x30\55\163\x69\156\x67\154\145\x2d\163\151\147\156\x2d\157\x6e\x2d\163\x74\141\156\144\x61\x72\x64\x2d\142\x61\143\x6b\165\160\x2d" . $this->current_version;
        $od = explode("\x3c\57\165\x6c\76", $Q3["\156\x65\167\x5f\x76\145\162\x73\x69\157\x6e\x5f\x63\150\141\156\147\x65\154\157\147"]);
        $o2 = $od[0];
        $bZ = $o2 . "\74\x2f\x75\154\76";
        echo "\x3c\x64\x69\x76\x3e";
        if (is_writable($bA)) {
            goto HM;
        }
        echo "\74\x62\162\x2f\x3e\x3c\163\x70\141\x6e\x20\x73\164\171\x6c\x65\75\42\x63\x6f\154\157\x72\x3a\162\x65\144\x22\76\x3c\142\76\x4e\117\x54\x45\x3a\40\111\x74\x20\163\145\145\x6d\163\40\164\x68\145\40\x75\x70\x6c\157\141\x64\163\x20\x64\151\162\145\143\164\157\162\171\x20\x69\163\40\x6e\x6f\164\40\167\162\151\164\141\x62\x6c\145\x2e\40\x42\x61\x63\x6b\165\x70\40\157\x66\40\164\x68\145\x20\x63\165\162\162\x65\x6e\164\x20\160\x6c\x75\x67\x69\x6e\x20\166\145\162\x73\151\157\156\x20\143\157\x75\154\x64\x6e\47\164\x20\x62\145\x20\x63\x72\145\141\164\x65\144\x2e\x3c\142\x72\x2f\76\111\x74\40\x69\163\x20\162\x65\x63\157\155\155\145\x6e\144\145\144\40\164\x6f\40\160\162\157\166\x69\x64\145\x20\x77\162\151\164\145\40\160\x65\x72\155\x69\x73\x73\151\157\156\40\x74\157\40\164\150\145\40\125\160\154\x6f\x61\144\x73\x20\x64\151\162\145\143\x74\157\162\x79\x20\50\40" . $bA . "\x20\x29\x20\142\145\146\157\162\x65\40\143\150\145\143\153\x69\156\x67\40\x66\x6f\162\x20\165\x70\144\x61\164\145\x2e\74\57\142\x3e\x3c\57\163\160\x61\156\x3e";
        goto C8;
        HM:
        echo "\74\x62\x3e" . __("\74\x62\x72\x20\57\76\x41\x6e\x20\x61\165\164\x6f\x6d\x61\164\151\143\x20\142\141\x63\x6b\165\160\x20\x6f\146\40\x63\165\x72\162\145\x6e\164\x20\x76\145\162\163\x69\157\156\40" . $this->current_version . "\x20\150\141\163\40\x62\x65\x65\156\40\143\x72\x65\141\164\145\x64\x20\x61\x74\40\164\150\145\40\154\157\x63\x61\164\151\157\156\40" . $R4 . "\40\167\x69\164\x68\x20\164\x68\x65\x20\156\x61\155\x65\x20\74\x73\x70\141\156\x20\x73\x74\171\154\x65\75\x22\x63\157\154\157\x72\x3a\x23\x30\60\x37\63\141\141\73\42\x3e" . $vy . "\x3c\x2f\163\160\141\156\76\56\x20\x49\156\x20\x63\x61\163\145\x2c\40\163\157\155\145\164\150\151\156\x67\x20\x62\162\x65\x61\153\163\40\x64\165\162\x69\x6e\147\40\x74\x68\x65\x20\165\x70\x64\x61\164\x65\x2c\40\171\157\x75\40\x63\141\156\40\162\x65\166\145\x72\x74\x20\x74\157\40\171\x6f\165\162\40\143\165\x72\162\145\156\x74\x20\166\145\162\x73\x69\x6f\x6e\x20\x62\x79\40\x72\x65\160\154\x61\143\151\156\147\x20\164\x68\x65\40\142\141\x63\x6b\x75\160\40\165\163\x69\x6e\147\x20\x46\x54\x50\x20\x61\x63\143\145\x73\163\x2e", "\x6d\x69\156\151\157\x72\x61\156\147\x65\x2d\x73\141\x6d\154\x2d\62\60\55\x73\151\156\147\154\x65\x2d\x73\151\147\156\x2d\x6f\x6e") . "\74\57\x62\76";
        C8:
        echo "\x3c\x2f\x64\x69\166\x3e\x3c\144\151\x76\x20\163\164\x79\x6c\145\75\x22\x63\157\154\157\x72\x3a\40\x23\x66\60\60\x3b\42\76" . __("\x3c\x62\162\x20\57\76\124\141\153\x65\40\141\40\x6d\151\156\165\164\x65\40\x74\x6f\x20\143\150\x65\x63\x6b\x20\x74\150\x65\x20\143\150\x61\156\x67\145\x6c\157\x67\40\x6f\x66\x20\x6c\141\x74\x65\x73\164\40\166\x65\162\163\x69\157\x6e\40\x6f\x66\x20\164\150\x65\40\160\x6c\165\x67\151\156\56\40\x48\145\162\x65\x27\x73\x20\167\x68\171\40\x79\157\x75\x20\156\145\145\x64\x20\164\157\x20\165\160\144\141\164\145\72", "\155\x69\x6e\151\157\162\141\156\147\145\x2d\x73\x61\155\154\x2d\x32\x30\55\163\151\156\x67\154\x65\x2d\x73\151\147\x6e\x2d\157\156") . "\74\x2f\144\x69\166\x3e";
        echo "\x3c\x64\151\166\40\163\x74\171\x6c\145\x3d\x22\146\157\x6e\164\x2d\167\x65\151\147\150\x74\72\40\x6e\157\162\155\x61\154\x3b\x22\76" . $bZ . "\74\x2f\x64\151\166\x3e\74\x62\76\116\x6f\164\x65\x3a\x3c\x2f\142\x3e\x20\x50\154\145\141\x73\x65\40\143\x6c\151\x63\153\x20\x6f\156\40\74\142\x3e\126\151\145\x77\x20\126\145\162\x73\x69\x6f\x6e\x20\x64\x65\164\x61\x69\154\x73\74\x2f\x62\x3e\40\154\151\156\153\x20\164\157\x20\x67\x65\x74\x20\143\157\155\160\x6c\145\164\x65\40\143\x68\x61\156\147\x65\154\157\x67\x20\x61\x6e\x64\40\154\x69\143\145\x6e\163\145\x20\x69\x6e\146\157\162\155\141\x74\151\157\x6e\56\x20\103\154\151\143\x6b\40\157\x6e\40\x3c\x62\76\125\x70\144\141\x74\145\40\x4e\x6f\167\74\x2f\x62\76\x20\x6c\x69\156\x6b\40\164\157\x20\165\x70\144\141\164\x65\x20\x74\150\145\40\x70\154\x75\x67\x69\156\x20\164\157\40\x6c\141\x74\x65\x73\164\x20\x76\145\162\x73\151\157\x6e\56";
        Jg:
    }
    public function mo_saml_license_key_notice()
    {
        if (!array_key_exists("\x6d\157\163\141\155\154\55\144\151\x73\x6d\x69\x73\163", $_GET)) {
            goto PE;
        }
        return;
        PE:
        if (!(get_option("\x6d\x6f\137\x73\x61\155\x6c\x5f\163\154\x65") && new DateTime() > get_option("\155\157\x2d\x73\x61\155\x6c\55\x70\154\165\147\151\156\55\x74\x69\155\145\162"))) {
            goto Ff;
        }
        $px = esc_url(add_query_arg(array("\x6d\x6f\163\141\155\x6c\x2d\144\x69\x73\x6d\x69\x73\163" => wp_create_nonce("\163\x61\155\154\x2d\144\151\x73\155\151\x73\x73"))));
        echo "\74\163\143\162\x69\160\x74\x3e\xd\12\x9\11\11\x9\x66\165\156\143\164\151\x6f\156\40\155\157\123\101\115\114\120\141\171\x6d\x65\x6e\x74\x53\164\x65\x70\x73\x28\51\x20\x7b\15\xa\x9\x9\x9\11\x9\x76\x61\162\40\141\x74\x74\x72\40\75\x20\144\157\143\165\x6d\145\156\x74\x2e\147\x65\x74\x45\x6c\x65\155\145\156\164\x42\171\x49\144\x28\42\x6d\157\163\x61\x6d\154\x70\x61\171\x6d\145\x6e\164\x73\164\x65\160\x73\x22\51\x2e\163\x74\x79\x6c\145\56\x64\x69\x73\160\x6c\x61\171\73\15\12\x9\x9\x9\x9\x9\x69\146\x28\141\164\x74\162\x20\x3d\75\x20\x22\156\x6f\156\145\x22\x29\173\xd\xa\11\x9\11\11\11\x9\144\x6f\x63\x75\155\x65\x6e\x74\56\147\x65\164\105\x6c\145\155\x65\156\164\x42\x79\111\x64\x28\42\x6d\157\163\141\155\154\x70\141\x79\x6d\x65\156\164\x73\164\145\x70\x73\42\x29\x2e\163\164\171\154\145\x2e\x64\151\x73\160\154\x61\171\40\75\40\x22\142\x6c\x6f\143\153\42\73\15\xa\11\11\x9\11\11\x7d\x65\x6c\x73\145\x7b\15\xa\11\11\x9\x9\x9\x9\144\157\x63\165\155\x65\156\x74\56\147\x65\x74\105\154\x65\155\x65\156\164\x42\x79\111\x64\50\x22\x6d\x6f\x73\141\155\x6c\x70\x61\171\x6d\x65\156\164\x73\x74\x65\x70\163\42\51\56\163\x74\171\x6c\x65\x2e\144\151\x73\160\154\x61\x79\x20\x3d\40\x22\156\157\156\145\x22\73\xd\12\x9\x9\11\x9\x9\x7d\15\xa\x9\11\11\x9\175\15\12\x9\x9\x9\x9\xd\12\x9\x9\x9\11\15\12\11\11\11\x3c\57\x73\x63\162\151\160\x74\x3e";
        echo "\xd\xa\x3c\144\x69\166\x20\151\x64\75\x22\x6d\x65\x73\x73\x61\147\x65\x22\x20\x73\164\x79\154\x65\x3d\x22\x70\x6f\x73\151\x74\x69\157\156\x3a\162\145\154\141\164\x69\166\145\42\40\143\154\x61\x73\x73\x3d\x22\156\157\x74\x69\x63\145\x20\x6e\157\164\151\143\x65\40\x6e\x6f\x74\151\x63\145\x2d\x77\x61\162\156\151\x6e\147\x22\x3e\74\142\162\40\57\x3e\x3c\x73\x70\x61\156\40\143\x6c\141\x73\x73\75\42\141\x6c\x69\147\156\x6c\145\x66\164\x22\x20\163\164\171\154\145\75\42\143\157\x6c\x6f\x72\72\x23\141\60\x30\x3b\146\x6f\x6e\x74\55\146\141\155\x69\x6c\171\72\40\x2d\x77\145\x62\x6b\x69\x74\x2d\160\151\143\x74\157\x67\162\141\x70\150\x3b\x66\157\156\x74\x2d\163\151\x7a\145\72\40\62\65\x70\x78\x3b\x22\76\x49\x4d\120\x4f\122\124\x41\116\124\41\x3c\x2f\x73\160\x61\x6e\76\74\x62\162\40\x2f\x3e\74\x69\x6d\147\x20\163\162\x63\x3d\42" . plugin_dir_url(__FILE__) . "\x69\x6d\141\147\x65\163\57\155\151\x6e\x69\x6f\x72\141\x6e\x67\145\x2d\154\x6f\147\157\56\160\156\147" . "\42\x20\x63\154\141\x73\163\75\x22\x61\x6c\151\147\x6e\154\x65\146\164\x22\40\150\145\151\x67\x68\164\x3d\x22\x38\x37\42\40\167\x69\144\x74\150\75\x22\x36\66\x22\40\141\x6c\x74\x3d\x22\x6d\x69\156\x69\117\x72\x61\156\147\145\40\154\157\x67\x6f\x22\x20\163\x74\171\x6c\x65\x3d\42\x6d\141\x72\147\151\x6e\72\x31\x30\160\x78\x20\61\x30\x70\170\x20\61\60\x70\x78\x20\60\73\x20\x68\145\151\147\150\x74\72\x31\x32\70\x70\x78\x3b\x20\167\x69\x64\x74\x68\72\x20\x31\x32\x38\160\x78\73\42\x3e\74\150\x33\x3e\155\151\156\151\x4f\x72\x61\x6e\147\145\x20\x53\101\115\x4c\x20\62\x2e\60\x20\x53\x69\156\x67\x6c\x65\x20\123\x69\x67\156\x2d\x4f\x6e\40\x53\x75\x70\160\157\162\164\40\x26\40\x4d\x61\x69\156\x74\145\156\x61\156\x63\x65\x20\x4c\151\143\x65\156\163\145\40\x45\x78\160\x69\x72\x65\144\x3c\57\150\x33\x3e\74\160\76\x59\x6f\x75\x72\x20\x6d\x69\156\151\x4f\x72\x61\156\147\x65\x20\x53\101\115\114\x20\62\x2e\x30\x20\123\x69\x6e\147\x6c\145\x20\x53\151\147\x6e\55\x4f\156\x20\x6c\x69\x63\x65\156\163\145\x20\x69\x73\40\145\x78\160\x69\162\x65\144\56\40\x54\x68\x69\163\40\x6d\x65\x61\156\x73\x20\171\157\165\342\200\x99\162\x65\40\155\x69\163\x73\151\x6e\x67\x20\157\165\x74\40\157\156\x20\x6c\x61\164\x65\163\164\x20\x73\145\x63\x75\162\151\x74\x79\40\160\141\164\143\x68\145\163\54\x20\x63\x6f\155\160\x61\x74\x69\x62\151\154\x69\164\x79\x20\167\x69\x74\150\x20\164\x68\145\x20\154\141\x74\145\x73\164\x20\120\x48\x50\x20\166\145\x72\163\x69\x6f\156\163\x20\x61\x6e\144\x20\x57\157\x72\x64\x70\162\x65\x73\163\56\40\115\157\x73\x74\40\x69\155\x70\157\162\164\x61\156\164\154\x79\40\x79\157\165\xe2\200\x99\x6c\154\40\142\145\x20\155\x69\x73\x73\x69\x6e\147\x20\157\x75\x74\x20\157\156\x20\157\x75\162\x20\141\167\x65\x73\x6f\155\145\x20\x73\x75\x70\x70\x6f\x72\x74\x21\x20\x3c\57\160\x3e\15\12\x9\x9\74\160\76\74\x61\x20\x68\x72\x65\146\75\42" . mo_options_plugin_constants::HOSTNAME . "\57\155\x6f\141\163\x2f\x6c\157\x67\151\156\77\x72\x65\144\151\x72\145\x63\164\125\x72\x6c\75" . mo_options_plugin_constants::HOSTNAME . "\57\x6d\157\x61\x73\57\x61\x64\x6d\151\156\x2f\x63\165\163\164\157\155\145\162\57\154\151\143\x65\156\163\145\x72\x65\156\145\x77\141\x6c\x73\77\x72\145\156\x65\167\141\x6c\x72\145\161\165\145\x73\x74\75" . mo_options_plugin_constants::LICENSE_TYPE . "\x22\40\143\x6c\141\x73\x73\75\x22\142\x75\164\x74\x6f\x6e\40\x62\x75\x74\x74\157\x6e\55\160\162\151\155\141\x72\171\x22\x20\x74\x61\x72\x67\145\x74\x3d\x22\x5f\142\154\141\x6e\153\42\76\x52\x65\x6e\145\x77\x20\x79\157\165\x72\x20\x73\x75\160\160\157\162\x74\x20\154\151\x63\x65\156\x73\x65\74\57\141\x3e\x26\156\142\x73\x70\x3b\x26\x6e\x62\163\160\x3b\74\142\x3e\x3c\x61\x20\150\x72\145\146\x3d\42\43\x22\40\157\156\x63\154\151\143\153\75\42\x6d\157\x53\x41\x4d\114\x50\141\x79\x6d\145\156\164\123\x74\145\160\163\50\51\x22\76\x43\154\151\143\x6b\x20\x68\x65\162\x65\74\x2f\x61\x3e\40\164\x6f\40\153\x6e\157\x77\40\x68\157\x77\40\x74\x6f\40\x72\145\x6e\x65\x77\77\74\57\142\x3e\74\x64\151\x76\x20\x69\x64\75\42\x6d\157\163\x61\155\154\160\x61\171\155\145\x6e\164\163\164\x65\x70\x73\x22\x20\40\x73\x74\x79\x6c\x65\75\x22\144\151\163\160\154\141\x79\x3a\x20\156\157\156\x65\x3b\x22\76\74\x62\162\x20\x2f\x3e\74\x75\x6c\x20\163\164\x79\x6c\x65\x3d\x22\154\x69\163\164\x2d\163\x74\x79\x6c\145\72\x20\x64\151\163\x63\x3b\155\141\x72\147\151\x6e\55\x6c\145\146\164\x3a\x20\61\x35\160\170\73\x22\76\15\12\74\154\x69\76\103\154\151\143\153\40\x6f\156\40\141\142\157\166\145\40\x62\x75\x74\x74\157\x6e\x20\164\157\x20\x6c\157\x67\151\156\40\x69\156\x74\157\x20\x6d\151\x6e\x69\117\162\x61\x6e\x67\145\56\74\x2f\x6c\x69\76\15\12\x3c\x6c\151\x3e\131\157\165\x20\x77\x69\x6c\154\x20\142\x65\40\162\x65\x64\151\162\x65\x63\x74\x65\144\x20\164\x6f\40\160\154\165\x67\151\x6e\40\162\145\x6e\145\167\141\x6c\40\x70\141\x67\x65\x20\141\x66\x74\145\162\40\x6c\157\147\x69\x6e\56\x3c\x2f\x6c\x69\x3e\xd\12\74\154\x69\76\x49\146\40\x74\x68\145\x20\x70\x6c\165\147\151\x6e\40\x6c\151\143\x65\x6e\x73\145\40\x70\154\141\156\x20\x69\x73\40\156\157\164\40\163\x65\x6c\x65\x63\164\x65\144\x20\x74\x68\145\x6e\x20\143\150\x6f\157\x73\145\40\164\150\145\x20\x72\151\x67\150\164\x20\157\156\x65\40\x66\x72\x6f\x6d\40\164\150\x65\x20\x64\x72\x6f\160\144\x6f\x77\x6e\x2c\x20\x6f\x74\150\x65\162\167\x69\163\145\x20\143\x6f\x6e\x74\x61\143\x74\x20\x3c\x62\x3e\74\141\x20\150\x72\x65\146\x3d\42\x6d\141\151\154\x74\157\x3a\x69\x6e\146\x6f\x40\170\145\143\x75\x72\151\146\171\x2e\143\x6f\155\x22\76\151\x6e\146\157\100\x78\145\143\x75\x72\151\x66\x79\x2e\x63\x6f\x6d\74\x2f\x61\76\74\57\142\76\40\x74\157\x20\x6b\156\x6f\x77\x20\141\x62\x6f\165\164\40\x79\x6f\x75\162\40\154\151\143\145\156\163\145\x20\x70\154\141\156\56\x3c\x2f\x6c\x69\76\15\12\74\x6c\x69\x3e\x59\x6f\165\40\x77\151\154\154\40\163\x65\145\x20\164\150\x65\x20\x70\154\x75\147\151\x6e\x20\162\145\156\x65\x77\x61\x6c\40\141\155\x6f\x75\x6e\164\56\74\x2f\x6c\x69\x3e\15\12\74\x6c\151\76\106\151\154\x6c\x20\165\160\x20\x79\x6f\x75\x72\x20\x43\x72\145\144\x69\164\40\x43\x61\x72\144\x20\x69\x6e\146\x6f\162\155\141\164\x69\157\x6e\40\164\x6f\x20\155\141\153\145\x20\164\x68\145\40\x70\141\171\155\x65\156\164\56\74\57\154\151\x3e\15\xa\x3c\x6c\x69\76\117\156\x63\x65\x20\164\150\145\x20\x70\x61\171\x6d\x65\156\164\40\x69\163\x20\x64\x6f\156\145\x2c\40\x63\154\151\x63\153\x20\x6f\156\40\x3c\142\76\103\x68\x65\143\153\x20\101\x67\141\x69\x6e\74\x2f\142\x3e\40\142\x75\164\164\x6f\156\40\146\162\x6f\x6d\x20\164\150\145\40\106\157\162\143\x65\40\x55\160\x64\x61\x74\x65\40\x61\x72\145\x61\x20\x6f\x66\x20\171\x6f\x75\162\40\x57\x6f\162\x64\120\x72\145\163\163\40\x61\x64\x6d\151\x6e\40\144\x61\163\150\142\157\141\x72\x64\x20\157\162\x20\x77\x61\151\x74\40\146\x6f\162\x20\x61\x20\144\x61\x79\x20\x74\x6f\40\147\145\164\x20\164\x68\x65\x20\x61\165\164\x6f\155\141\164\151\x63\40\165\160\x64\141\x74\x65\x2e\74\x2f\154\151\76\15\xa\74\x6c\151\x3e\103\x6c\151\143\153\40\157\156\40\x3c\142\76\125\x70\x64\141\x74\145\40\116\157\x77\x3c\57\x62\76\x20\154\x69\156\153\40\x74\157\40\151\156\x73\164\141\154\x6c\x20\x74\150\145\x20\154\141\164\145\163\164\x20\166\x65\x72\163\151\157\156\40\x6f\146\40\164\150\145\x20\160\154\x75\x67\x69\x6e\x20\x66\x72\157\155\x20\x70\x6c\165\x67\x69\x6e\40\x6d\141\156\141\x67\145\162\x20\141\x72\145\141\x20\x6f\x66\40\x79\x6f\x75\162\40\141\144\x6d\151\x6e\40\x64\x61\x73\150\142\x6f\x61\162\144\56\x3c\57\154\x69\76\15\xa\x3c\57\x75\154\x3e\111\x6e\40\x63\141\163\145\x2c\x20\171\x6f\x75\x20\141\x72\145\40\x66\141\143\151\x6e\147\x20\x61\156\171\40\144\x69\146\x66\151\x63\165\154\x74\171\x20\x69\156\40\151\x6e\x73\164\x61\154\x6c\x69\x6e\x67\x20\164\150\x65\x20\165\160\x64\x61\x74\x65\54\40\x70\x6c\x65\x61\163\145\40\143\157\156\x74\141\x63\164\x20\74\142\x3e\x3c\141\x20\x68\x72\x65\146\75\x22\x6d\141\x69\154\164\157\72\151\x6e\146\x6f\100\170\x65\143\165\162\x69\146\171\56\x63\x6f\155\x2e\x63\157\155\42\76\151\156\146\x6f\100\170\x65\143\165\x72\151\146\x79\x2e\x63\x6f\x6d\x2e\143\x6f\x6d\x3c\57\141\x3e\x3c\x2f\142\x3e\x2e\xd\12\117\165\x72\x20\x53\165\160\x70\157\162\x74\x20\x45\x78\x65\143\x75\164\x69\166\145\40\x77\x69\154\154\40\141\163\x73\x69\x73\x74\40\171\157\x75\x20\x69\156\x20\151\156\x73\164\x61\154\154\x69\x6e\x67\40\x74\150\x65\x20\165\160\144\141\x74\145\163\x2e\74\x62\162\40\57\x3e\x3c\151\x3e\106\x6f\x72\40\x6d\x6f\162\145\40\x69\x6e\x66\x6f\x72\x6d\141\164\151\157\x6e\54\40\x70\154\145\141\x73\145\x20\143\x6f\156\x74\x61\143\164\40\74\142\x3e\x3c\x61\x20\150\x72\x65\x66\75\x22\x6d\141\151\154\x74\x6f\72\151\x6e\146\157\x40\170\x65\x63\165\x72\x69\146\x79\x2e\x63\157\x6d\x2e\x63\x6f\x6d\x22\76\x69\x6e\x66\157\x40\x78\x65\143\x75\162\151\x66\171\56\143\157\155\56\x63\157\155\74\x2f\141\76\74\x2f\x62\76\56\x3c\x2f\x69\x3e\x3c\x2f\144\151\x76\76\x3c\x61\40\150\x72\x65\146\x3d\42" . $px . "\42\x20\x63\154\141\x73\163\75\x22\141\154\x69\x67\156\x72\x69\147\x68\x74\40\142\x75\x74\x74\x6f\x6e\x20\x62\165\x74\164\157\x6e\x2d\154\x69\x6e\x6b\42\x3e\104\151\163\155\x69\x73\163\74\x2f\141\76\x3c\57\x70\76\15\12\11\11\x3c\144\151\x76\40\143\154\x61\x73\163\x3d\42\x63\154\145\141\162\x22\x3e\x3c\x2f\144\151\166\76\74\57\x64\151\x76\x3e";
        Ff:
    }
    public function mo_saml_dismiss_notice()
    {
        if (!empty($_GET["\155\157\x73\x61\x6d\x6c\x2d\144\x69\x73\x6d\151\163\x73"])) {
            goto tP;
        }
        return;
        tP:
        if (wp_verify_nonce($_GET["\155\157\x73\141\x6d\x6c\x2d\144\x69\x73\155\x69\163\x73"], "\x73\141\155\154\x2d\x64\x69\x73\x6d\151\163\x73")) {
            goto lc;
        }
        return;
        lc:
        if (!(isset($_GET["\x6d\x6f\163\141\x6d\154\55\144\151\163\x6d\x69\x73\x73"]) && wp_verify_nonce($_GET["\155\x6f\x73\141\x6d\x6c\x2d\144\x69\x73\x6d\151\x73\163"], "\x73\141\x6d\x6c\x2d\144\x69\163\155\x69\163\x73"))) {
            goto wu;
        }
        $sO = new DateTime();
        $sO->modify("\x2b\x31\x20\x64\x61\x79");
        update_option("\155\157\55\163\141\x6d\x6c\x2d\x70\x6c\165\147\x69\x6e\55\164\151\155\x65\162", $sO);
        wu:
    }
    function mo_saml_create_backup_dir()
    {
        $R4 = plugin_dir_path(__FILE__);
        $R4 = rtrim($R4, "\57");
        $R4 = rtrim($R4, "\134");
        $Q3 = get_plugin_data(__FILE__);
        $Og = $Q3["\124\145\x78\164\104\x6f\x6d\x61\x69\x6e"];
        $bA = wp_upload_dir();
        $Id = $bA["\142\x61\x73\145\144\151\x72"];
        $bA = rtrim($Id, "\57");
        if (is_writable($bA)) {
            goto rg;
        }
        return;
        rg:
        $J0 = $bA . DIRECTORY_SEPARATOR . "\x62\141\143\153\165\x70" . DIRECTORY_SEPARATOR . $Og . "\x2d\163\164\141\x6e\144\141\162\144\x2d\142\x61\x63\x6b\x75\160\x2d" . $this->current_version;
        if (file_exists($J0)) {
            goto Kx;
        }
        mkdir($J0, 511, true);
        Kx:
        $Yg = $R4;
        $yV = $J0;
        $this->mo_saml_copy_files_to_backup_dir($Yg, $yV);
    }
    function mo_saml_copy_files_to_backup_dir($R4, $J0)
    {
        if (!is_dir($R4)) {
            goto PC;
        }
        $Pt = scandir($R4);
        PC:
        if (!empty($Pt)) {
            goto UT;
        }
        return;
        UT:
        foreach ($Pt as $Gs) {
            if (!($Gs == "\56" || $Gs == "\56\56")) {
                goto p3;
            }
            goto VT;
            p3:
            $n7 = $R4 . DIRECTORY_SEPARATOR . $Gs;
            $Le = $J0 . DIRECTORY_SEPARATOR . $Gs;
            if (is_dir($n7)) {
                goto WP;
            }
            copy($n7, $Le);
            goto Pv;
            WP:
            if (file_exists($Le)) {
                goto w_;
            }
            mkdir($Le, 511, true);
            w_:
            $this->mo_saml_copy_files_to_backup_dir($n7, $Le);
            Pv:
            VT:
        }
        V2:
    }
}
function mo_saml_update()
{
    $gs = mo_options_plugin_constants::HOSTNAME;
    $Ms = mo_options_plugin_constants::Version;
    $dW = $gs . "\x2f\x6d\x6f\x61\x73\57\141\160\x69\57\160\154\x75\x67\151\x6e\x2f\x6d\x65\164\141\x64\x61\x74\141";
    $xA = plugin_basename(dirname(__FILE__) . "\x2f\x6c\x6f\147\x69\156\x2e\x70\150\x70");
    $va = new mo_saml_update_framework($Ms, $dW, $xA);
    add_action("\151\x6e\x5f\x70\154\x75\147\151\156\137\165\x70\x64\141\164\145\137\x6d\x65\163\163\x61\x67\x65\x2d{$xA}", array($va, "\155\x6f\x5f\163\x61\x6d\154\137\160\154\x75\147\x69\x6e\137\165\160\x64\141\164\145\x5f\x6d\x65\163\x73\141\147\145"), 10, 2);
    add_action("\141\144\x6d\x69\156\137\150\x65\x61\144", array($va, "\x6d\x6f\x5f\163\x61\155\x6c\137\x6c\151\143\x65\x6e\163\x65\137\153\145\x79\x5f\x6e\157\164\x69\143\x65"));
    add_action("\141\x64\155\151\x6e\x5f\x6e\157\x74\x69\143\145\x73", array($va, "\155\157\137\x73\x61\155\x6c\137\144\x69\163\x6d\151\x73\x73\137\x6e\x6f\164\x69\143\145"), 50);
    if (!get_option("\155\x6f\x5f\163\x61\155\154\x5f\163\154\x65")) {
        goto Xk;
    }
    update_option("\x6d\157\137\163\141\x6d\154\137\x73\x6c\x65\137\155\145\163\x73\141\147\145", "\131\x6f\165\x72\40\x53\x41\115\114\x20\x70\154\165\x67\151\156\x20\x6c\x69\143\x65\x6e\163\145\x20\150\141\163\x65\40\x62\x65\145\156\40\x65\x78\x70\151\162\x65\x64\56\40\x59\x6f\x75\40\141\162\145\x20\x6d\x69\x73\163\x69\156\147\40\157\165\164\x20\157\x6e\x20\165\x70\144\141\x74\145\x73\x20\x61\x6e\x64\x20\x73\165\160\x70\157\162\x74\41\40\x50\x6c\145\x61\163\145\x20\x3c\141\40\x68\x72\145\x66\x3d\42" . mo_options_plugin_constants::HOSTNAME . "\57\x6d\157\x61\x73\x2f\x6c\x6f\x67\x69\x6e\77\162\x65\144\x69\x72\145\x63\164\125\162\x6c\x3d" . mo_options_plugin_constants::HOSTNAME . "\x2f\x6d\x6f\x61\163\x2f\x61\144\155\151\x6e\x2f\143\165\x73\164\157\155\145\x72\x2f\154\x69\143\145\156\x73\145\162\x65\x6e\x65\167\141\x6c\163\x3f\162\145\156\x65\167\141\154\162\x65\x71\165\x65\x73\164\x3d" . mo_options_plugin_constants::LICENSE_TYPE . "\40\x22\x20\164\x61\162\147\x65\164\x3d\x22\137\x62\154\141\156\x6b\42\76\x3c\x62\76\x43\154\151\x63\x6b\40\x48\145\162\145\x3c\57\x62\76\74\57\141\x3e\40\x74\x6f\x20\162\145\x6e\145\167\40\x74\150\145\40\123\x75\x70\160\157\162\x74\40\x61\156\144\x20\x4d\x61\151\x6e\164\145\156\x61\x63\x65\x20\x70\x6c\141\x6e\x2e");
    Xk:
}
