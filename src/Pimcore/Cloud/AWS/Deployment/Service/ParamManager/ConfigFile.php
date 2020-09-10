<?php
namespace Pimcore\Cloud\AWS\Deployment\Service\ParamManager;

use Aws\Ecs\EcsClient;
use League\CLImate\CLImate;
use Pimcore\Cloud\AWS\Deployment\Exception\ConfigException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpFoundation\File\File;

class ConfigFile
{
    private const CONFIG_DIR = '.pimcore-cloud';
    private const CONFIG_FILENAME = 'config.json';

    public function readParams(string $profile = "default") : array {
        $configFile = $this->accessConfigFile();
        $profiles = $this->readProfiles($configFile);
        $params = [];
        if (isset($profiles[$profile])) {
            $params = $profiles[$profile];
        }
        return $params;
    }

    private function readProfiles(File $configFile) : array {
        $profiles = [];
        $content = file_get_contents($configFile->getPath().'/'.$configFile->getBasename());
        if ($content) {
            $profiles = json_decode($content, true);
        }

        return $profiles ? : [];
    }

    public function writeParams(array $params, string $profile = "default") {
        $configFile = $this->accessConfigFile();
        $profiles = $this->readProfiles($configFile);
        $profiles[$profile] = $params;
        file_put_contents($configFile->getPath().'/'.$configFile->getBasename(), json_encode($profiles, JSON_PRETTY_PRINT));
    }

    public function accessConfigFile() : File {
        $configPath = $_SERVER['HOME'].'/'.static::CONFIG_DIR;
        $fullConfigFilePath = $configPath.'/'.static::CONFIG_FILENAME;

        $hasConfigDir = true;
        if (!file_exists($configPath)) {
            $hasConfigDir = mkdir($configPath);
        }

        if (!$hasConfigDir) {
            throw new ConfigException(sprintf('Directory "%s" does not exist and cannot be created. Please check permissions.', $configPath));
        }

        $hasConfigFile = true;
        if (!file_exists($fullConfigFilePath)) {
            $hasConfigFile = touch($fullConfigFilePath);
        }

        if (!$hasConfigFile) {
            throw new ConfigException(sprintf('File "%s" does not exist and cannot be created. Please check permissions.', $fullConfigFilePath));
        }

        $file = new File($fullConfigFilePath);

        return $file;
    }

    public function deleteProfile(string $profile) {
        $configFile = $this->accessConfigFile();
        $profiles = $this->readProfiles($configFile);
        if (array_key_exists($profile, $profiles)) {
            unset($profiles[$profile]);
            file_put_contents($configFile->getPath().'/'.$configFile->getBasename(), json_encode($profiles, JSON_PRETTY_PRINT));
        } else {
            throw new ConfigException(sprintf('Profile "%s" is not existing.', $profile));
        }
    }
}