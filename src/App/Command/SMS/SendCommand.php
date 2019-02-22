<?php
namespace App\Command\SMS;

#use Symfony\Component\Console\Command\Command;
use App\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use MessageBird\Client as MessageBirdClient;
use MessageBird\Objects\Message as MessageBirdMessage;

/**
 *  * Class MyCommand
 *
 * @package App\Command
 */
class SendCommand extends AbstractCommand
{
  /**
   * @return void
   */
  protected function configure(): void
  {
    $this
      ->setName('sms:send')
      ->addArgument('message', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'What\'s the message you want to send?')
      ->addOption('to', 't', InputOption::VALUE_REQUIRED, 'What number you want to send it to?', $this->getConfig('defaults.to', null))

      ->addOption('originator', 'o', InputOption::VALUE_REQUIRED, 'Set Message display name', $this->getConfig('messagebird.originator', null))
      ->addOption('access-key', 'k', InputOption::VALUE_REQUIRED, 'Messagebird username?', $this->getConfig('messagebird.accesskey', null))
    ;
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output): void
  {
    $message = join(PHP_EOL, $input->getArgument('message'));
    $to = $input->getOption('to');
    $accesskey = $input->getOption('accesskey');
    $originator = $input->getOption('originator');

    $mb = new MessageBirdClient($accesskey);

    $output->writeln(sprintf('To: %s', $to));
    $output->writeln(sprintf('Message: %s', $message));

    $mbmsg = new MessageBirdMessage();
    $mbmsg->originator = $originator;
    $mbmsg->recipients = [
      $to
    ];

    $mbmsg->body = $message;
    
    try {
      $mbResult = $mb->messages->create($mbmsg);
    } catch (\MessageBird\Exceptions\AuthenticateException $e) {
      // That means that your accessKey is unknown
      $output->writeln('wrong login');
    } catch (\MessageBird\Exceptions\BalanceException $e) {
      // That means that you are out of credits, so do something about it.
      $output->writeln('no balance');
    } catch (\Exception $e) {
      $output->writeln($e->getMessage());
    }
  }
}
