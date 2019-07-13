<?php
/**
* @package  test
* @subpackage
* @author
* @contributor
* @copyright
* @link
* @licence  http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
*/

$appPath = __DIR__.'/';
require ($appPath.'vendor/autoload.php');

jApp::initPaths(
    $appPath
    //$appPath.'www/',
    //$appPath.'var/',
    //$appPath.'var/log/',
    //$appPath.'var/config/'
);
jApp::setTempBasePath(realpath($appPath.'temp/').'/');

require ($appPath.'vendor/jelix_app_path.php');

jApp::declareModulesDir(array(
                        __DIR__.'/modules/',
                        __DIR__.'/../modules/'
                    ));
