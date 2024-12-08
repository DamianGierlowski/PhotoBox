<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:create',
    description: 'Creating a user',
)]
class UserCreateCommand extends Command
{
    public function __construct(
        private readonly UserService $service,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('plainPassword', InputArgument::REQUIRED, 'User password')
            ->addArgument('name', InputArgument::REQUIRED, 'User name')
            ->addArgument('surname', InputArgument::REQUIRED, 'User surname')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('plainPassword');
        $name = $input->getArgument('name');
        $surname = $input->getArgument('surname');

        $this->service->createUser($email, $name, $surname, $plainPassword);

        $io->success('You have successfully created an user!');

        return Command::SUCCESS;
    }
}
