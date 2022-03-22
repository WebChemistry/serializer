<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\DI;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Definition;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Tracy\Bar;
use Utilitte\Doctrine\DoctrineIdentityExtractor;
use WebChemistry\Serializer\Normalizer\ContextBuilderNormalizer;
use WebChemistry\Serializer\Normalizer\EntityFinderNormalizer;
use WebChemistry\Serializer\Normalizer\EntityNormalizer;
use WebChemistry\Serializer\ObjectNormalizerFactory;
use WebChemistry\Serializer\SerializerFactory;
use WebChemistry\Serializer\Tracy\SerializerBar;

final class SerializerExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'normalizers' => Expect::arrayOf(
				Expect::anyOf(Expect::string())
			)
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		// factories

		$builder->addDefinition($this->prefix('normalizers.objectFactory'))
			->setAutowired(false)
			->setFactory(ObjectNormalizerFactory::class);

		$builder->addDefinition($this->prefix('serializerFactory'))
			->setAutowired(false)
			->setFactory(SerializerFactory::class);

		// normalizers

		$builder->addDefinition($this->prefix('normalizers.object'))
			->setFactory($this->prefix('@normalizers.objectFactory') . '::create');

		$builder->addDefinition($this->prefix('normalizers.contextBuilder'))
			->setFactory(ContextBuilderNormalizer::class);

		$builder->addDefinition($this->prefix('normalizers.entity'))
			->setFactory(EntityNormalizer::class);

		$builder->addDefinition($this->prefix('normalizers.entityFinder'))
			->setFactory(EntityFinderNormalizer::class);

		$builder->addDefinition($this->prefix('tracy.bar'))
			->setFactory(SerializerBar::class);

		// serializer

		$builder->addDefinition($this->prefix('serializer'))
			->setFactory($this->prefix('@serializerFactory') . '::create');

		$this->getInitialization()->addBody('$this->getService("serializer.serializer");');
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$factory = $builder->getDefinition($this->prefix('serializerFactory'));
		assert($factory instanceof ServiceDefinition);

		foreach ($this->getNormalizers($config->normalizers) as $normalizer) {
			$factory->addSetup('addNormalizer', [$normalizer]);
		}

		$service = $builder->getByType(Bar::class);
		if ($service) {
			$this->initialization->addBody('$this->getService(?)->addPanel($this->getService(?));', [$service, $this->prefix('tracy.bar')]);
		}
	}

	/**
	 * @param mixed[] $normalizers
	 * @return Definition[]
	 */
	private function getNormalizers(array $normalizers): array
	{
		$builder = $this->getContainerBuilder();
		$services = [];

		foreach ($normalizers as $i => $normalizer) {
			if (is_string($normalizer)) {
				if (str_starts_with($normalizer, '@')) {
					if (!str_contains($normalizer, '\\')) {
						$services[] = $service = $builder->getDefinition(substr($normalizer, 1));
					} else {
						$services[] = $service = $builder->getDefinitionByType(substr($normalizer, 1));
					}

					$service->addTag('serializer.autowire', false);
				} elseif (str_ends_with($normalizer, '[]')) {
					foreach ($builder->findByType(substr($normalizer, 0, -2)) as $service) {
						if ($service->getTag('serializer.autowire') === false) {
							continue;
						}

						$services[] = $service;
					}
				} else {
					if ($builder->getByType($normalizer)) {
						throw new LogicException(sprintf('Normalizer %s is already registered.', $normalizer));
					}

					$services[] = $builder->addDefinition($this->prefix('normalizers.dynamic.' . $i))
						->addTag('serializer.autowire', false)
						->setFactory($normalizer);
				}
			} else {
				throw new LogicException(
					sprintf('Normalizer of type %s not supported yet', get_debug_type($normalizer))
				);
			}
		}

		return $services;
	}

}
