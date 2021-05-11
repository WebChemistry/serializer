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

	public function denormalize($data, string $type, string $format = null, array $context = [])
	{
		return $data;
	}

	public function supportsDenormalization($data, string $type, string $format = null)
	{
		return $data instanceof $type;
	}

}
