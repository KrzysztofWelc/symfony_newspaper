<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminPermissionsType;
use App\Form\AdminPwdChangeType;
use App\Form\BlockUserType;
use App\Form\CredentialsType;
use App\Form\PasswordChangeType;
use App\Form\SuperAdminPermissionsType;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var Symfony\Contracts\Translation\TranslatorInterface translator Interface
     */
    private $translator;

    /**
     * UserController constructor.
     *
     * @param UserService         $service    user service
     * @param TranslatorInterface $translator
     */
    public function __construct(UserService $service, TranslatorInterface $translator)
    {
        $this->userService = $service;
        $this->translator = $translator;
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
     * @return Response
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
     * @Security("is_granted('BLOCK', usr) or is_granted('EDIT', usr)")
     */
    public function changeEmail(Request $request, User $usr): Response
    {
        $form = $this->createForm(CredentialsType::class, $usr, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($usr);

            $this->addFlash('success', $this->translator->trans('email_updated_msg'));

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
     * @return Response
     *
     * @Route(
     *     "/user_change_password/{id}",
     *     name="user_password_change",
     *     methods={"GET", "PUT"}
     * )
     *
     * @Security("is_granted('BLOCK', usr) or is_granted('EDIT', usr)")
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

            $status = $this->userService->changePassword($isAdmin, $usr, $newPassword, $oldPassword);
            $flashType = $status ? 'success' : 'danger';
            $flashMsg = $status ? 'password_updated_msg' : 'wrong_password_msg';

            $this->addFlash($flashType, $this->translator->trans($flashMsg));

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
            $status = $usr->getCanPublish() ? 'user_unblocked_msg' : 'user_blocked_msg';

            $this->userService->save($usr);

            $this->addFlash('success', $this->translator->trans($status));

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

    /**
     * Change user's permissions action.
     *
     * @param Request $request HTTP request
     * @param User    $usr     user entity
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/change_permissions/{id}",
     *     name="user_permissions",
     *     methods={"GET", "PUT"}
     * )
     *
     * @Security("is_granted('BLOCK', usr)")
     */
    public function changePermissions(Request $request, User $usr): Response
    {
        $formType = $this->isGranted('ROLE_SUPER_ADMIN') ? SuperAdminPermissionsType::class : AdminPermissionsType::class;
        $form = $this->createForm($formType, null, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();

            switch ($role) {
                case User::ROLE_USER:
                    $newRoles = [User::ROLE_USER];
                    $usr->setRoles($newRoles);
                    break;
                case User::ROLE_REDACTOR:
                    $newRoles = [User::ROLE_USER, User::ROLE_REDACTOR];
                    $usr->setRoles($newRoles);
                    break;
                case User::ROLE_ADMIN:
                    $newRoles = [User::ROLE_USER, User::ROLE_REDACTOR, User::ROLE_ADMIN];
                    $usr->setRoles($newRoles);
                    break;
            }

            $this->userService->save($usr);
            $this->addFlash('success', $this->translator->trans('permission_updated_msg'));

            return $this->redirectToRoute('user_profile', ['id' => $usr->getId()]);
        }

        return $this->render(
            'user/changePermissions.html.twig',
            ['form' => $form->createView(), 'id' => $usr->getId()]
        );
    }
}
