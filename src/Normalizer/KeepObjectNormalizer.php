<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class KeepObjectNormalizer implements DenormalizerInterface
{

	/**
	 * @param mixed $data
	 * @param mixed[] $context
	 */
	public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
	{
		return $data;
	}

	public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
	{
		return $data instanceof $type;
	}

}
