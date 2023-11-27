<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Tracy;

use ReflectionProperty;
use Symfony\Component\Serializer\Serializer;
use Tracy\Debugger;
use Tracy\Helpers;
use Tracy\IBarPanel;

final class SerializerBar implements IBarPanel
{

	public function __construct(
		private Serializer $serializer,
	)
	{
	}

	public function getTab(): string
	{
		return 'Serializer';
	}

	public function getPanel(): string
	{
		return Helpers::capture(function (): void {
			$reflection = new ReflectionProperty($this->serializer, 'normalizers');
			$reflection->setAccessible(true);
			$classes = [];
			/** @var object[] $normalizers */
			$normalizers = $reflection->getValue($this->serializer);

			foreach ($normalizers as $object) {
				$classes[] = $object::class;
			}

			require __DIR__ . '/templates/panel.phtml';
		});
	}

}
