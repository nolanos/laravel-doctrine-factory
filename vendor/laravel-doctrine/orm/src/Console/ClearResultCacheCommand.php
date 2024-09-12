<?php

namespace LaravelDoctrine\ORM\Console;

use Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand;

class ClearResultCacheCommand extends ResultCommand
{
    public function __construct(EntityManagerProvider $entityManagerProvider)
    {
        parent::__construct($entityManagerProvider);
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('doctrine:clear:result:cache');
    }
}
