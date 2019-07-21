<?php
/**
* @package   test
* @subpackage 
* @author    test
* @copyright 2005-2015 Laurent Jouanneau and other contributors
* @link      
* @license    All rights reserved
*/

require_once (JELIX_LIB_CORE_PATH.'response/jResponseHtml.class.php');

class adminHtmlResponse extends jResponseHtml {

    public $bodyTpl = 'master_admin~main';

    function __construct() {
        parent::__construct();

        // Include your common CSS and JS files here
        $this->addAssets('master_admin');
    }

    protected function doAfterActions() {
        // Include all process in common for all actions, like the settings of the
        // main template, the settings of the response etc..
        $this->title .= ($this->title !=''?' - ':'').' Administration';
        $this->body->assignIfNone('selectedMenuItem','');
        $this->body->assignZone('MENU','master_admin~admin_menu', array('selectedMenuItem'=>$this->body->get('selectedMenuItem')));
        $this->body->assignZone('INFOBOX','master_admin~admin_infobox');
        $this->body->assignIfNone('MAIN','');
        $this->body->assignIfNone('adminTitle','');
        $this->body->assign('user', jAuth::getUserSession());
    }

}
