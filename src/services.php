<?php
use Zend\ServiceManager\ServiceManager;
use App\Command\SMS\SendCommand;
use Symfony\Component\Yaml\Yaml;

return [
  'factories' => [
    SendCommand::class => function (ServiceManager $serviceManager) {
      return new SendCommand($serviceManager);
    },

    Yaml::class => function (ServiceManager $serviceManager) {
      $configFile = getenv("HOME") . '/./phpsms.yaml';
      $altConfigFile = '/etc/messagebird-php-application.yaml';

      if (file_exists($configFile)) {
        return Yaml::parseFile($configFile);
      } 
      if (file_exists($altConfigFile)) {
        return Yaml::parseFile($altConfigFile);
      }

      printf('No configuration found using defaults (parameters.yaml.dist)' . PHP_EOL . PHP_EOL, $altConfigFile);

      return Yaml::parseFile(__DIR__ . '/../config/parameters.yaml.dist');
    }

  ],
];
