<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Tracy;

use ReflectionProperty;
use Symfony\Component\Serializer\Serializer;
use Tracy\Debugger;
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

	public function getPanel()
	{
		$reflection = new ReflectionProperty($this->serializer, 'normalizers');
		$reflection->setAccessible(true);
		$classes = [];

		foreach ($reflection->getValue($this->serializer) as $object) {
			$classes[] = $object::class;
		}

		ob_start();

		require __DIR__ . '/templates/panel.phtml';

		return ob_get_clean();
	}

}
