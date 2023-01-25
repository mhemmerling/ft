<?php

namespace App\Message\Application;

use App\Message\Command\CreateMessage;
use App\Message\Domain\MessageContext;
use App\User\Application\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:notify-account-expires',
    description: 'Sends emails to users whose account will expire in the next 30 days',
)]
class NotifyAccountExpiresCommand extends Command
{
    public function __construct(
        private readonly MessageService $messageService,
        private readonly UserService $userService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // for testing purposes - send the mails to all users
            $users = $this->userService->getUsers();
            $userIds = [];

            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            $command = new CreateMessage(
                'Your account is about to expire',
                'Your account will expire in 30 days, please renew it',
                null,
                MessageContext::SYSTEM->value,
                $userIds
            );

            $this->messageService->send($command);

            return Command::SUCCESS;
        } catch (\Throwable) {
            return Command::FAILURE;
        }
    }
}
