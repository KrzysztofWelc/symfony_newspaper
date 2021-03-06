<?php
/**
 * User fixtures.
 */

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures.
 */
class UserFixtures extends AbstractBaseFixtures
{
    /**
     * Password encoder.
     *
     * @var Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserFixtures constructor.
     *
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder Password encoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Load data.
     *
     * @param \Doctrine\Persistence\ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(10, 'users', function ($i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([User::ROLE_USER]);
            $user->setCanPublish(true);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'user1234'
                )
            );

            return $user;
        });

        $this->createMany(5, 'admins', function ($i) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@example.com', $i));
            $user->setRoles([User::ROLE_USER, User::ROLE_REDACTOR, User::ROLE_ADMIN]);
            $user->setCanPublish(true);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'admin1234'
                )
            );

            return $user;
        });

        $this->createMany(2, 'redactors', function ($i) {
            $user = new User();
            $user->setEmail(sprintf('redactor%d@example.com', $i));
            $user->setRoles([User::ROLE_USER, User::ROLE_REDACTOR]);
            $user->setCanPublish(true);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'admin1234'
                )
            );

            return $user;
        });

        $this->createMany(1, 'super_admins', function ($i) {
            $user = new User();
            $user->setEmail(sprintf('superadmin%d@example.com', $i));
            $user->setRoles([User::ROLE_USER, User::ROLE_REDACTOR, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN]);
            $user->setCanPublish(true);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'admin1234'
                )
            );

            return $user;
        });

        $manager->flush();
    }
}
