<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Context;

interface NormalizerContextBuilderInterface
{

	/**
	 * @param mixed[] $context
	 * @return mixed[]
	 */
	public function buildNormalizerContext(array $context): array;

}
