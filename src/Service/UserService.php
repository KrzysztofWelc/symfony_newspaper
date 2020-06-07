<?php
/*
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * Class UserService.
 */
class UserService{

    /**
     * @var App\Repository\UserRepository;
     */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $repository User repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->userRepository = $repository;
    }

    /**
     * Save user.
     * @param User $user user entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }
}