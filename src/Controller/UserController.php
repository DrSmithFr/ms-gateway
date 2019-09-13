<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Service\UserService;
use App\Model\ConnectionModel;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\RegistrationModel;
use App\Repository\UserRepository;
use App\Controller\Traits\SerializerAware;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/users")
 */
class UserController extends AbstractController
{
    use SerializerAware;

    /**
     * UserController constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->setSerializer($serializer);
    }

    /**
     * @Route(path="/register", methods={"POST"}, name="public_users_register")
     * @throws Exception
     * @param UserService            $userService
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface        $kernel
     * @param Request                $request
     * @return JsonResponse
     */
    public function register(
        Request $request,
        UserService $userService,
        EntityManagerInterface $entityManager,
        KernelInterface $kernel
    ): JsonResponse {
        $form = $this
            ->createForm(RegisterType::class, $reg = new RegistrationModel())
            ->submit($request->request->all());

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->formErrorResponse($form, $kernel->isDebug());
        }

        $user = $userService->updatePassword(
            $userService->createUser(),
            $reg->getPassword()
        );

        if ($user->getPassword() === null) {
            return $this->messageResponse(
                'cannot perform encryption',
                Response::HTTP_I_AM_A_TEAPOT
            );
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            [
                'username' => $user->getUuid(),
            ]
        );
    }
}
