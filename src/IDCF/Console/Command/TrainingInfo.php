<?php

namespace IDCF\Console\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use IDCF\Helper\ArrayConverter;
use IDCF\Exceptions\FileFormatException;

/**
 * Class MyCommand
 *
 */
class TrainingInfo extends Command
{
    protected $idcfFile;
    protected $issueFile;
    protected $fileErrorCounter = 0;
    protected $idcfCsvFile;

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
        $this->setName('idcf:training-info');
        $this->setDescription('Showing info data training');
        $this->setHelp('This command allows showing info data training');
        parent::configure();
    }
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $w = $this->getW();
		$wordList = isset($w['weight']['wordList']) ? $w['weight']['wordList'] : [];
		$docnameList = isset($w['weight']['docnameList']) ? $w['weight']['docnameList'] : [];
		$classList = isset($w['classList']) ? $w['classList'] : [];

		$output->writeln("\n<info>Words \t:".sizeof($wordList)."</info>");
		$output->writeln("<info>Docs \t:".sizeof($docnameList)."</info>");
		$output->writeln("<info>Class \t:".sizeof($classList)."</info>");
    }

    protected function getW()
    {
        $w = ["i" => 0, "weight" => [], "classList" => []];
        if (file_exists($this->idcfFile)) {
            $json = file_get_contents($this->idcfFile);
            $idcf = json_decode($json, true);
            if ($idcf) {
                $w = $idcf;
            }
        }

        return $w;
    }
}
