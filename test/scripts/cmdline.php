<?php

require_once ('../application.init.php');
require_once (JELIX_LIB_CORE_PATH.'jCmdlineCoordinator.class.php');
require_once (JELIX_LIB_CORE_PATH.'request/jCmdLineRequest.class.php');

$config_file = 'cmdline/config.ini.php';

jApp::setCoord(new jCmdlineCoordinator($config_file));
jApp::coord()->process(new jCmdLineRequest());