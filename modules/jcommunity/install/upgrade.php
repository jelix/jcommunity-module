<?php
/**
 * @package     jcommunity
 * @author      Laurent Jouanneau
 * @copyright   2023 Laurent Jouanneau
 * @link        https://www.jelix.org
 * @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Upgrader for Jelix 1.7+
 */
class jcommunityModuleUpgrader extends \Jelix\Installer\Module\Installer
{
    public function install(Jelix\Installer\Module\API\InstallHelpers $helpers)
    {
        // remove deprecated key for Jelix 1.7+
        $liveConfigIni = $helpers->getLiveConfigIni();
        $liveConfigIni->removeValue('persistant_crypt_key', 'coordplugin_auth');
        $goodkey = $liveConfigIni->getValue('persistant_encryption_key', 'coordplugin_auth');
        if (!$goodkey) {
            $cryptokey = \Defuse\Crypto\Key::createNewRandomKey();
            $key = $cryptokey->saveToAsciiSafeString();
            $liveConfigIni->setValue('persistant_encryption_key', $key, 'coordplugin_auth');
        }
        else {
            // Check that the stored key is good, else regenerate it.
            try {
                $cryptokey = \Defuse\Crypto\Key::loadFromAsciiSafeString($goodkey);
            } catch (\Defuse\Crypto\Exception\CryptoException $e) {
                $cryptokey = \Defuse\Crypto\Key::createNewRandomKey();
                $key = $cryptokey->saveToAsciiSafeString();
                $liveConfigIni->setValue('persistant_encryption_key', $key, 'coordplugin_auth');
            }
        }
    }
}
