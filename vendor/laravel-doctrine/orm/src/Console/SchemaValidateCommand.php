<?php

namespace LaravelDoctrine\ORM\Console;

use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;

class SchemaValidateCommand extends ValidateSchemaCommand
{
    public function __construct(EntityManagerProvider $entityManagerProvider)
    {
        parent::__construct($entityManagerProvider);
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('doctrine:schema:validate');
    }
}
