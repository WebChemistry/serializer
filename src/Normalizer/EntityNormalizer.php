<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Utilitte\Doctrine\DoctrineIdentityExtractor;

final class EntityNormalizer implements DenormalizerInterface, NormalizerInterface
{

	public function __construct(
		private EntityManagerInterface $em,
		private DoctrineIdentityExtractor $identityExtractor,
	)
	{
	}

	public function normalize($object, string $format = null, array $context = []): mixed
	{
		return $this->identityExtractor->extractIdentity($object);
	}

	public function supportsNormalization($data, string $format = null): bool
	{
		if (!is_object($data) || !$this->em->getMetadataFactory()->hasMetadataFor(get_class($data))) {
			return false;
		}

		return true;
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
