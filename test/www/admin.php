<?php
/**
* @package   test
* @subpackage 
* @author    test
* @copyright 2005-2015 Laurent Jouanneau and other contributors
* @link      
* @license    All rights reserved
*/

require ('../application.init.php');
require (JELIX_LIB_CORE_PATH.'request/jClassicRequest.class.php');

checkAppOpened();

jApp::loadConfig('admin/config.ini.php');

jApp::setCoord(new jCoordinator());
jApp::coord()->process(new jClassicRequest());



