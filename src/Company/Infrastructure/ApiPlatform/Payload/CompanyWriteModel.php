<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\ApiPlatform\Payload;

use Symfony\Component\Validator\Constraints as Assert;
use Webmozart\Assert\Assert as Assertion;

final class CompanyWriteModel implements \JsonSerializable
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $group = '';

    #[Assert\PositiveOrZero]
    public int $ranking = 1;

    private function __construct(string $name, string $group, int $ranking)
    {
        $this->name = $name;
        $this->group = $group;
        $this->ranking = $ranking;
    }

    public static function fromArray($array): CompanyWriteModel
    {
        Assertion::keyExists($array, 'name');
        Assertion::string($array['name']);

        Assertion::keyExists($array, 'group');
        Assertion::string($array['group']);

        Assertion::keyExists($array, 'ranking');
        Assertion::integer($array['ranking']);

        return new self(
            $array['name'],
            $array['group'],
            $array['ranking'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
           'name' => $this->name,
           'group' => $this->group,
           'ranking' => $this->ranking,
        ];
    }
}
