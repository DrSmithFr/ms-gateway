<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use App\Entity\Overview;
use App\Form\OnlyPasswordType;
use App\Form\OverviewType;
use App\Service\UserService;
use App\Repository\OverviewRepository;
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

/**
 * @Route(path="/overviews")
 */
class OverviewController extends AbstractController
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
     * @Route(path="", methods={"GET"}, name="user_overviews_list")
     * @param OverviewRepository $repository
     * @return JsonResponse
     */
    public function all(OverviewRepository $repository): JsonResponse
    {
        $list = $repository->findAllForUser($this->getUser());
        return $this->serializeResponse($list);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="user_overviews_one")
     * @param Overview $overview
     * @return JsonResponse
     */
    public function one(Overview $overview): JsonResponse
    {
        if ($overview->getUser() !== $this->getUser()) {
            return $this->messageResponse('access denied', Response::HTTP_FORBIDDEN);
        }

        return $this->serializeResponse($overview);
    }

    /**
     * @Route(path="", methods={"POST"}, name="user_overviews_create")
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    public function create(
        Request $request,
        EntityManagerInterface $manager
    ): JsonResponse {
        return $this->update(
            (new Overview())->setUser($this->getUser()),
            $request,
            $manager
        );
    }

    /**
     * @Route(path="/{id}", methods={"PUT", "PATCH"}, name="user_overviews_update")
     * @param Overview               $overview
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    public function update(
        Overview $overview,
        Request $request,
        EntityManagerInterface $manager
    ): JsonResponse {
        if ($overview->getUser() !== $this->getUser()) {
            return $this->messageResponse('access denied', Response::HTTP_FORBIDDEN);
        }

        $form = $this
            ->createForm(OverviewType::class, $overview)
            ->submit($request->request->all());

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->formErrorResponse($form);
        }

        // allow creation
        if ($overview->getId() === null) {
            $manager->persist($overview);
        }

        $manager->flush();

        return $this->serializeResponse($overview);
    }
}
