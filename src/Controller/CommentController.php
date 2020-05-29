<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
