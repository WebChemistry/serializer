<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class EntityFinderNormalizer implements DenormalizerInterface
{

	public const Enabled = self::class . '::enabled';
	public const ClassNames = self::class . '::classNames';

	public function __construct(
		private EntityManagerInterface $em,
	)
	{
	}

	/**
	 * @param class-string $type
	 * @param mixed[] $context
	 */
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

	/**
	 * @param class-string $type
	 * @param mixed[] $context
	 */
	public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
	{
		if (str_ends_with($type, '[]')) {
			return false;
		}

		if (($context[self::Enabled] ?? true) === false) {
			return false;
		}

		$type = ClassUtils::getRealClass($type);

		if (isset($context[self::ClassNames])) {
			return in_array($type, $context[self::ClassNames], true);
		}

		if ($this->em->getMetadataFactory()->isTransient($type)) {
			return false;
		}

		if ($data instanceof $type) {
			return true;
		}

		return is_scalar($data);
	}

}
