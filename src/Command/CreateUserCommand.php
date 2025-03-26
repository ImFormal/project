<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[AsCommand(
    name: 'create-user',
    description: 'Commande pour ajouter un utilisateur en BDD',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this

            // configure an argument
            ->addArgument('firstname', InputArgument::REQUIRED, 'Prénom de l\'utilisateur')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Nom de l\'utilisateur')
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('password', InputArgument::REQUIRED, 'Email de l\'utilisateur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $firstname = $input->getArgument('firstname');
        $lastname = $input->getArgument('lastname');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        if ($firstname) {
            $io->note(sprintf('You passed an argument firstname : %s', $firstname));
        }
        if ($lastname) {
            $io->note(sprintf('You passed an argument lastname: %s', $lastname));
        }
        if ($email) {
            $io->note(sprintf('You passed an argument email: %s', $email));
        }
        if ($password) {
            $io->note(sprintf('You passed an argument password : %s', $password));
        }

        if ($this->userRepository->findOneBy(['email' => $email])) {
            $io->error('Cet email est déjà utilisé !');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $hashedPassword = $this->hasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        try {
            $this->em->persist($user);
            $this->em->flush();
            $io->success('Le compte utilisateur a été créé avec succès.');
        } catch (\Exception $e) {
            $io->error('Une erreur est survenue lors de la création du compte : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}