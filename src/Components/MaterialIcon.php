<?php

namespace NotificationChannels\GoogleChat\Components;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

class MaterialIcon implements Arrayable
{
    protected ?bool $fill = null;

    protected ?int $weight = null;

    protected ?int $grade = null;

    public function __construct(protected string $name) {}

    public static function create(string $name): static
    {
        return new static($name);
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Render the icon with filled paths.
     */
    public function fill(bool $fill = true): static
    {
        $this->fill = $fill;

        return $this;
    }

    /**
     * Set the icon stroke weight.
     */
    public function weight(int $weight): static
    {
        if (! in_array($weight, [100, 200, 300, 400, 500, 600, 700], true)) {
            throw new InvalidArgumentException('Material icon weight must be between 100 and 700 in increments of 100.');
        }

        $this->weight = $weight;

        return $this;
    }

    /**
     * Set the icon optical grade.
     */
    public function grade(int $grade): static
    {
        if (! in_array($grade, [-25, 0, 200], true)) {
            throw new InvalidArgumentException('Material icon grade must be -25, 0, or 200.');
        }

        $this->grade = $grade;

        return $this;
    }

    public function toArray(): array
    {
        $materialIcon = ['name' => $this->name];

        if ($this->fill !== null) {
            $materialIcon['fill'] = $this->fill;
        }

        if ($this->weight !== null) {
            $materialIcon['weight'] = $this->weight;
        }

        if ($this->grade !== null) {
            $materialIcon['grade'] = $this->grade;
        }

        return ['materialIcon' => $materialIcon];
    }
}
