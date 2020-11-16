<?php

class reset_passCtrl extends jControllerCmdLine {

    public $help = array();

    protected $allowed_options = array(
        'change' => array(
            '--force' => false,
            '-v' => false,
        ),
    );

    protected $allowed_parameters = array(
        'change' => array(
            'login' => true,
            'password' => false
        ),
        'reset' => array(
            'login' => true,
        )
    );

    public function __construct($request) {
        parent::__construct($request);
        $this->help = array(
            'change' => jLocale::get('password.change.cmdline.help').PHP_EOL,
            'reset' => jLocale::get('password.reset.cmdline.help').PHP_EOL,
        );
    }

    public function change()
    {
        $rep = $this->getResponse();

        $force = $this->option('--force');
        $login = $this->param('login');
        $password = $this->param('password');
        $verbose = $this->option('-v');

        $userInfos = jAuth::getUser($login);

        if (!$userInfos) {
            if ($verbose) {
                $rep->addContent(jLocale::get('password.change.cmdline.error.unknown').PHP_EOL);
            }
            $rep->setExitCode(1);

            return $rep;
        }

        if (preg_match('/!!.*!!/', $userInfos->password)) {
            if ($verbose) {
                $rep->addContent(jLocale::get('password.change.cmdline.error.module').PHP_EOL);
            }
            $rep->setExitCode(1);

            return $rep;
        }

        if (!$force && !empty($userInfos->password)) {
            if ($verbose) {
                $rep->addContent(jLocale::get('password.change.cmdline.error.defined').PHP_EOL);
            }
            $rep->setExitCode(1);

            return $rep;
        }

        if (!$password) {
            $password = jAuth::getRandomPassword();
        }

        if (jAuth::changePassword($login, $password)) {
            if ($verbose) {
                $rep->addContent(jLocale::get('password.change.cmdline.success').PHP_EOL);
            }
            $rep->addContent($login.': '.$password.PHP_EOL);
            $rep->setExitCode(0);

            return $rep;
        }

        if ($verbose) {
            $rep->addContent(jLocale::get('password.change.cmdline.error.change').PHP_EOL);
        }
        $rep->setExitCode(1);

        return $rep;
    }

    public function reset()
    {
        $rep = $this->getResponse();
        $login = $this->param('login');
        $userInfos = jAuth::getUser($login);
        
        if (!$userInfos) {
            $rep->addContent(jLocale::get('password.change.cmdline.error.unknown').PHP_EOL);
            $rep->setExitCode(1);

            return $rep;
        }

        if (!$userInfos->email) {
            $rep->addContent(jLocale::get('password.reset.cmdline.mail.undefined').PHP_EOL);
            $rep->setExitCode(1);

            return $rep;
        }

        if ($userInfos->status == \Jelix\JCommunity\Account::STATUS_VALID
            || $userInfos->status == \Jelix\JCommunity\Account::STATUS_PWD_CHANGED) {
            $passReset = new \Jelix\JCommunity\PasswordReset(true, true);
            $result = $passReset->sendEmail($login, $userInfos->email);
        }
        else {
            $result = \Jelix\JCommunity\PasswordReset::RESET_BAD_STATUS;
        }

        if ($result != \Jelix\JCommunity\PasswordReset::RESET_OK) {
            $rep->addContent(jLocale::get('password.reset.cmdline.error'));
            $rep->setExitCode(1);

            return $rep;
        }

        return $rep;
    }
}
