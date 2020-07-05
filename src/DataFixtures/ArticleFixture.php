<?php
/**
 * Article fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ArticleFixture.
 */
class ArticleFixture extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     * @param ObjectManager $manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(50, 'articles', function ($i) {
            $groups = ['admins', 'redactors'];
            $ownerType = $groups[rand(0, 1)];

            $article = new Article();
            $article->setTitle($this->faker->sentence);
            $article->setBody($this->faker->text(700));
            $article->setIsPublished(true);
            $article->setCategory($this->getRandomReference('categories'));
            $article->setAuthor($this->getRandomReference($ownerType));

            return $article;
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
        return [CategoryFixtures::class, UserFixtures::class];
    }
}
