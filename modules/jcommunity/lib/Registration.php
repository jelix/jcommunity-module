<?php
/**
 * @author       Laurent Jouanneau <laurent@jelix.org>
 * @copyright    2018 Laurent Jouanneau
 *
 * @link         http://jelix.org
 * @licence      http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
 */

namespace Jelix\JCommunity;

class Registration
{

    protected $replyToEmail = '';

    public function __construct($replyToEmail = '')
    {
        $this->replyToEmail = $replyToEmail;
    }

    /**
     * Create a user object.
     *
     * @param string $login
     * @param string $email
     * @param string $password
     *
     * @return object
     */
    public function createUser($login, $email, $password)
    {
        if (\jAuth::getUser($login)) {
            throw new \LogicException("User $login already exists");
        }
        $key = sha1(password_hash($login.$password.microtime(), PASSWORD_DEFAULT));

        $user = \jAuth::createUserObject($login, $password);
        $user->email = $email;
        $user->status = Account::STATUS_NEW;
        $user->request_date = date('Y-m-d H:i:s');
        $user->keyactivate = 'U:'.$key;

        return $user;
    }

    /**
     * @param \jFormsBase $form
     */
    public function createUserByAdmin($user) {
        $config = new \Jelix\JCommunity\Config();
        if ($config->isResetAdminPasswordEnabledForAdmin()) {
            $key = sha1(password_hash($user->login.$user->password.microtime(), PASSWORD_DEFAULT));
            $user->status = Account::STATUS_NEW;
            $user->request_date = date('Y-m-d H:i:s');
            $user->keyactivate = 'A:'.$key;
            $this->sendRegistrationMail($user,
                'jcommunity~mail.registration.admin.body.html',
                'jcommunity~password_confirm_registration:resetform');
            \jAuth::updateUser($user);
        }
    }


    /**
     * Create the user account and send an email.
     *
     * @param object $user the user created with createUser()
     */
    public function createAccount($user)
    {
        $this->sendRegistrationMail($user,
            'jcommunity~mail.registration.body.html',
            'jcommunity~registration:confirm');
        \jAuth::saveNewUser($user);
    }

    public function getAutorizedPropertiesForImport()
    {
        $fields = array();
        /** @var \jDaoRecordBase $account */
        $account = \jAuth::createUserObject('dummy','');
        $properties = $account->getProperties();
        foreach ($properties as $name => $value) {
            if (! in_array($name, ['id', 'password', 'role', 'keyactivate', 'request_date', 'create_date', 'status'])) {
                $fields[] = $name;
            }
        }
        $fields[] = '_role_'; // specific field for acl
        $fields[] = '_ignore_'; // specific name to indicate to ignore the field
        return $fields;
    }

