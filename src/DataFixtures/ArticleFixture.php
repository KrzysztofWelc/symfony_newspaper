<?php
/**
 * Article fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class ArticleFixture.
 */
class ArticleFixture extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; ++$i) {
            $article = new Article();
            $article->setTitle($this->faker->sentence);
            $article->setBody($this->faker->text(700));
//            $article->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $article->setCategory($this->getRandomReference('categories'));
            $this->manager->persist($article);
        }
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
        return [CategoryFixtures::class];
    }
}
