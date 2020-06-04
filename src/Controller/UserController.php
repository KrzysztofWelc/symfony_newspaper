<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\CredentialsType;
use App\Repository\UserRepository;
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/change_email",
     *     name="user_email_change",
     *     methods={"GET", "PUT"}
     * )
     */
    public function changeEmail(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(CredentialsType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user);

            $this->addFlash('success', 'email has been hanged');

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/changeEmail.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/change_password",
     *     name="user_password_change",
     *     methods={"GET", "PUT"}
     * )
     */
    public function changePassword(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            dump($newPassword);
            $userRepository->setNewPassword($user, $newPassword);

            $this->addFlash('success', 'password has been hanged');

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/changePassword.html.twig',
            ['form' => $form->createView()]
        );
    }
}
