<?php

declare(strict_types = 1);

namespace App\Service;

use DateTime;
use Exception;
use App\Entity\User;
use Ramsey\Uuid\Uuid;
use App\Model\PasswordModel;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

class AccountTransferManager
{
    const TTL_IN_SECONDS = 10*60;

    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    /**
     * @var EncoderFactoryInterface|null
     */
    private $encoderFactory;

    /**
     * AccountTransferManager constructor.
     *
     * @param JWTEncoderInterface     $encoder
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        JWTEncoderInterface $encoder,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->encoder        = $encoder;
        $this->encoderFactory = $encoderFactory;
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
            ->setTransferPassword($password)
            ->setRequestTransferAt(new DateTime());

        $payload = [
            'exp'     => self::TTL_IN_SECONDS,
            'account' => $user->getTransferUuid()->toString(),
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
}
