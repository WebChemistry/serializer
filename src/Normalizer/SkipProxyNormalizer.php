<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Persistence\Proxy;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SkipProxyNormalizer implements NormalizerInterface
{

	public function __construct(
		private EntityManagerInterface $em,
	)
	{
	}

	/**
	 * @param mixed[] $context
	 */
	public function normalize(mixed $object, string $format = null, array $context = [])
	{
		assert(is_object($object));

		if ($object instanceof PersistentCollection) {
			return [];
		}

		$identifiers = $this->em->getClassMetadata(ClassUtils::getClass($object))->getIdentifierValues($object);

		if (count($identifiers) <= 1) {
			return reset($identifiers); // @phpstan-ignore-line
		}

		return $identifiers;
	}

	public function supportsNormalization(mixed $data, string $format = null)
	{
		if (!is_object($data)) {
			return false;
		}

		if ($data instanceof Proxy) {
			return true;
		}

		if ($data instanceof PersistentCollection) {
			if (!$data->isInitialized()) {
				return true;
			}
		}

		return false;
	}

}
