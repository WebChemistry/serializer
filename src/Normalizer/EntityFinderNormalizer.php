<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

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

		return $this->em->find($type, $data);
	}

	public function supportsDenormalization($data, string $type, string $format = null): bool
	{
		if (!$this->em->getMetadataFactory()->hasMetadataFor($type)) {
			return false;
		}

		if ($data instanceof $type) {
			return true;
		}

		return is_scalar($data) && !str_ends_with($type, '[]');
	}

}
