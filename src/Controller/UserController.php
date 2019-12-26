<?php

declare(strict_types = 1);

namespace App\Controller;

use Exception;
use Crypt_GPG;
use App\Entity\User;
use App\Form\GpgType;
use App\Model\GpgModel;
use App\Form\RecoverType;
use App\Form\TransferType;
use App\Model\RecoverModel;
use Crypt_GPG_KeyGenerator;
use App\Model\TransferModel;
use App\Service\UserService;
use App\Model\PasswordModel;
use App\Form\OnlyPasswordType;
use App\Service\AccountTransferManager;
use JMS\Serializer\SerializerInterface;
use App\Exception\BadPasswordException;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UserNotFoundException;
use App\Controller\Traits\SerializerAware;
use App\Exception\InvalidPayloadException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface        $kernel
     * @param Request                $request
     * @param UserService            $userService
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
     * @Route(path="/secure", methods={"POST"}, name="public_users_secure")
     * @throws Exception
     *
     * @param KernelInterface        $kernel
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function secure(
        Request $request,
        EntityManagerInterface $entityManager,
        KernelInterface $kernel
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->messageResponse(
                'not connected',
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($user->getPublicGpg()) {
            return $this->messageResponse(
                'account already secured',
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        $form = $this
            ->createForm(GpgType::class, $gpg = new GpgModel())
            ->submit($request->request->all());

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->formErrorResponse($form, $kernel->isDebug());
        }

        $user->setPublicGpg($gpg->getKey());
        $entityManager->flush();

        return $this->messageResponse('account secured with GPG');
    }

    /**
     * @Route(path="/transfer", methods={"POST"}, name="private_users_transfer")
     * @throws Exception
     *
     * @param KernelInterface        $kernel
     * @param AccountTransferManager $transferManager
     * @param EntityManagerInterface $manager
     * @param Request                $request
     *
     * @return JsonResponse
     */
    public function transferRequest(
        Request $request,
        KernelInterface $kernel,
        AccountTransferManager $transferManager,
        EntityManagerInterface $manager
    ): JsonResponse {
        $form = $this
            ->createForm(TransferType::class, $model = new TransferModel())
            ->submit($request->request->all());

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->formErrorResponse($form, $kernel->isDebug());
        }

        try {
            $token = $transferManager->generateTransferToken($this->getUser(), $model);
            $manager->flush();

            return $this->json(
                [
                    'token' => $token,
                ]
            );
        } catch (JWTEncodeFailureException|BadPasswordException $e) {
            return $this->messageResponse(
                'transfer invalid: ' . $e->getMessage(),
                Response::HTTP_NOT_ACCEPTABLE
            );
        }
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
            ->createForm(RecoverType::class, $model = new RecoverModel())
            ->submit($request->request->all());

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->formErrorResponse($form, $kernel->isDebug());
        }

        try {
            $user = $transferManager->verifyTransfer($model);

            return new JsonResponse(
                [
                    'uuid' => $user->getUuid(),
                ]
            );
        } catch (BadPasswordException|InvalidPayloadException|UserNotFoundException $e) {
            return $this->messageResponse(
                'recover invalid: ' . $e->getMessage(),
                Response::HTTP_NOT_ACCEPTABLE
            );
        } catch (JWTDecodeFailureException $e) {
            return $this->messageResponse(
                'recover outdated',
                Response::HTTP_REQUEST_TIMEOUT
            );
        }
    }
}
