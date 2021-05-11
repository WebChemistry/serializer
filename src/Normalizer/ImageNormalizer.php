<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Normalizer;

use Contributte\Imagist\Bridge\Nette\Form\Entity\UploadControlEntity;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\Transaction\TransactionFactoryInterface;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use WebChemistry\Serializer\Context\DenormalizerContextBuilderInterface;

final class ImageNormalizer implements NormalizerInterface, DenormalizerInterface, DenormalizerContextBuilderInterface
{

	public const IMAGE_AS_OBJECT = 'webchemistry.image.imageAsObject';
	public const INSTANT_PERSIST = 'webchemistry.image.instantPersist';
	public const TRANSACTION_OBJECT = 'webchemistry.image.transactionObject';

	public function __construct(
		private TransactionFactoryInterface $transactionFactory,
		private ImageStorageInterface $imageStorage,
	)
	{
	}

	public function normalize($object, string $format = null, array $context = []): object|string|null
	{
		assert($object instanceof ImageInterface);

		if (($context[self::IMAGE_AS_OBJECT] ?? false) === true) {
			return $object;
		}

		return $object->isEmpty() ? null : $object->getId();
	}

	public function supportsNormalization($data, string $format = null): bool
	{
		return $data instanceof ImageInterface;
	}

	public function denormalize($data, string $type, string $format = null, array $context = []): ?PersistentImageInterface
	{
		assert($data instanceof UploadControlEntity);

		if (($context[self::INSTANT_PERSIST] ?? false) === true) {
			return $data->resolve($this->imageStorage);
		}

		return $data->resolve(
			$context[self::TRANSACTION_OBJECT] ?? $this->transactionFactory->create()
		);
	}

	public function supportsDenormalization($data, string $type, string $format = null): bool
	{
		if ($type !== PersistentImageInterface::class) {
			return false;
		}

		return $data instanceof UploadControlEntity;
	}

	public function buildDenormalizerContext(mixed $data, string $type, array $context): array
	{
		$context[self::TRANSACTION_OBJECT] = $this->transactionFactory->create();
		
		return $context;
	}

}
