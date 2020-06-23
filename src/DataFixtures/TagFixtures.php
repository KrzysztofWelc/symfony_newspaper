<?php
/**
 * Tag fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class TagFixtures.
 */
class TagFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(20, 'tags', function ($i) {
            $tag = new Tag();
            $tag->setName($this->faker->word);
            $tag->addArticle($this->getRandomReference('articles'));
            $tag->addArticle($this->getRandomReference('articles'));
            $tag->addArticle($this->getRandomReference('articles'));

            return $tag;
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
        return [ArticleFixture::class];
    }
}
