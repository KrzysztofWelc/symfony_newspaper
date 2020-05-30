<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Comment controller.
 *
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * Add comment action.
     *
     * @param Symfony\Component\HttpFoundation\Request $request           HTTP request
     * @param App\Entity\Article                       $article           Article entity selected by id param form URL
     * @param App\Repository\CommentRepository         $commentRepository comment repository
     *
     * @return Response HTTP Resposne
     *
     * @Route(
     *     "/{id}/add",
     *     name="comment_add",
     *     methods={"GET", "POST"}
     *     )
     */
    public function add(Request $request, Article $article, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setArticle($article);
            $commentRepository->save($comment);

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render(
            'comment/create.html.twig',
            [
                'id' => $article->getId(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param CommentRepository $commentRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/delete/{id}",
     *     name="comment_delete",
     *     methods={"GET", "DELETE"}
     *     )
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('DELETE', comment)")
     */
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(FormType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->delete($comment);

            $this->addFlash('success', 'comment deleted');

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'comment/delete.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }
}
