<?php

namespace App\Form\DataTransformer;

use App\Entity\Model;
use App\Repository\ModelRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ModelToIdTransformer implements DataTransformerInterface
{
    public function __construct(private ModelRepository $modelRepository) {}

    public function transform(mixed $value): mixed
    {
        if (!$value instanceof Model) {
            return '';
        }

        return $value->getId();
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (!$value) {
            return null;
        }

        $model = $this->modelRepository->find($value);

        if (!$model) {
            throw new TransformationFailedException(sprintf('Le modèle avec ID "%s" n\'existe pas.', $value));
        }

        return $model;
    }
}
