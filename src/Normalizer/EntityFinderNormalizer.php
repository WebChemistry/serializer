<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class EntityFinderNormalizer implements DenormalizerInterface
{

	public function __construct(
		private EntityManagerInterface $em,
	)
	{
	}

	public function denormalize($data, string $type, string $format = null, array $context = []): ?object
	{
		if (is_object($data)) {
			return $data;
		}
		
		if ($data === '') {
			return null;
		}

		return $this->em->find($type, $data);
	}

	public function supportsDenormalization($data, string $type, string $format = null): bool
	{
		if (str_ends_with($type, '[]')) {
			return false;
		}

		$type = ClassUtils::getRealClass($type);
		if ($this->em->getMetadataFactory()->isTransient($type)) {
			return false;
		}

		if ($data instanceof $type) {
			return true;
		}

		return is_scalar($data);
	}

}
