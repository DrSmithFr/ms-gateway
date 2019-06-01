<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\SerializerAware;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/users")
 */
class UserController
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
}
