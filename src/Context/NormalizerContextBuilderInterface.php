<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Context;

interface NormalizerContextBuilderInterface
{

	public function buildNormalizerContext(array $context): array;

}
