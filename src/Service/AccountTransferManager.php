<?php

declare(strict_types = 1);

namespace App\Service;

use DateTime;
use Exception;
use App\Entity\User;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use App\Model\PasswordModel;
use App\Model\TransferModel;
use App\Repository\UserRepository;
use App\Exception\UserNotFoundException;
use App\Exception\TransferPayloadException;
use App\Exception\TransferPasswordException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

class AccountTransferManager
{
    private const TTL_IN_SECONDS  = 10*60;
    private const PAYLOAD_ACCOUNT = 'account';

    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    /**
     * @var EncoderFactoryInterface|null
     */
    private $encoderFactory;

    /**
     * @var UserRepository|null
     */
    private $userRepository;

    /**
     * AccountTransferManager constructor.
     *
     * @param JWTEncoderInterface     $encoder
     * @param EncoderFactoryInterface $encoderFactory
     * @param UserRepository          $userRepository
     */
    public function __construct(
        JWTEncoderInterface $encoder,
        EncoderFactoryInterface $encoderFactory,
        UserRepository $userRepository
    ) {
        $this->encoder        = $encoder;
        $this->encoderFactory = $encoderFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws JWTEncodeFailureException
     * @throws Exception
     *
     * @param PasswordModel $form
     * @param User          $user
     *
     * @return string
     */
    public function generateTransferToken(User $user, PasswordModel $form): string
    {
        $encoder  = $this->encoderFactory->getEncoder($user);
        $password = $encoder->encodePassword($form->getPassword(), $user->getSalt());

        $user
            ->setTransferUuid(Uuid::uuid4())
            ->setTransferPassword($password);

        $payload = [
            'exp'                 => self::TTL_IN_SECONDS,
            self::PAYLOAD_ACCOUNT => $user->getTransferUuid()->toString(),
        ];

        return $this->encoder->encode($payload);
    }

    /**
     * @throws JWTDecodeFailureException
     *
     * @param string $token
     *
     * @return array
     */
    public function verifyTransferToken(string $token): array
    {
        return $this->encoder->decode($token);
    }

    /**
     * @throws JWTDecodeFailureException
     * @throws TransferPayloadException
     * @throws UserNotFoundException
     * @throws TransferPasswordException
     *
     * @param TransferModel $model
     *
     * @return User
     */
    public function verifyTransfer(TransferModel $model): User
    {
        $payload = $this->encoder->decode($model->getToken());

        if (!$uuid = $payload[self::PAYLOAD_ACCOUNT] ?? null) {
            throw new TransferPayloadException('bad payload format', 1);
        }

        /** @var User $user */
        if (!$user = $this->userRepository->findOneBy(['transferUuid' => $uuid])) {
            throw new UserNotFoundException();
        }

        $encoder  = $this->encoderFactory->getEncoder($user);
        $password = $encoder->encodePassword($model->getPassword(), $user->getSalt());

        if ($password !== $user->getTransferPassword()) {
            throw new TransferPasswordException();
        }

        return $user;
    }
}
