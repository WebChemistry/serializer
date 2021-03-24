<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use WebChemistry\Serializer\Context\DenormalizerContextBuilderInterface;
use WebChemistry\Serializer\Context\NormalizerContextBuilderInterface;
use WebChemistry\Serializer\Guard\SerializerRecursionGuard;

final class ContextBuilderNormalizer
	implements NormalizerInterface, NormalizerAwareInterface, ContextAwareNormalizerInterface, DenormalizerInterface,
	ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{

	use SerializerRecursionGuard;
	use DenormalizerAwareTrait;
	use NormalizerAwareTrait;

	/**
	 * @param NormalizerContextBuilderInterface[] $normalizerContextBuilders
	 * @param DenormalizerContextBuilderInterface[] $denormalizerContextBuilders
	 */
	public function __construct(
		private array $normalizerContextBuilders,
		private array $denormalizerContextBuilders,
	)
	{
	}

	public function denormalize($data, string $type, string $format = null, array $context = [])
	{
		foreach ($this->denormalizerContextBuilders as $builder) {
			$context = $builder->buildDenormalizerContext($data, $type, $context);
		}

		$this->setRecursionGuard($context);

		return $this->denormalizer->denormalize($data, $type, $format, $context);
	}

	public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
	{
		return !$this->isRecursion($context);
	}

	public function normalize($object, string $format = null, array $context = [])
	{
		foreach ($this->normalizerContextBuilders as $builder) {
			$context = $builder->buildNormalizerContext($context);
		}

		$this->setRecursionGuard($context);

		return $this->normalize($object, $format, $context);
	}

	public function supportsNormalization($data, string $format = null, array $context = [])
	{
		return !$this->isRecursion($context);
	}

}
