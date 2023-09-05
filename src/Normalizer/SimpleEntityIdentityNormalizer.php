<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\Proxy;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Utilitte\Doctrine\DoctrineIdentityExtractor;

final class SimpleEntityIdentityNormalizer implements NormalizerInterface
{

	public const CALLBACK = 'simple_entity_identity_normalizer_callback';

	public function __construct(
		private EntityManagerInterface $em,
		private DoctrineIdentityExtractor $identityExtractor,
	)
	{
	}

	/**
	 * @param mixed $object
	 * @param mixed[] $context
	 */
	public function normalize($object, ?string $format = null, array $context = [])
	{
		return $this->identityExtractor->extractIdentity($object);
	}

	public function supportsNormalization($data, string $format = null, array $context = [])
	{
		if (!is_object($data)) {
			return false;
		}

		$callback = $context[self::CALLBACK] ??  null;

		if ($callback && $callback($data)) {
			return true;
		}

 		$supports = $context[self::class] ?? null;

		if (!$supports) {
			return false;
		}

		if ($data instanceof Proxy) {
			$className = get_parent_class($data);
		} elseif ($this->em->getMetadataFactory()->hasMetadataFor($data::class)) {
			$className = $data::class;
		} else {
			return false;
		}

		return in_array($className, $supports, true);
	}

}
