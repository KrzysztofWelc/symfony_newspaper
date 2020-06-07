<?php
/*
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService.
 */
class UserService
{
    /**
     * @var App\Repository\UserRepository;
     */
    private $userRepository;

    /**
     * Password encoder.
     *
     * @var Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserService constructor.
     *
     * @param UserRepository $repository User repository
     */
    public function __construct(UserRepository $repository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $repository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Save user.
     *
     * @param User $user user entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Change passoword.
     *
     * @param User   $user User entity
     * @param string $old  old password
     * @param string $new  new password
     */
    public function changePassowrd(User $user, string $old, string $new): void
    {
        $pwdCheck = $this->passwordEncoder->isPasswordValid($user, $old);

        if ($pwdCheck) {
            $newEncodedPwd = $this->passwordEncoder->encodePassword($user, $new);
            $user->setPassword($newEncodedPwd);
            $this->save($user);
        }
    }
}
