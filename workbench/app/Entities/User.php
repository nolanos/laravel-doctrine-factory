<?php

namespace Workbench\App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class User
{
    use HasFactory;

    public function __construct(
        private string         $name,
    )
    {
        //
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}