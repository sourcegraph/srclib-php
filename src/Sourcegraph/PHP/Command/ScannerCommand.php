<?php

namespace Sourcegraph\PHP\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Sourcegraph\PHP\Scanner;
use Sourcegraph\PHP\SourceUnit\FileNotFound;

class ScannerCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('scan')
            ->setDescription('scan for Composer packages')
            ->addOption(
               'repo',
               null,
               InputOption::VALUE_OPTIONAL,
               'the URI of the repository that contains the directory tree being scanned'
            )
            ->addOption(
               'subdir',
               null,
               InputOption::VALUE_OPTIONAL,
               'directory of the repository being scanned'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $stdin = $helper->ask($input, $output, new Question(''));

        $result = $this->scan($input->getOption('subdir'));
        $json = json_encode($result, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

        $output->writeln($json);
    }

    protected function scan($path = null)
    {
        if (!$path) {
            $path = '.';
        }

        $scanner = new Scanner();

        try {
            return $scanner->run($path);
        } catch (FileNotFound $e) {
            return;
        }
    }
}
