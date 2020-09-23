<?php
declare(strict_types=1);

namespace NW;

class PasswordGenerator
{
    //add strings from which default passwords are generated from
    private array $syllables = [
        'thi', 's', 'is', 'fake',
    ];

    public function generate(int $syllables): string
    {
        $password = '';

        for ($i = 0; $i < $syllables; $i++) {
            $password .= $this->syllables[random_int(0, count($this->syllables) - 1)];
        }

        return $password;
    }
}