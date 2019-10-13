<?php

namespace IDCF\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MyCommand
 *
 */
class TrainingDelete extends Command
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
        $this->setName('idcf:training-delete')
        ->setDescription('Deleteing data training')
        ->setHelp('This command allows you to deleteing data training');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        @unlink($this->idcfFile);
        $output->writeln("<info>done.</info>");
    }
}
