<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Context;

interface DenormalizerContextBuilderInterface
{

	public function buildDenormalizerContext(mixed $data, string $type, array $context): array;

}
