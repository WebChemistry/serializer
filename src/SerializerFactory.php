<?php declare(strict_types = 1);

namespace WebChemistry\Serializer;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

final class SerializerFactory
{

	/** @var DenormalizerInterface[]|NormalizerInterface[] */
	private array $normalizers = [];

	/** @var EncoderInterface[]|DecoderInterface[] */
	private array $encoders = [];

	public function addNormalizer(DenormalizerInterface|NormalizerInterface $normalizer): void
	{
		$this->normalizers[] = $normalizer;
	}

	public function addEncoder(EncoderInterface|DecoderInterface $encoder): void
	{
		$this->encoders[] = $encoder;
	}

	public function create(): Serializer
	{
		return new SerializerExtras($this->normalizers, $this->encoders);
	}

}
