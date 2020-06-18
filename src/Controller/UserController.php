<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminPwdChangeType;
use App\Form\BlockUserType;
use App\Form\CredentialsType;
use App\Form\PasswordChangeType;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     *
     * @param UserService $service user service
     */
    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    /**
     * Profile action.
     *
     * @param Request $request HTTP request
     * @param User    $usr     User entity
     *
     * @return Response HTTP response
     *
     * @Route("/profile/{id}", name="user_profile")
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('EDIT', usr)")
     */
    public function profile(Request $request, User $usr): Response
    {
        $page = $request->query->getInt('page', 1);
        $paginator = $this->userService->createPaginatedArticlesList($page, $usr);

        return $this->render(
            'user/profile.html.twig',
            ['user' => $usr, 'pagination' => $paginator]
        );
    }

    /**
     * Change email action.
     *
     * @param Request $request HTTP request
     * @param User    $usr     User entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/change_email/{id}",
     *     name="user_email_change",
     *     methods={"GET", "PUT"}
     * )
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('EDIT', usr)")
     */
    public function changeEmail(Request $request, User $usr): Response
    {
        $form = $this->createForm(CredentialsType::class, $usr, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($usr);

            $this->addFlash('success', 'email has been hanged');

            return $this->redirectToRoute('user_profile', ['id' => $usr->getId()]);
        }

        return $this->render(
            'user/changeEmail.html.twig',
            [
                'form' => $form->createView(),
                'id' => $usr->getId(),
            ]
        );
    }

    /**
     * Change password.
     *
     * @param Request $request HTTP request
     * @param User    $usr     User entity
     *
     * @Route(
     *     "/user_change_password/{id}",
     *     name="user_password_change",
     *     methods={"GET", "PUT"}
     * )
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('EDIT', usr)")
     */
    public function changePassword(Request $request, User $usr): Response
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        $type = $isAdmin ? AdminPwdChangeType::class : PasswordChangeType::class;
        $form = $this->createForm($type, $usr, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $isAdmin ? null : $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            dump($usr);

            $status = $this->userService->changePassword($isAdmin, $usr, $newPassword, $oldPassword);
            $flashType = $status ? 'success' : 'danger';
            $flashMsg = $status ? 'password has been changed' : 'wrong current password';

            $this->addFlash($flashType, $flashMsg);

            return $this->redirectToRoute('user_profile', ['id' => $usr->getId()]);
        }

        return $this->render(
            'user/changePassword.html.twig',
            [
                'form' => $form->createView(),
                'id' => $usr->getId(),
            ]
        );
    }

    /**
     * Block / unblock user action.
     *
     * @param Request $request HTTP request
     * @param User    $usr     user entity
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/block_user/{id}",
     *     name="user_block",
     *     methods={"GET", "PUT"}
     * )
     *
     * @Security("is_granted('BLOCK', usr)")
     */
    public function blockUser(Request $request, User $usr): Response
    {
        $form = $this->createForm(BlockUserType::class, $usr, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $usr->getCanPublish() ? 'user unblocked' : 'user blocked';

            $this->userService->save($usr);

            $this->addFlash('success', $status);

            return $this->redirectToRoute('user_profile', ['id' => $usr->getId()]);
        }

        return $this->render(
            'user/blockUser.html.twig',
            [
                'form' => $form->createView(),
                'id' => $usr->getId(),
            ]
        );
    }
}
