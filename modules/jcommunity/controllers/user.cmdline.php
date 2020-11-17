<?php

class userCtrl extends jControllerCmdLine {

    public $help = array();

    protected $allowed_options = array(
        'changePassword' => array(
            '--force' => false,
            '-v' => false,
        ),
        'create' => array(
            '--reset' => false,
            '--admin' => false,
            '-v' => false,
        )
    );

    protected $allowed_parameters = array(
        'changePassword' => array(
            'login' => true,
            'password' => false
        ),
        'resetPassword' => array(
            'login' => true,
        ),
        'create' => array(
            'login' => true,
            'email' => true,
            'password' => false
        ),
    );

    public function __construct($request) {
        parent::__construct($request);
        $this->help = array(
            'changePassword' => jLocale::get('password.change.cmdline.help').PHP_EOL,
            'resetPassword' => jLocale::get('password.reset.cmdline.help').PHP_EOL,
            'create' => jLocale::get('register.cmdline.create.help').PHP_EOL
        );
    }

    protected function exit($rep, $code, $message = null, $verbose = true)
    {
        if ($verbose && $message) {
            $rep->addContent($message);
        }
        $rep->setExitCode($code);

        return $rep;
    }

    public function changePassword()
    {
        $rep = $this->getResponse();

        $force = $this->option('--force');
        $login = $this->param('login');
        $password = $this->param('password');
        $verbose = $this->option('-v');

        $userInfos = jAuth::getUser($login);
        $code = 0;

        if (!$userInfos) {
            $message = jLocale::get('password.change.cmdline.error.unknown').PHP_EOL;
            $code = 1;
        }

        if (!$code && preg_match('/!!.*!!/', $userInfos->password)) {
            $message = jLocale::get('password.change.cmdline.error.module').PHP_EOL;
            $code = 1;
        }

        if (!$code && !$force && !empty($userInfos->password)) {  
            $message = jLocale::get('password.change.cmdline.error.defined').PHP_EOL;
            $code = 1;
        }

        if (!$password) {
            $password = jAuth::getRandomPassword();
        }

        if (!$code && jAuth::changePassword($login, $password)) {
            $rep->addContent($login.': '.$password.PHP_EOL);
            $message = jLocale::get('password.change.cmdline.success').PHP_EOL;
            $code = 0;
        } else {
            $message = jLocale::get('password.change.cmdline.error.change').PHP_EOL;
            $code = 1;
        }

        return $this->exit($rep, $message, $code, $verbose);
    }

    public function resetPassword()
    {
        $rep = $this->getResponse();
        $login = $this->param('login');
        $userInfos = jAuth::getUser($login);

        $code = 0;
        $message = null;

        if (!$userInfos) {
            $message = jLocale::get('password.change.cmdline.error.unknown').PHP_EOL;
            $code = 1;
        }

        if (!$code && !$userInfos->email) {
            $message = jLocale::get('password.reset.cmdline.mail.undefined').PHP_EOL;
            $code = 1;
        }

        if (!$code && ($userInfos->status == \Jelix\JCommunity\Account::STATUS_VALID
            || $userInfos->status == \Jelix\JCommunity\Account::STATUS_PWD_CHANGED)) {
            $passReset = new \Jelix\JCommunity\PasswordReset(true, true);
            $result = $passReset->sendEmail($login, $userInfos->email);
        }
        else {
            $result = \Jelix\JCommunity\PasswordReset::RESET_BAD_STATUS;
        }

        if (!$code && $result != \Jelix\JCommunity\PasswordReset::RESET_OK) {
            $message = jLocale::get('password.reset.cmdline.error');
            $code = 1;
        }

        return $this->exit($rep, $code, $message);
    }

    public function create()
    {
        $rep = $this->getResponse();

        $login = $this->param('login');
        $email = $this->param('email');
        $password = $this->param('password');
        $reset = $this->option('--reset');
        $admin = $this->option('--admin');
        $verbose = $this->option('-v');
        $code = 0;
        $message = '';

        $user = jAuth::getUser($login);

        if ($user) {
            $message = jLocale::get('register.form.login.exists').PHP_EOL;
            $code = 1;
        }

        if (!$code && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = jLocale::get('register.email.bad.format').PHP_EOL;
            $code = 1;
        }

        if ($code) {
            return $this->exit($rep, $code, $message, $verbose);
        }

        if (!$password) {
            if ($reset) {
                $password = '';
            } else {
                $password = jAuth::getRandomPassword();
                $rep->addContent($login.': '.$password.PHP_EOL);
            }
        }

        $user = jAuth::createUserObject($login, $password);
        $user->email = $email;
        $user->status = \Jelix\JCommunity\Account::STATUS_VALID;
        jAuth::saveNewUser($user);
        if ($admin) {
            jAcl2DbUserGroup::addUserToGroup($login, 'admins');
        }
        $message = jLocale::get('register.registration.cmdline.ok').PHP_EOL;

        if ($reset) {
            $passReset = new \Jelix\JCommunity\PasswordReset(true, true);
            $result = $passReset->sendEmail($login, $user->email);
            if ($result != \Jelix\JCommunity\PasswordReset::RESET_OK) {
                $message = $message.jLocale::get('password.reset.cmdline.error').PHP_EOL;
                $code = 1;
            }
        }

        return $this->exit($rep, $code, $message, $verbose);
    }
}
