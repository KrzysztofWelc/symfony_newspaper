<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CredentialsType;
use App\Form\PasswordChangeType;
use App\Repository\UserRepository;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @var App\Service\UserService
     */
    private $userService;

    /**
     * UserController constructor.
     * @param UserService $service user service
     */
    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    /**
     * Profile action.
     *
     * @Route("/profile/{id}", name="user_profile")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profile(): Response
    {
        return $this->render('user/profile.html.twig');
    }

    /**
     * Change email action.
     *
     * @param Request $request
     * @param UserRepository $userRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/change_email",
     *     name="user_email_change",
     *     methods={"GET", "PUT"}
     * )
     */
    public function changeEmail(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(CredentialsType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash('success', 'email has been hanged');

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/changeEmail.html.twig',
            ['form' => $form->createView()]
        );
    }

}
