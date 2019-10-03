<?php

declare(strict_types = 1);

namespace App\Controller;

use Exception;
use App\Entity\User;
use RuntimeException;
use App\Form\TransferType;
use App\Model\TransferModel;
use App\Form\OnlyPasswordType;
use App\Service\UserService;
use App\Service\AccountTransferManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\PasswordModel;
use App\Exception\UserNotFoundException;
use App\Controller\Traits\SerializerAware;
use JMS\Serializer\SerializerInterface;
use App\Exception\TransferPayloadException;
use App\Exception\TransferPasswordException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route(path="/transfer", methods={"POST"}, name="private_users_transfer")
     * @throws JWTEncodeFailureException
     *
     * @param KernelInterface        $kernel
     * @param AccountTransferManager $transferManager
     * @param Request                $request
     *
     * @return JsonResponse
     */
    public function transferRequest(
        Request $request,
        KernelInterface $kernel,
        AccountTransferManager $transferManager
    ): JsonResponse {
        $form = $this
            ->createForm(OnlyPasswordType::class, $model = new PasswordModel())
            ->submit($request->request->all());

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->formErrorResponse($form, $kernel->isDebug());
        }

        /** @var User $user */
        $user  = $this->getUser();
        $token = $transferManager->generateTransferToken($user, $model);

        return $this->json(
            [
                'token' => $token,
            ]
        );
    }

    /**
     * @Route(path="/recover", methods={"POST"}, name="private_users_recover")
     * @param Request                $request
     * @param KernelInterface        $kernel
     * @param AccountTransferManager $transferManager
     *
     * @return JsonResponse
     */
    public function transfer(
        Request $request,
        KernelInterface $kernel,
        AccountTransferManager $transferManager
    ): JsonResponse {
        $form = $this
            ->createForm(TransferType::class, $model = new TransferModel())
            ->submit($request->request->all());

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->formErrorResponse($form, $kernel->isDebug());
        }

        try {
            $user = $transferManager->verifyTransfer($model);

            return new JsonResponse(
                [
                    'uuid' => $user->getUuid()
                ]
            );
        } catch (TransferPasswordException|TransferPayloadException|UserNotFoundException $e) {
            return $this->messageResponse(
                'transfer invalid',
                Response::HTTP_NOT_ACCEPTABLE
            );
        } catch (JWTDecodeFailureException $e) {
            return $this->messageResponse(
                'transfer token outdated',
                Response::HTTP_REQUEST_TIMEOUT
            );
        }
    }
}
