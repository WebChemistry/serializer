<?php declare(strict_types = 1);

namespace WebChemistry\Serializer;

trait SerializerRecursionGuard
{

	protected function setRecursionGuard(array &$context): void
	{
		$context['_recursion.guard'][static::class] = true;
	}

	protected function isRecursion(array $context): bool
	{
		return isset($context['_recursion.guard'][static::class]);
	}

}
