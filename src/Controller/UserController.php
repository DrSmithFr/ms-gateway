<?php

declare(strict_types = 1);

namespace App\Controller;

use Exception;
use App\Entity\User;
use RuntimeException;
use App\Form\OnlyPasswordType;
use App\Service\UserService;
use App\Service\AccountTransferManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\PasswordModel;
use App\Controller\Traits\SerializerAware;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\LcobucciJWTEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

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
     *
     * @param UserService            $userService
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface        $kernel
     * @param Request                $request
     *
     * @return JsonResponse
     */
    public function register(
        Request $request,
        UserService $userService,
        EntityManagerInterface $entityManager,
        KernelInterface $kernel
    ): JsonResponse {
        $form = $this
            ->createForm(OnlyPasswordType::class, $reg = new PasswordModel())
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

    /**
     * @Route(path="/transfer/request", methods={"POST"}, name="public_users_transfer_request")
     * @throws JWTEncodeFailureException
     *
     * @param AccountTransferManager $transferManager
     * @param KernelInterface        $kernel
     * @param Request                $request
     *
     * @return JsonResponse
     */
    public function transferRequest(
        Request $request,
        AccountTransferManager $transferManager,
        KernelInterface $kernel
    ): JsonResponse {
        $form = $this
            ->createForm(OnlyPasswordType::class, $pass = new PasswordModel())
            ->submit($request->request->all());

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->formErrorResponse($form, $kernel->isDebug());
        }

        /** @var User $user */
        $user  = $this->getUser();
        $token = $transferManager->generateTransferToken($user, $pass);

        return $this->json(
            [
                'token' => $token,
            ]
        );
    }
}
