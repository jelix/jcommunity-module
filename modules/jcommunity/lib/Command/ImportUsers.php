<?php
/**
 * @author      Laurent Jouanneau
 * @copyright   2026 Laurent Jouanneau
 *
 * @see         https://jelix.org
 * @licence     http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
 */

namespace Jelix\JCommunity\Command;

use Jelix\JCommunity\Registration;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportUsers extends \Jelix\Scripts\ModuleCommandAbstract
{
    protected $registration;

    public function __construct()
    {
        $this->registration = new Registration();
        parent::__construct();
    }

    protected function configure()
    {
        $authorizedFields = $this->registration->getAutorizedPropertiesForImport();
        $fields = implode(', ', $authorizedFields);
        $this
            ->setName('jcommunity:user:import')
            ->setDescription(\jLocale::get('jcommunity~register.cmdline.import.help.description'))
            ->setHelp(\jLocale::get('jcommunity~register.cmdline.import.help.text'))
            ->addArgument(
                'csvfile',
                InputArgument::REQUIRED,
                \jLocale::get('jcommunity~register.cmdline.import.help.parameter.csvfile')
            )
            ->addArgument(
                'fields',
                InputArgument::REQUIRED,
                \jLocale::get('jcommunity~register.cmdline.import.help.parameter.fields', [$fields])
            )
            ->addOption(
                'csv-separator',
                null,
                InputOption::VALUE_REQUIRED,
                \jLocale::get('jcommunity~register.cmdline.import.help.option.separator'),
                ','
            )
            ->addOption(
                'csv-enclosure',
                null,
                InputOption::VALUE_REQUIRED,
                \jLocale::get('jcommunity~register.cmdline.import.help.option.enclosure'),
                '"'
            )
            ->addOption(
                'reset-password',
                null,
                InputOption::VALUE_NONE,
                \jLocale::get('jcommunity~register.cmdline.create.help.option.reset')
            )
            ->addOption(
                'wait-between-email',
                null,
                InputOption::VALUE_REQUIRED,
                \jLocale::get('jcommunity~register.cmdline.import.help.option.wait'),
                3
            )
            ->addOption(
                'max-accounts',
                null,
                InputOption::VALUE_REQUIRED,
                \jLocale::get('jcommunity~register.cmdline.import.help.option.maxaccount'),
                0
            )
            ->addOption(
                'ignore-first-line',
                null,
                InputOption::VALUE_NONE,
                \jLocale::get('jcommunity~register.cmdline.import.help.option.ignorefirstline'),
            )
        ;
    }

    protected function displayMessage(OutputInterface $output, $exitCode, $message = null)
    {
        if ($output->isVerbose() && $message) {
            $output->writeln($message);
        }
        return $exitCode;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csvFileName = $input->getArgument('csvfile');
        $fields = $input->getArgument('fields');
        $reset = $input->getOption('reset-password');
        $separator = $input->getOption('csv-separator');
        $enclosure = $input->getOption('csv-enclosure');
        $maxLines = $input->getOption('max-accounts');
        $ignoreFirstLine = $input->getOption('ignore-first-line');
        $milliSecRate = $input->getOption('wait-between-email') * 100000;

        // check that given fields are ok
        $authorizedFields = $this->registration->getAutorizedPropertiesForImport();
        $fieldsOrder = preg_split('/\s*,\s*/', $fields);
        $fieldsOrder = array_map(function($val) {
            if ($val == '') {
                $val = '_ignore_';
            }
            return $val;
        }, $fieldsOrder);

        $unknownFields = array_diff($fieldsOrder, $authorizedFields);
        if (count($unknownFields)) {
            $output->writeln('<error>'.\jLocale::get('jcommunity~register.cmdline.import.error.unknownfield', [implode(',', $unknownFields)]).'</error>');
            return 1;
        }

        if (!in_array('login', $fieldsOrder)) {
            $output->writeln('<error>'.\jLocale::get('jcommunity~register.cmdline.import.error.login.missing').'</error>');
            return 2;
        }
        if (!in_array('email', $fieldsOrder)) {
            $output->writeln('<error>'.\jLocale::get('jcommunity~register.cmdline.import.error.email.missing').'</error>');
            return 3;
        }

        $handle = fopen($csvFileName, 'r');
        if (!$handle) {
            $output->writeln('<error>'.\jLocale::get('jcommunity~register.cmdline.import.error.opening.file', [$csvFileName]).'</error>');// FIXME
            return 1;
        }

        $line = 0;
        $importedLines = 0;
        while (($csvRow = fgetcsv($handle, null, $separator, $enclosure, '\\')) !== false) {
            $line++;

            if ($ignoreFirstLine && $line == 1) {
                continue;
            }

            if (count($csvRow) < count($fieldsOrder)) {
                $output->writeln(\jLocale::get('jcommunity~register.cmdline.import.error.bad.field.count', [$line]));
                continue;
            }

            try {
                $user = $this->registration->importUser($csvRow, $fieldsOrder, $reset);
                if (is_string($user)) {
                    $output->writeln(\jLocale::get('jcommunity~register.cmdline.import.notice.login.exists', [$line, $user]));
                }
                else {
                    $output->writeln(\jLocale::get('jcommunity~register.cmdline.import.notice.login.imported', [$line, $user->login]));
                }
            } catch (\Exception $e) {
                $output->writeln($e->getMessage().' (line '.$line.')');
            }

            if ($maxLines && $importedLines >= $maxLines) {
                break;
            }

            if ($reset) {
                usleep($milliSecRate);
            }
        }
        fclose($handle);

        return 0;
    }



}
