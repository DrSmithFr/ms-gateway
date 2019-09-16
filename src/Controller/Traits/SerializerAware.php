<?php

namespace App\Controller\Traits;

use Symfony\Component\Form\FormInterface;
use App\Entity\Interfaces\SerializableEntity;
use InvalidArgumentException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

trait SerializerAware
{
    /**
     * @var SerializerInterface|null
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     * @return $this
     */
    private function setSerializer(SerializerInterface $serializer): self
    {
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * Create serialization context for specifics groups
     * with serialize null field enable
     *
     * @param array $group
     * @return SerializationContext
     */
    private function getSerializationContext(array $group = ['Default']): SerializationContext
    {
        $context = SerializationContext::create();
        $context->setSerializeNull(true);
        $context->setGroups($group);
        return $context;
    }

    /**
     * @return SerializerInterface
     */
    private function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * Return the json string of the data, serialize for specifics groups
     *
     * @param SerializableEntity $data
     * @param array              $group
     * @return string
     */
    private function serialize($data, array $group = ['Default']): string
    {
        return $this
            ->getSerializer()
            ->serialize(
                $data,
                'json',
                $this->getSerializationContext($group)
            );
    }

    /**
     * Return the JsonResponse of the data, serialize for specifics groups
     *
     * @param mixed $data
     * @param array $group
     * @return JsonResponse
     */
    private function serializeResponse($data, array $group = ['Default']): JsonResponse
    {
        $response = new JsonResponse([], JsonResponse::HTTP_OK);
        $json     = $this->serialize($data, $group);
        return $response->setJson($json);
    }

    /**
     * Simple JsonResponse use to transmit a message
     *
     * @param string $message
     * @param int    $code
     * @return JsonResponse
     */
    private function messageResponse(string $message, int $code = JsonResponse::HTTP_OK): JsonResponse
    {
        $response = new JsonResponse(
            [
                'code'    => $code,
                'message' => $message,
            ],
            $code
        );

        return $response;
    }

    /**
     * Simple JsonResponse use to transmit a message
     *
     * @param FormInterface $form
     * @param bool          $showReason
     * @return JsonResponse
     */
    private function formErrorResponse(FormInterface $form, bool $showReason = true): JsonResponse
    {
        return new JsonResponse(
            [
                'code'    => Response::HTTP_NOT_ACCEPTABLE,
                'message' => 'Invalid form',
                'reason'  => $showReason ? $this->getFormErrorArray($form) : 'hidden',
            ],
            Response::HTTP_NOT_ACCEPTABLE
        );
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getFormErrorArray(FormInterface $form): array
    {
        $errors = [];

        // Global
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][$error->getOrigin()->getName()][] = $error->getMessage();
        }

        // Fields
        foreach ($form as $child) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$form->getName()][$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }

    /**
     * Simple JsonResponse use to transmit the new id of the created entity
     *
     * @param mixed  $entity
     * @param string $message
     * @return JsonResponse
     */
    private function createResponse(SerializableEntity $entity, string $message): JsonResponse
    {
        if (!method_exists($entity, 'getId')) {
            throw new InvalidArgumentException('Entity must have a getId() method');
        }

        return new JsonResponse(
            [
                'code'    => JsonResponse::HTTP_ACCEPTED,
                'message' => $message,
                'id'      => $entity->getIdentifier(),
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }
}
