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
      $configFile = __DIR__ . '/../config/parameters.yaml';

      if (!file_exists($configFile)) {
        printf('No configuration found using defaults (parameters.yaml.dist)' . PHP_EOL . PHP_EOL, $configFile);
        $configFile = sprintf('%s.dist', $configFile);
      }

      return Yaml::parseFile($configFile);
    }
  ],
];
