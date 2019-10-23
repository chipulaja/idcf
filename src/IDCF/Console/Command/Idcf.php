<?php

namespace IDCF\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Idcf
 */
class Idcf extends Command
{
    protected $idcfFile;

    public function __construct($config)
    {
        $this->idcfFile = $config["idcfFile"];

        parent::__construct();
    }
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('idcf:run')
            ->setDescription('Running idcf app')
            ->setHelp('This command allows you to running idcf app with input data');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('idcf');
    }
}
