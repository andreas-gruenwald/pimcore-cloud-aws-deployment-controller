<?php
namespace Pimcore\Cloud\AWS\Deployment\Service\ParamManager;

use League\CLImate\CLImate;
use Pimcore\Cloud\AWS\Deployment\Exception\ConfigException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CliAgent
{
    private InputInterface $input;
    private OutputInterface $output;
    private CliMate $climate;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->climate = new CLImate;
    }

    /**
     * @Improvement switch to YML
     * @param string $paramName
     * @return array|false|mixed|string
     * @throws ConfigException
     */
    public function getParameter(string $paramName, bool $mustConfirm = false) {
        $value = getenv($paramName);
        if (empty($value)) {

            $paramManager = new ConfigFile();
            $params = $paramManager->readParams();

            if (isset($params[$paramName])) {
                $value = $params[$paramName];
                if ($mustConfirm) {
                    $value = $this->confirmExistingValue($paramName, $value);
                }
            } else {
                $helper = new QuestionHelper();
                $question = new Question(sprintf('Please specifiy parameter "%s": ', $paramName));
                do {
                    $value = trim($helper->ask($this->input, $this->output, $question));
                } while (!$value);

                $params[$paramName] = $value;
                $paramManager->writeParams($params);
            }
        } elseif ($mustConfirm) {
            $value = $this->confirmExistingValue($paramName, $value);
        }
        putenv($paramName.'='.$value);
        return $value;
    }

    public function hasParameter(string $paramName) : bool {
        $value = getenv($paramName) ? : null;
        if (empty($value)) {
            $paramManager = new ConfigFile();
            $params = $paramManager->readParams();

            if (isset($params[$paramName])) {
                $value = $params[$paramName];
            }
        }
        return isset($value);
    }

    private function confirmExistingValue(string $paramName, $value) {
        $currentValue = $value;
        $helper = new QuestionHelper();
        $question = new Question(sprintf('Please confirm parameter "%s" [%s]: ', $paramName, $value),$value );
        do {
            $value = $helper->ask($this->input, $this->output, $question);
        } while (!$value);

        if ($value != $currentValue) {
            $paramManager = new ConfigFile();
            $params = $paramManager->readParams();
            $params[$paramName] = $value;
            $paramManager->writeParams($params);
        }

        return $value;
    }

    public function confirmParameter(string $paramName) {
        return $this->getParameter($paramName, true);
    }

    public function setParameter(string $paramName, string $value) {
        $paramManager = new ConfigFile();
        $params = $paramManager->readParams();
        $params[$paramName] = $value;
        $paramManager->writeParams($params);
    }

    /**
     * @return CLImate
     */
    public function getClimate(): CLImate
    {
        return $this->climate;
    }

    /**
     * @param CLImate $climate
     * @return CliAgent
     */
    public function setClimate(CLImate $climate): CliAgent
    {
        $this->climate = $climate;
        return $this;
    }
}