<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\TransferModel;
use DateTime;
use Exception;
use App\Entity\User;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use App\Model\PasswordModel;
use App\Model\RecoverModel;
use App\Repository\UserRepository;
use App\Exception\UserNotFoundException;
use App\Exception\InvalidPayloadException;
use App\Exception\BadPasswordException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

class AccountTransferManager
{
    private const TTL_IN_SECONDS = 10 * 60 * 1000;
    private const PAYLOAD_ACCOUNT = 'account';
    private const PAYLOAD_SALT = 'salt';

    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * AccountTransferManager constructor.
     *
     * @param JWTEncoderInterface $encoder
     * @param EncoderFactoryInterface $encoderFactory
     * @param UserRepository $userRepository
     * @param UserService $userService
     */
    public function __construct(
        JWTEncoderInterface $encoder,
        EncoderFactoryInterface $encoderFactory,
        UserRepository $userRepository,
        UserService $userService
    )
    {
        $this->encoder = $encoder;
        $this->encoderFactory = $encoderFactory;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * @param TransferModel $form
     * @param User $user
     *
     * @return string
     * @throws Exception
     *
     * @throws JWTEncodeFailureException
     * @throws BadPasswordException
     */
    public function generateTransferToken(User $user, TransferModel $form): string
    {
        $encoder = $this->encoderFactory->getEncoder($user);

        if (!$this->userService->checkPassword($user, $form->getPassword())) {
            throw new BadPasswordException();
        }

        $salt = $this->userService->generateSalt();
        $passphrase = $encoder->encodePassword($form->getPassword(), $salt . $user->getSalt());

        $user
            ->setTransferUuid(Uuid::uuid4())
            ->setTransferPassword($passphrase);

        $payload = [
            'exp' => time() + self::TTL_IN_SECONDS,
            self::PAYLOAD_SALT => $salt,
            self::PAYLOAD_ACCOUNT => $user->getTransferUuid()->toString(),
        ];

        return $this->encoder->encode($payload);
    }

    /**
     * @param RecoverModel $model
     *
     * @return User
     * @throws UserNotFoundException
     * @throws BadPasswordException
     *
     * @throws JWTDecodeFailureException
     * @throws InvalidPayloadException
     */
    public function verifyTransfer(RecoverModel $model): User
    {
        $payload = $this->encoder->decode($model->getToken());

        if (!$uuid = $payload[self::PAYLOAD_ACCOUNT] ?? null) {
            throw new InvalidPayloadException('bad payload format', 1);
        }

        if (!$salt = $payload[self::PAYLOAD_SALT] ?? null) {
            throw new InvalidPayloadException('bad payload format', 1);
        }

        /** @var User $user */
        if (!$user = $this->userRepository->findOneBy(['transferUuid' => $uuid])) {
            throw new UserNotFoundException();
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        $password = $encoder->encodePassword($model->getPassphrase(), $salt . $user->getSalt());

        if ($password !== $user->getTransferPassword()) {
            throw new BadPasswordException();
        }

        return $user;
    }
}
