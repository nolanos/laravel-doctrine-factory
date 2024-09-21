<?php

namespace Workbench\App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Entity, Table(name: 'users')]
class User
{
    use HasFactory;

    #[Id, GeneratedValue, Column(type: 'integer')]
    protected $id;

    #[Column(type: 'string')]
    protected $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}