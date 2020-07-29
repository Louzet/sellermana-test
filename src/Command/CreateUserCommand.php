<?php

namespace App\Command;

use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-admin';

    private $userManager;
    private $em;

    public function __construct(UserManager $userManager, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->em = $em;

    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new user')
            ->setDefinition([
                new InputArgument('email', InputArgument::OPTIONAL),
                new InputArgument('password', InputArgument::OPTIONAL),
            ])
            ->setHelp('This command allows you to create a user...')
        ;
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if(!$input->getArgument('email')) {
            $question = new Question('Enter a valid email');
            $question->setValidator(function($email) {
                if (empty($email)) {
                    throw new \Exception("Email cannot be empty");
                }

                return $email;
            });

            $questions['email'] = $question;
        }

        if(!$input->getArgument('password')) {
            $question = new Question('Enter a valid password');
            $question->setValidator(function($password) {
                if(empty($password)) {
                    throw new \Exception("Password cannot be empty");
                }
                if(\strlen($password) < 4) {
                    throw new \Exception("Password must be at least 4 characters");
                }

                return $password;
            });

            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln([
        'User Creation :',
        '==============',
        '',
        ]);

        try {

            $email = $input->getArgument('email');
            $password = $input->getArgument('password');

            $user = $this->userManager->createUser($email, $password, ['ROLE_ADMIN']);

            $this->em->persist($user);
            $this->em->flush();

        } catch (\Exception $exception) {
            throw $exception;
        }

        $io->success("Congrats ! User ${email} has been successfully added.");

        return Command::SUCCESS;
    }
}
