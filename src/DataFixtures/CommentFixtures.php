<?php
/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CommentFixtures.
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(150, 'comments', function ($i) {
            $comment = new Comment();
            $comment->setBody($this->faker->sentence);
            $comment->setArticle($this->getRandomReference('articles'));
            $comment->setAuthor($this->getRandomReference('users'));

            return $comment;
        });

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array Array of dependencies
     */
    public function getDependencies(): array
    {
        return [ArticleFixture::class, UserFixtures::class];
    }
}
