<?php declare(strict_types = 1);

namespace WebChemistry\Serializer;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class ObjectNormalizerFactory
{

	private ClassMetadataFactoryInterface $classMetadataFactory;

	private ?NameConverterInterface $nameConverter = null;

	private PropertyAccessorInterface $propertyAccessor;

	private PropertyInfoExtractorInterface $propertyInfoExtractor;

	private ?ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null;

	/** @var callable|null */
	private $objectClassResolver = null;

	/** @var mixed[] */
	private array $defaultContext = [];

	public function __construct(
		private Reader $reader,
		?PropertyAccessorInterface $propertyAccessor = null,
		?PropertyInfoExtractorInterface $propertyInfoExtractor = null,
	)
	{
		$this->propertyInfoExtractor = $propertyInfoExtractor ?? $this->createPropertyInfoExtractor();
		$this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
	}

	/**
	 * @param mixed[] $defaultContext
	 */
	public function setDefaultContext(array $defaultContext): void
	{
		$this->defaultContext = $defaultContext;
	}

	public function setClassDiscriminatorResolver(
		?ClassDiscriminatorResolverInterface $classDiscriminatorResolver
	): void
	{
		$this->classDiscriminatorResolver = $classDiscriminatorResolver;
	}

	public function setClassMetadataFactory(ClassMetadataFactoryInterface $classMetadataFactory): void
	{
		$this->classMetadataFactory = $classMetadataFactory;
	}

	public function setNameConverter(?NameConverterInterface $nameConverter): void
	{
		$this->nameConverter = $nameConverter;
	}

	public function setPropertyInfoExtractor(PropertyInfoExtractorInterface $propertyInfoExtractor): void
	{
		$this->propertyInfoExtractor = $propertyInfoExtractor;
	}

	public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor): void
	{
		$this->propertyAccessor = $propertyAccessor;
	}

	public function create(): ObjectNormalizer
	{
		return new ObjectNormalizer(
			$this->classMetadataFactory ?? new ClassMetadataFactory(new AnnotationLoader($this->reader)),
			$this->nameConverter,
			$this->propertyAccessor,
			$this->propertyInfoExtractor,
			$this->classDiscriminatorResolver,
			$this->objectClassResolver,
			$this->defaultContext,
		);
	}

	private function createPropertyInfoExtractor(): PropertyInfoExtractorInterface
	{
		$phpDocExtractor = new PhpDocExtractor();
		$reflectionExtractor = new ReflectionExtractor();

		return new PropertyInfoExtractor(
			[$reflectionExtractor],
			[$phpDocExtractor, $reflectionExtractor],
			[$phpDocExtractor],
			[$reflectionExtractor],
			[$reflectionExtractor]
		);
	}

}
