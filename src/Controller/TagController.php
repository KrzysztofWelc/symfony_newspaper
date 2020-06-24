<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagSearchType;
use App\Repository\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagController.
 *
 * @Route("/tag")
 */
class TagController extends AbstractController
{
    /**
     * @var TagRepository;
     */
    private $repository;

    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Tags index.
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     *
     * @Route("/", name="tag_index")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $tags = $this->repository->findAll();
        $pagination = $paginator->paginate(
            $tags,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'tag/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param App\Entity\Tag $tag Tag entity
     *
     * @return Response
     *
     * @Route("/show/{code}", name="tag_show")
     */
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * Search action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/search", name="tag_search")
     */
    public function search(Request $request): Response
    {
        $form = $this->createForm(TagSearchType::class, null);
        $form->handleRequest($request);
        $articles = null;
        $searched = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $phrase = $form->get('phrase')->getData();
            $tag = $this->repository->findOneByName($phrase);

            if ($tag) {
                $articles = $tag->getArticles();
            }
            $searched = true;
        }

        return $this->render(
            'tag/search.html.twig',
            [
                'form' => $form->createView(),
                'articles' => $articles,
                'searched' => $searched,
            ]
        );
    }
}
