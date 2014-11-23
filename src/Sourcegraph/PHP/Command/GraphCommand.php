<?php

namespace Sourcegraph\PHP\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sourcegraph\PHP\Grapher;
use Sourcegraph\PHP\SourceUnit;

class GraphCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('graph')
            ->setDescription('graph a Composer package');
    }

    protected function getResult(SourceUnit $unit)
    {
        $grapher = new Grapher();
        return $grapher->run($unit);
    }
}
