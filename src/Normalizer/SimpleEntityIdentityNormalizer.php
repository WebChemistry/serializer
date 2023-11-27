<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SimpleEntityIdentityNormalizer implements NormalizerInterface
{

	/** @deprecated */
	public const CALLBACK = 'simple_entity_identity_normalizer_callback';
	public const Callback = 'simple_entity_identity_normalizer_callback';
	public const ClassNames = self::class;

	public function __construct(
		private EntityManagerInterface $em,
	)
	{
	}

	/**
	 * @param mixed $object
	 * @param mixed[] $context
	 */
	public function normalize($object, ?string $format = null, array $context = []): mixed // @phpstan-ignore-line
	{
		assert(is_object($object));

		$identifiers = $this->em->getClassMetadata(ClassUtils::getClass($object))->getIdentifierValues($object);

		if (count($identifiers) <= 1) {
			return reset($identifiers); // @phpstan-ignore-line
		}

		return $identifiers;
	}

	/**
	 * @param mixed[] $context
	 */
	public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
	{
		if (!is_object($data)) {
			return false;
		}

		$callback = $context[self::Callback] ??  null;

		if ($callback && is_callable($callback)) {
			if (!is_callable($callback)) {
				throw new LogicException('Callback is not callable.');
			}

			if($callback($data)) {
				return true;
			}
		}

 		$supports = $context[self::ClassNames] ?? null;

		if (!$supports) {
			return false;
		}

		return in_array(ClassUtils::getClass($data), $supports, true);
	}

}
