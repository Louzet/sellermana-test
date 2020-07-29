<?php declare(strict_types=1);

namespace App\Manager;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function createUser(string $email, string $password, array $role = ['ROLE_USER'])
    {
        $user = (new User())
            ->setEmail($email)
            ->setRoles($role)
            ;

        $passwordEncoded = $this->encoder->encodePassword($user, $password);

        $user->setPassword($passwordEncoded);

        return $user;
    }
}