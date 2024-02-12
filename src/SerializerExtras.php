<?php declare(strict_types = 1);

namespace WebChemistry\Serializer;

use Symfony\Component\Serializer\Serializer;

final class SerializerExtras extends Serializer
{

	public const RootData = 'rootData';

	/**
	 * @param mixed[] $context
	 * @return mixed[]|string|int|float|bool|\ArrayObject|null
	 */
	public function normalize(
		mixed $data,
		string $format = null,
		array $context = []
	): array|string|int|float|bool|\ArrayObject|null
	{
		$context[self::RootData] = $data;

		return parent::normalize($data, $format, $context);
	}

}
