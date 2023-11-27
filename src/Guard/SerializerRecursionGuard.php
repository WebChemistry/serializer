<?php declare(strict_types = 1);

namespace WebChemistry\Serializer\Guard;

trait SerializerRecursionGuard
{

	/**
	 * @param mixed[] $context
	 */
	protected function setRecursionGuard(array &$context): void
	{
		$context['_recursion.guard'][static::class] = true; // @phpstan-ignore-line
	}

	/**
	 * @param mixed[] $context
	 */
	protected function isRecursion(array $context): bool
	{
		return isset($context['_recursion.guard'][static::class]); // @phpstan-ignore-line
	}

}
