<?php

namespace Sourcegraph\PHP\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Sourcegraph\PHP\Grapher;

class GraphCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('graph')
            ->setDescription('graph a Composer package');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stdin = fgets(STDIN);
        $unit = json_decode($stdin, true);

        $result = $this->graph($unit);
        $json = json_encode($result, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

        $output->writeln($json);
    }

    protected function graph(Array $unit)
    {
        $grapher = new Grapher('.');
        return $grapher->run($unit);
    }
}
