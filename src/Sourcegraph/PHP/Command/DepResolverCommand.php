<?php

namespace Sourcegraph\PHP\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sourcegraph\PHP\DepResolver;
use Sourcegraph\PHP\SourceUnit;

class DepResolverCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('depresolve')
            ->setDescription('resolve a Composer package\'s depedencies');
    }

    protected function getResult(SourceUnit $unit)
    {
        $resolver = new DepResolver();
        return $resolver->run($unit);
    }
}
