<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'app:create-user';
    /**
     * @var bool
     */
    private $requiredPassword;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var User
     */
    private $user;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(string $name = null, bool $requiredPassword = false, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder) {
        parent::__construct($name);
        $this->requiredPassword = $requiredPassword;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();
        $helper = $this->getHelper('question');
        $io->section('Create your user.');

        $question = new Question('Enter the username : ', 'Admin');
        $username = $helper->ask($input, $output, $question);
        $user->setUsername($username);
        $io->newLine();
        $output->writeln('<info>Username added</info>');

        $io->newLine();

        $question = new Question('Enter email : ');
        $email = $helper->ask($input, $output, $question);
        $user->setEmail($email);
        $io->newLine();
        $output->writeln('<info>Email added</info>');

        $io->newLine();

        $questionPassword = $io->askHidden('Enter Password : ');
        $plainPassword = $questionPassword;
        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        $io->newLine();
        $output->writeln('<info>Password added and encoded</info>');

        $io->newLine();

        $question = new Question('Enter first name : ', 'John');
        $firstName = $helper->ask($input, $output, $question);
        $user->setFirstName($firstName);
        $io->newLine();
        $output->writeln('<info>First name added</info>');

        $io->newLine();

        $question = new Question('Enter family name : ', 'Doe');
        $familyName = $helper->ask($input, $output, $question);
        $user->setLastName($familyName);
        $io->newLine();
        $output->writeln('<info>Family name added</info>');

        $io->newLine();

        $question = new ChoiceQuestion('Please set the role of your new user <info>(Default is User)</info> : ', [
            'ROLE_SUPER_ADMIN' => 'Super Administrator',
            'ROLE_ADMIN' => 'Adminstrator',
            'ROLE_MODERATOR' => 'Moderator',
            'ROLE_SUPPORT' => 'Support',
            'ROLE_USER' => 'User'
        ], '5');
        $question->setMultiselect(true);
        $roles = $helper->ask($input, $output, $question);
        $user->setRoles($roles);
        $io->newLine();
        $output->writeln('<info>Role(s) added - ' . implode(', ', $roles) . '</info>');

        $io->newLine();

        $io->warning('Do you want create this user with :');
        $output->writeln([
            'Username : ' . $user->getUsername(),
            'Email : ' . $user->getEmail(),
            'Password (not encoded) : ' . $plainPassword,
            'Encoded password : ' . $user->getPassword(),
            'First name : ' . $user->getFirstName(),
            'Family name : ' . $user->getLastName(),
            'Roles : ' . implode(', ', $user->getRoles())
        ]);

        $io->confirm('Yes, I want create this user.', true);

        $user->setCreatedAt(new \DateTime());
        $user->setEnabled(true);
        $this->manager->persist($user);
        $this->manager->flush();
        $io->success('Success ! The user successfully added in database.');

        return 0;
    }
}
