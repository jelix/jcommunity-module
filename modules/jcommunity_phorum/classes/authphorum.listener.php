<?php
/**
* @package      jcommunity
* @subpackage
* @author       Laurent Jouanneau <laurent@jelix.org>
* @contributor
* @copyright    2009 Laurent Jouanneau
* @link         http://jelix.org
* @licence      http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
*/



class authphorumListener extends jEventListener{

   protected $currentDir = '';
   protected $phorumPath = '';

   protected $enable = false;

   protected function getDaoName() {
      $plugin = jApp::coord()->getPlugin('auth');
      if($plugin === null)
          throw new jException('jelix~auth.error.plugin.missing');
      return $plugin->config['Db']['dao'];
   }

   function __construct() {
      $plugin = jApp::coord()->getPlugin('auth');
      if($plugin === null) {
         $this->enable = false;
         return;
         //throw new jException('jelix~auth.error.plugin.missing');
      }
      if(!isset($plugin->config['phorumPathInWWW']) || trim($plugin->config['phorumPathInWWW']) == '') {
         $this->enable = false;
         return;
      }
      $this->enable = true;
      if (defined('JELIX_APP_WWW_PATH')) // for Jelix 1.2 and older
         $this->phorumPath = JELIX_APP_WWW_PATH.$plugin->config['phorumPathInWWW'];
      else
         $this->phorumPath = jApp::wwwPath($plugin->config['phorumPathInWWW']);
      $this->currentDir = getcwd();

      chdir($this->phorumPath);
      if(!defined('phorum_page'))
         define('phorum_page','login');
      require_once("common.php");
      chdir($this->currentDir);
   }

   function performEvent($event){
      if ($this->enable) {
         chdir($this->phorumPath);
         $methodName = 'on'.$event->getName();
         $this->$methodName($event);
         chdir($this->currentDir);
      }
   }

   function onAuthNewUser($event) {
      $user = $event->getParam('user');
      $phuser = array (
         'user_id'                 => null,
         'username'                => $user->login,
         'real_name'               => $user->nickname,
         'password'                => '',
         'email'                   => $user->email,
         'active'                  => PHORUM_USER_PENDING_EMAIL,
      );
      phorum_api_user_save($phuser);
   }

   /**
   *
   */
   function onAuthCanLogin ($event) {
      // data: $user, $login
      //  $event->Add(array('canlogin'=>($event->getParam('user')->status > 0))); // >0 == VALID or MODIFIED
   }

   function onAuthUpdateUser($event) {
      $user = $event->getParam('user');
      $phuser = phorum_api_user_get($user->id);

      switch($user->status) {
         case -2: //deleted.  this case shouldn't be call here
            $phuser['real_name'] = '';
            $phuser['email'] = '';
            $phuser['active'] = PHORUM_USER_INACTIVE;
            break;
         case -1: //deactivated
            $phuser['active'] = PHORUM_USER_INACTIVE;
            break;
         case 1: //valid
            $phuser['real_name'] = $user->nickname;
            $phuser['email'] = $user->email;
            $phuser['active'] = PHORUM_USER_ACTIVE;
            break;
         case 0: //new user, not verified
         case 2: //valid, email changed
         case 3: //valid, password changed
            $phuser['real_name'] = $user->nickname;
            $phuser['email'] = $user->email;
            $phuser['active'] = PHORUM_USER_PENDING_EMAIL;
            break;
      }

      phorum_api_user_save_raw($phuser);
   }

   function onAuthCanRemoveUser($event) {
      $event->add(array('canremove'=>true));
   }

   function onAuthRemoveUser($event) {
      $user = $event->getParam('user');
      phorum_api_user_delete($user->id);
   }

   function onAuthLogin($event) {
      $login = $event->getParam('login');
      $persistence = $event->getParam('persistence');
      $user = jDao::get($this->getDaoName())->getByLogin($login);
      phorum_api_user_set_active_user(PHORUM_FORUM_SESSION, $user->id, ($persistence?0:PHORUM_FLAG_SESSION_ST));

      global $PHORUM;
      $PHORUM['use_cookies'] = PHORUM_REQUIRE_COOKIES;
      phorum_api_user_session_create(PHORUM_FORUM_SESSION);
   }

   function onAuthLogout($event) {
      //data: $login
      phorum_api_user_session_destroy(PHORUM_FORUM_SESSION);
   }

/*
   function onjcommunity_account_show($event) {
      //data: $login, $user, $tpl
   }

   function onjcommunity_init_edit_form_account($event) {
      //data: $user, $form
   }

   function onjcommunity_prepare_edit_account($event) {
      //data: $user, $form
   }

   function onjcommunity_edit_account($event) {
      //data: $user, $rep, $form, $tpl
   }

   function onjcommunity_check_before_save_account($event) {
      //data: $user, $form
   }

   function onjcommunity_save_account($event) {
      //data: $user, $form, $factory, $record, $to_insert
   }

   function onjcommunity_registration_init_form($event) {
      //data: $form
   }

   function onjcommunity_registration_prepare_save($event) {
      //data: $form, $user
   }

   function onjcommunity_registration_after_save($event) {
      //data: $form, $user
   }

   function onjcommunity_registration_confirm($event) {
      //data: $user
   }*/
}
