<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagEditType;
use App\Form\TagSearchType;
use App\Repository\TagRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * @var Symfony\Contracts\Translation\TranslatorInterface translator Interface
     */
    private $translator;

    /**
     * TagController constructor.
     */
    public function __construct(TagRepository $repository, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * Tags index.
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
     * @Route("/show/{code}", name="tag_show")
     */
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/edit/{id}", name="tag_edit", methods={"GET", "PUT"},)
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function editTag(Request $request, Tag $tag)
    {
        $form = $this->createForm(TagEditType::class, $tag, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save($tag);
            $this->addFlash('success', $this->translator->trans('tag_updated_msg'));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/edit.html.twig',
            [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/delete/{id}",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="tag_delete",
     *     )
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(FormType::class, $tag, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->delete($tag);
            $this->addFlash('success', $this->translator->trans('tag_deleted_msg'));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/delete.html.twig',
            [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }

    /**
     * Search action.
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
