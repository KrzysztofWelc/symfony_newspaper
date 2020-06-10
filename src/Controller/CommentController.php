<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\CommentService;
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
     * @var App\Service\CommentService
     */
    private $commentService;

    /**
     * CommentController constructor.
     * @param CommentService $commentService
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Add comment action.
     *
     * @param Symfony\Component\HttpFoundation\Request $request           HTTP request
     * @param App\Entity\Article                       $article           Article entity selected by id param form URL
     *
     * @return Response HTTP Resposne
     *
     * @Route(
     *     "/{id}/add",
     *     name="comment_add",
     *     methods={"GET", "POST"}
     *     )
     *
     * @isGranted("IS_AUTHENTICATED_FULLY")
     */
    public function add(Request $request, Article $article): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->save($comment, $article, $this->getUser());

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
     * @param Request $request http request
     * @param Comment $comment comment entity
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
    public function delete(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(FormType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);
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
