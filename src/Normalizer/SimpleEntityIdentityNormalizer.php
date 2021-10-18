<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Utilitte\Doctrine\DoctrineIdentityExtractor;

final class SimpleEntityIdentityNormalizer implements ContextAwareNormalizerInterface
{

	public function __construct(
		private EntityManagerInterface $em,
		private DoctrineIdentityExtractor $identityExtractor,
	)
	{
	}

	public function normalize($object, string $format = null, array $context = [])
	{
		return $this->identityExtractor->extractIdentity($object);
	}

	public function supportsNormalization($data, string $format = null, array $context = [])
	{
		if (!is_object($data)) {
			return false;
		}

 		$supports = $context[self::class] ?? null;
		if (!$supports) {
			return false;
		}

		$className = $data::class;
		$metadataFactory = $this->em->getMetadataFactory();
		if ($metadataFactory->isTransient($className)) {
			$className = get_parent_class($data);
		}
		if (!$metadataFactory->hasMetadataFor($className)) {
			return false;
		}

		return in_array($className, $supports, true);
	}

}
