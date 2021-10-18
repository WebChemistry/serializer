<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Utilitte\Doctrine\DoctrineIdentityExtractor;
use WebChemistry\Serializer\Guard\SerializerRecursionGuard;

#[Deprecated('Use SimpleEntityIdentity instead')]
final class EntityNormalizer implements NormalizerAwareInterface, ContextAwareNormalizerInterface
{

	use NormalizerAwareTrait;
	use SerializerRecursionGuard;

	public function __construct(
		private EntityManagerInterface $em,
		private DoctrineIdentityExtractor $identityExtractor,
	)
	{
	}

	public function normalize($object, string $format = null, array $context = []): mixed
	{
		$this->setRecursionGuard($context);

		$skip = false;
		if (($context['rootEntity'] ?? null) === $object) {
			$skip = true;
		} elseif (isset($context[self::class])) {
			if (is_array($context[self::class])) {
				if (($context[self::class][get_class($object)]) ?? true === false) {
					$skip = true;
				}
			} else if ($context[self::class] === false) {
				$skip = true;
			}
		}

		if ($skip) {
			return $this->normalizer->normalize($object, $format, $context);
		}

		return $this->identityExtractor->extractIdentity($object);
	}

	public function supportsNormalization($data, string $format = null, array $context = []): bool
	{
		if ($this->isRecursion($context)) {
			return false;
		}

		if (!is_object($data) || !$this->em->getMetadataFactory()->hasMetadataFor(get_class($data))) {
			return false;
		}

		return true;
	}

}
