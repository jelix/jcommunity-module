<?php

class reset_passCtrl extends jControllerCmdLine {

    public $help = array();

    protected $allowed_options = array(
        'change' => array(
            '--force' => false,
        ),
    );

    protected $allowed_parameters = array(
        'change' => array(
            'login' => true,
            'password' => false
        )
    );

    public function __construct($request) {
        parent::__construct($request);
        $this->help = array(
            'change' => jLocale::get('password.change.cmdline.help').PHP_EOL,
        );
    }

    public function change()
    {
        $rep = $this->getResponse();

        $force = $this->option('--force');
        $login = $this->param('login');
        $password = $this->param('password');

        $userInfos = jAuth::getUser($login);

        if (!$userInfos) {
            $rep->addContent(jLocale::get('password.change.cmdline.error.unknown').PHP_EOL);
            $rep->setExitCode(1);

            return $rep;
        }

        if (preg_match('/!!.*!!/', $userInfos->password)) {
            $rep->addContent(jLocale::get('password.change.cmdline.error.module').PHP_EOL);
            $rep->setExitCode(1);

            return $rep;
        }

        if (!$force && !empty($userInfos->password)) {
            $rep->addContent(jLocale::get('password.change.cmdline.error.defined').PHP_EOL);
            $rep->setExitCode(1);

            return $rep;
        }

        if (!$password) {
            $password = jAuth::getRandomPassword();
        }

        if (jAuth::changePassword($login, $password)) {
            $rep->addContent(jLocale::get('password.change.cmdline.success').PHP_EOL);
            $rep->addContent($login.': '.$password.PHP_EOL);
            $rep->setExitCode(0);

            return $rep;
        }

        $rep->addContent(jLocale::get('password.change.cmdline.error.change').PHP_EOL);
        $rep->setExitCode(1);

        return $rep;
    }
}
