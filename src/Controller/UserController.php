<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\SerializerAware;
use App\Entity\User;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/users")
 */
class UserController extends AbstractController
{
    use SerializerAware;

    public function __construct(SerializerInterface $serializer)
    {
        $this->setSerializer($serializer);
    }

    /**
     * @Route(path="", methods={"GET"}, name="user_list")
     * @param UserRepository $repository
     * @return Response
     */
    public function getUsers(UserRepository $repository): Response
    {
        $users = $repository->findAll();
        return $this->serializeResponse($users);
    }

    /**
     * @Route(path="/connect", methods={"POST"}, name="public_users_connect")
     * @return Response
     */
    public function connect(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json(
            [
                'id' => $user->getExternalId()
            ]
        );
    }
}