    /**
     * @param array $csvRow values of a csv row
     * @param array $fieldsOrder list of fields name corresponding to the csv row
     * @param bool $reset if true, the user is created with a random password and an email for registration is sent
     * @return object|string the user object if the user is created, the login if the user already exists
     * @throws \jExceptionSelector
     */
    public function importUser($csvRow, $fieldsOrder, $reset)
    {

        $role = '';
        $login = '';
        $properties = [];

        foreach($fieldsOrder as $k=>$field) {
            if ($field == '_ignore_') {
                continue;
            }
            if ($field == '_role_') {
                $role = $csvRow[$k];
                continue;
            }
            if ($field == 'login') {
                $login = $csvRow[$k];
                continue;
            }
            $properties[$field] = $csvRow[$k];
        }

        if ($login == '') {
            throw new \DomainException(\jLocale::get('jcommunity~register.import.error.login.missing'));
        }
        if (!isset($properties['email']) || $properties['email'] == '') {
            throw new \DomainException(\jLocale::get('jcommunity~register.import.error.email.missing'));
        }

        if (!filter_var($properties['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException(\jLocale::get('jcommunity~register.email.bad.format'));
        }

        $user = \jAuth::getUser($login);
        if ($user) {
             return $login;
        }

        $user = \jAuth::createUserObject($login, \jAuthPassword::getRandomPassword());

        foreach($properties as $k=>$v) {
            $user->$k = $v;
        }

        if ($reset) {
            $key = sha1(password_hash($user->login.$user->password.microtime(), PASSWORD_DEFAULT));
            $user->status = Account::STATUS_NEW;
            $user->request_date = date('Y-m-d H:i:s');
            $user->keyactivate = 'A:'.$key;
            $this->sendRegistrationMail($user,
                'jcommunity~mail.registration.admin.body.html',
                'jcommunity~password_confirm_registration:resetform');
        }
        else {
            $user->status = Account::STATUS_VALID;
        }

        \jAuth::saveNewUser($user);
        if ($role) {
            if (\jAcl2DbUserGroup::getGroup($role)) {
                \jAcl2DbUserGroup::addUserToGroup($login, $role);
            }
        }
        return $user;
    }


    public function resendRegistrationMail($user, $byAdmin=false)
    {
        $key = sha1(password_hash($user->login.$user->password.microtime(), PASSWORD_DEFAULT));
        $user->status = Account::STATUS_NEW;
        $user->request_date = date('Y-m-d H:i:s');
        if ($byAdmin || (preg_match('/^([AU]):/', $user->keyactivate , $m) && $m[1] == 'A')) {
            $user->keyactivate = 'A:'.$key;
            $this->sendRegistrationMail($user,
                'jcommunity~mail.registration.admin.body.html',
                'jcommunity~password_confirm_registration:resetform');
        }
        else {
            $user->keyactivate = 'U:'.$key;
            $this->sendRegistrationMail($user,
                'jcommunity~mail.registration.body.html',
                'jcommunity~registration:confirm');
        }
        \jAuth::updateUser($user);
    }


    protected function sendRegistrationMail($user, $tplLocaleId, $mailLinkAction)
    {

        $config = new Config();
        list($domain, $websiteUri) = $config->getDomainAndServerURI();

        $tpl = new \jTpl();
        $tpl->assign('user', $user);
        $tpl->assign('confirmation_link', \jUrl::getFull(
            $mailLinkAction,
            array('login' => $user->login, 'key' => substr($user->keyactivate, 2))
        ));
        $tpl->assign('validationKeyTTL', $config->getValidationKeyTTLAsString());

        $config->sendHtmlEmail(
            $user->email,
            \jLocale::get('jcommunity~mail.registration.subject', $domain),
            $tpl,
            \jLocale::get($tplLocaleId),
            $this->replyToEmail
        );
    }


    const CONFIRMATION_ALREADY_DONE = "alreadydone";
    const CONFIRMATION_DONE = "ok";
    const CONFIRMATION_BAD_KEY = "badkey";
    const CONFIRMATION_BAD_STATUS = "badstatus";
    const CONFIRMATION_EXPIRED_KEY = "expiredkey";

    /**
     * @return string one of CONFIRMATION_* const
     */
    public function confirm($login, $key) {
        $user = \jAuth::getUser($login);
        if (!$user) {
            return self::CONFIRMATION_BAD_KEY;
        }

        if ($user->status != Account::STATUS_NEW) {
            if ($user->status == Account::STATUS_VALID) {
                return self::CONFIRMATION_ALREADY_DONE;
            }
            return self::CONFIRMATION_BAD_STATUS;
        }

        if ($user->keyactivate == '') {
            return self::CONFIRMATION_BAD_KEY;
        }

        $keyactivate = $user->keyactivate;
        if (preg_match('/^([AU]:)(.+)$/', $keyactivate , $m)) {
            $keyactivate = $m[2];
        }

        if ($keyactivate != $key) {
            return self::CONFIRMATION_BAD_KEY;
        }

        $config = new Config();
        $dtNow = new \DateTime();
        $dt = new \DateTime($user->request_date);
        $dt->add($config->getValidationKeyTTL());
        if ($dt < $dtNow ) {
            return self::CONFIRMATION_EXPIRED_KEY;
        }

        $user->keyactivate = '';
        $user->status = Account::STATUS_VALID;
        \jEvent::notify('jcommunity_registration_confirm', array('user' => $user));
        \jAuth::updateUser($user);
        return self::CONFIRMATION_DONE;
    }

}
