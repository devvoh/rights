<?php declare(strict_types=1);

namespace Parable\Rights;

class Rights
{
    /**
     * @var int[]
     */
    protected $rights = [];

    public function add(string ...$names): void
    {
        foreach ($names as $name) {
            if ($this->get($name) !== null) {
                throw new Exception(sprintf(
                    "Cannot redefine right with name '%s'",
                    $name
                ));
            }

            $this->rights[$name] = $this->getNextValue();
        }
    }

    /**
     * @return int[]
     */
    public function getAll(): array
    {
        return $this->rights;
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        return array_keys($this->rights);
    }

    public function get(string $name): ?int
    {
        return $this->rights[$name] ?? null;
    }

    public function can(string $provided, string $name): bool
    {
        return (bool)(bindec($provided) & $this->get($name));
    }

    public function combine(string ...$rights): string
    {
        $combined = str_repeat('0', count($this->rights));

        foreach ($rights as $right) {
            $combined |= $right;
        }

        return $combined;
    }

    public function getRightsFromNames(string ...$names): string
    {
        $rightsString = '';

        foreach ($this->rights as $right => $value) {
            $rightsString .= in_array($right, $names) ? '1' : '0';
        }

        return strrev($rightsString);
    }

    /**
     * @return string[]
     */
    public function getNamesFromRights(string $rights): array
    {
        $names = [];

        foreach ($this->rights as $name => $value) {
            if ($this->can($rights, $name)) {
                $names[] = $name;
            }
        }

        return $names;
    }

    protected function getNextValue(): int
    {
        if (count($this->rights) === 0) {
            return 1;
        }

        return end($this->rights) * 2;
    }
}
