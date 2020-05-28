<?php
/**
 * Tags data transformer.
 */

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Form\DataTransformerInterface;

class TagsDataTransformer implements DataTransformerInterface
{
    /**
     * Tag repository.
     *
     * @var \App\Repository\TagRepository
     */
    private $repository;

    /**
     * TagsDataTransformer constructor.
     *
     * @param \App\Repository\TagRepository $repository Tag repository
     */
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Transform array of tags to string of names.
     *
     * @param \Doctrine\Common\Collections\Collection $tags Tags entity collection
     *
     * @return string Result
     */
    public function transform($tags): string
    {
        if (null == $tags) {
            return '';
        }

        $tagNames = [];

        foreach ($tags as $tag) {
            $tagNames[] = $tag->getName();
        }

        $result = implode(',', $tagNames);
        dump($result);

        return $result;
    }

    /**
     * Transform string of tag names into array of Tag entities.
     *
     * @param string $value String of tag names
     *
     * @return array Result
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function reverseTransform($value): array
    {
        $tagTitles = explode(',', $value);

        $tags = [];

        foreach ($tagTitles as $tagTitle) {
            if ('' !== trim($tagTitle)) {
                $tag = $this->repository->findOneByName(strtolower($tagTitle));
                if (null == $tag) {
                    $tag = new Tag();
                    $tag->setName($tagTitle);
                    $this->repository->save($tag);
                }
                $tags[] = $tag;
            }
        }

        dump($tags);
        return $tags;
    }
}
