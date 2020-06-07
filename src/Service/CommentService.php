<?php
/*
 * Comment service.
 */

namespace App\Service;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/*
 * Class CommentService.
 */
class CommentService
{
    /**
     * @var App\Repository\CommentRepository
     */
    private $commentRepository;

    /**
     * CommentService constructor.
     * @param CommentRepository $repository
     */
    public function __construct(CommentRepository $repository)
    {
        $this->commentRepository = $repository;
    }

    /**
     * Save comment.
     *
     * @param Comment $comment
     * @param Article $article
     * @param UserInterface $user
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Comment $comment, Article $article, UserInterface $user): void
    {
        if($comment instanceof  Comment){
            if ($article instanceof Article){
                if($user instanceof UserInterface){
                    $comment->setAuthor($user);
                    $comment->setArticle($article);
                    $this->commentRepository->save($comment);
                }
            }
        }
    }

    /**
     * Delete comment.
     *
     * @param Comment $comment
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }
}
