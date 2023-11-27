<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Context;

interface DenormalizerContextBuilderInterface
{

	/**
	 * @param mixed[] $context
	 * @return mixed[]
	 */
	public function buildDenormalizerContext(mixed $data, string $type, array $context): array;

}
