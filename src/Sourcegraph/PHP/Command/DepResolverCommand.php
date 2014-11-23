<?php

namespace Sourcegraph\PHP\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Sourcegraph\PHP\DepResolver;
use Sourcegraph\PHP\SourceUnit;
use Sourcegraph\PHP\SourceUnit\ComposerPackage;

class DepResolverCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('depresolve')
            ->setDescription('resolve a Composer package\'s depedencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stdin = fgets(STDIN);
        $json = json_decode($stdin, true);
        $unit = $this->getSourceUnit($json);

        $result = $this->resolve($unit);
        $json = json_encode($result, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

        $output->writeln($json);
    }

    protected function resolve(SourceUnit $unit)
    {
        $resolver = new DepResolver();
        return $resolver->run($unit);
    }

    protected function getSourceUnit(Array $unit)
    {
        switch ($unit['Type']) {
            case 'ComposerPackage':
                return new ComposerPackage($unit['Dir']);
            default:
                throw new Exception(sprintf(
                    "Unsupported SourceUnit type: '%s'",
                    $unit['Type']
                ));
        }

    }
}
