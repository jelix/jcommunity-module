<?php
/**
* @package     jelix-modules
* @subpackage  jauth
* @author      Laurent Jouanneau
* @contributor Antoine Detante
* @copyright   2005-2008 Laurent Jouanneau, 2007 Antoine Detante
* @link        http://www.jelix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


class loginCtrl extends jController {

    public $pluginParams = array(
      '*'=>array('auth.required'=>false)
    );

    function index()
    {
        if (isset(jApp::config()->jcommunity)) {
            $config = jApp::config()->jcommunity;
            $stayOnLoginPage = isset($config['noRedirectionOnAuthenticatedLoginPage'])
                               && $config['noRedirectionOnAuthenticatedLoginPage'];
        }
        else {
            $config = null;
            $stayOnLoginPage = true;
        }


        if (jAuth::isConnected() && !$stayOnLoginPage) {
            // if the user is already connected, try to go to the right page

            $conf = jApp::coord()->getPlugin('auth')->config;

            $url = '';

            if ($conf['after_login'] != '') {
                $url = jUrl::get($conf['after_login']);
            }
            $auth_url_return = $this->param('auth_url_return');
            if ($conf['enable_after_login_override'] && $auth_url_return != '') {
                if (method_exists('jAuth','checkReturnUrl')
                    && jAuth::checkReturnUrl($auth_url_return)
                ) {
                    $url = $auth_url_return;
                }
            }

            if ($url != '') {
                $rep = $this->getResponse('redirectUrl');
                $rep->url = $url;
                return $rep;
            }
        }

        $response = 'html';
        if ($config) {
            $response = (isset($config['loginResponse'])?$config['loginResponse']:'html');
        }

        $rep = $this->getResponse($response);
        $rep->title = jLocale::get('login.login.title');
        $rep->body->assign('page_title', $rep->title);
        $rep->body->assignZone('MAIN','jcommunity~login', array('as_main_content'=>true));
        return $rep;
    }

    /**
    *
    */
    function in() {
        $rep = $this->getResponse('redirectUrl');
        $conf = jApp::coord()->getPlugin('auth')->config;

        if ($conf['after_login'] == '') {
            throw new jException ('jcommunity~login.error.no.after_login');
        }

        if ($conf['after_logout'] == '') {
            throw new jException ('jcommunity~login.error.no.after_logout');
        }

        $form = jForms::fill('jcommunity~login');
        if (!$form) {
            $rep->url = jUrl::get($conf['after_logout']);
            return $rep;
        }

        if (!jAuth::login($form->getData('auth_login'), $form->getData('auth_password'), $form->getData('auth_remember_me'))) {
            $form->setErrorOn('auth_login', jLocale::get('jcommunity~login.error'));
            //jMessage::add(jLocale::get('jcommunity~login.error'), 'error');
            $auth_url_return = $this->param('auth_url_return');
            if (method_exists('jAuth', 'checkReturnUrl') &&  jAuth::checkReturnUrl($auth_url_return)) {
                $rep->url = jUrl::get('login:index', array('auth_url_return'=>$auth_url_return));
            } else {
                $rep->url = jUrl::get('login:index');
            }
        } else {
            jForms::destroy('jcommunity~login');
            if ($conf['enable_after_login_override']) {
                $url_return = $this->param('auth_url_return');
                if (method_exists('jAuth', 'checkReturnUrl') &&  jAuth::checkReturnUrl($url_return)) {
                    $rep->url = $url_return;
                }
                else {
                    $rep->url =  jUrl::get($conf['after_login']);
                }
            }
            else {
                $rep->url =  jUrl::get($conf['after_login']);
            }
        }

        return $rep;
    }

    /**
    *
    */
    function out() {
        $rep = $this->getResponse('redirectUrl');
        jAuth::logout();
        $conf = jApp::coord()->getPlugin ('auth')->config;

        if ($conf['after_logout'] == '') {
            throw new jException ('jcommunity~login.error.no.after_logout');
        }

        if (jApp::coord()->execOriginalAction()) {
            if ($conf['enable_after_logout_override']) {
                $url_return = $this->param('auth_url_return');
                if (method_exists('jAuth', 'checkReturnUrl') &&  jAuth::checkReturnUrl($url_return)) {
                    $rep->url = $url_return;
                }
                else {
                    $rep->url =  jUrl::get($conf['after_logout']);
                }
            }
            else {
                $rep->url =  jUrl::get($conf['after_logout']);
            }
        }
        else {
            // we are here because of an internal redirection (authentication missing)
            // if we can indicate the url to go after the login, let's pass this url
            // to the next action (which is in most of case a login form)
            if ($conf['enable_after_login_override'] && $_SERVER['REQUEST_METHOD'] == 'GET') {
                $rep->url = jUrl::get($conf['after_logout'],
                                      array('auth_url_return'=> jUrl::getCurrentUrl()));
            }
            else {
                $rep->url = jUrl::get($conf['after_logout']);
            }
        }
        return $rep;
    }
}
