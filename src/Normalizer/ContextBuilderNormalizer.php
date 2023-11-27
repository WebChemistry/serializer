<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use WebChemistry\Serializer\Context\DenormalizerContextBuilderInterface;
use WebChemistry\Serializer\Context\NormalizerContextBuilderInterface;
use WebChemistry\Serializer\Guard\SerializerRecursionGuard;

final class ContextBuilderNormalizer implements NormalizerInterface, NormalizerAwareInterface, DenormalizerInterface, DenormalizerAwareInterface
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

	/**
	 * @param mixed[] $context
	 */
	public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
	{
		foreach ($this->denormalizerContextBuilders as $builder) {
			$context = $builder->buildDenormalizerContext($data, $type, $context);
		}

		$this->setRecursionGuard($context);

		return $this->denormalizer->denormalize($data, $type, $format, $context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
	{
		return !$this->isRecursion($context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function normalize(mixed $object, string $format = null, array $context = []): mixed // @phpstan-ignore-line
	{
		foreach ($this->normalizerContextBuilders as $builder) {
			$context = $builder->buildNormalizerContext($context);
		}

		$this->setRecursionGuard($context);

		return $this->normalizer->normalize($object, $format, $context);
	}

	/**
	 * @param mixed[] $context
	 */
	public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
	{
		return !$this->isRecursion($context);
	}

}
