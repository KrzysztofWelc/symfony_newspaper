<?php
/*
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @var App\Repository\ArticleRepository;
     */
    private $articleRepository;

    /**
     * Password encoder.
     *
     * @var Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var Knp\Component\Pager\PaginatorInterface
     */
    private $paginator;

    /**
     * UserService constructor.
     *
     * @param UserRepository $repository User repository
     * @param ArticleRepository $articleRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param PaginatorInterface $paginator
     */
    public function __construct(UserRepository $repository,ArticleRepository $articleRepository, UserPasswordEncoderInterface $passwordEncoder, PaginatorInterface $paginator)
    {
        $this->userRepository = $repository;
        $this->articleRepository = $articleRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->paginator = $paginator;
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
     * Change password.
     *
     * @param bool   $isAdmin User status
     * @param User   $user    User entity
     * @param string $new     new password
     * @param string $old     old password
     *
     * @return bool success status
     */
    public function changePassword(bool $isAdmin, User $user, string $new, ?string $old): bool
    {
        $success = false;
        if ($isAdmin) {
//            admin change password procedure
            $newEncodedPwd = $this->passwordEncoder->encodePassword($user, $new);
            $user->setPassword($newEncodedPwd);
            $this->save($user);

            $success = true;
        } else {
//            standard user change password procedure
            $pwdCheck = $this->passwordEncoder->isPasswordValid($user, $old);

            if ($pwdCheck) {
                $newEncodedPwd = $this->passwordEncoder->encodePassword($user, $new);
                $user->setPassword($newEncodedPwd);
                $this->save($user);

                $success = true;
            }
        }

        return $success;
    }

    /**
     * @param int $page
     * @param User $usr
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface Paginated list
     */
    public function createPaginatedArticlesList(int $page, User $usr): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articleRepository->getUsersArticles($usr),
            $page,
            5
        );
    }
}
