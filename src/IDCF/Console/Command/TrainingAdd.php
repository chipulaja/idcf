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
class TrainingAdd extends Command
{
    protected $idcfFile;
    protected $issueFile;
    protected $fileErrorCounter = 0;
    protected $idcfCsvFile;

    public function __construct($config)
    {
        $this->idcfFile = $config["idcfFile"];
        $this->issueFile = $config["issueFile"];
        $this->idcfCsvFile = dirname($this->idcfFile)."/idcf.csv";

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('idcf:training-add');
        $this->setDescription('Adding data training');
        $this->setHelp('This command allows you to adding data training');
        $this->setDefinition(
            [
                new InputArgument(
                    'file',
                    InputArgument::REQUIRED,
                    'file or path'
                ),
                new InputArgument(
                    'type',
                    InputArgument::OPTIONAL,
                    'csv|xml',
                    'xml'
                )
            ]
        );

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
        $type = 'xml';
        if (in_array(strtolower($input->getArgument('type')), ['xml','csv'])) {
            $type = $input->getArgument('type');
        }
        $filePath = $input->getArgument('file');
        if (! file_exists($filePath)) {
            exit("File doesn't exist");
        }

        $w = $this->getW();
        $listFileError = [];
        $currentTime = new \DateTime();

        if (is_file($filePath)) {
            $progressBar = $this->getProgressBar($output);
            $progressBar->advance();
            $w = $this->training($filePath, $type, $w);
            $progressBar->finish();
        } else {
            $files = \Nette\Utils\Finder::findFiles('*.'.$type)->from($filePath);
            if (count($files) == 0) {
                exit("File doesn't exist");
            }

            $progressBar = $this->getProgressBar($output, count($files));
            foreach ($files as $key => $file) {
                $w = $this->training($file, $type, $w);
                $progressBar->advance();
            }
            $progressBar->finish();
        }

        $this->saveToFile(json_encode($w), $this->idcfFile, "w");
        $output->writeln("\n<info>done.</info>");

        if ($output->isVerbose()) {
            $this->saveWToCsv($w);
        }
        if ($this->fileErrorCounter > 0) {
            $output->writeln("<comment>have $this->fileErrorCounter issue, please chek file $this->issueFile<comment>");
        }
    }

    protected function getW()
    {
        $w = ["i" => 0, "weight" => [], "category" => []];
        if (file_exists($this->idcfFile)) {
            $json = file_get_contents($this->idcfFile);
            $idcf = json_decode($json, true);
            if ($idcf) {
                $w = $idcf;
            }
        }

        return $w;
    }

    protected function getProgressBar($output, $maxBar = 1)
    {
        $progressBar = new ProgressBar($output, $maxBar);
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();
        return $progressBar;
    }

    protected function training($filePath, $type, $w)
    {
        try {
            if ($type === 'csv') {
                $w = $this->csvTrainer($filePath, $w);
            } elseif ($type === 'xml') {
                $w = $this->xmlTrainer($filePath, $w);
            }
        } catch (FileFormatException $e) {
            $this->saveIssue($e->getMessage());
            $this->fileErrorCounter++;
        }

        return $w;
    }

    protected function csvTrainer($filePath, $w)
    {
        $file = fopen($filePath, "r");
        $flag = true;
        $header = [];
        $wi = $w["i"];
        while (($row = fgetcsv($file)) !== false) {
            if ($flag) {
                $header = ["kata kunci", "isi"];
                foreach ($header as $head) {
                    if (! in_array($head, array_values($row))) {
                        throw new FileFormatException("$filePath : Header $head not found");
                    }
                }
                $header = array_flip($row);
                $flag = false;
                continue;
            }
            $category = $row[$header["kata kunci"]];
            $content  = $row[$header["isi"]];

            if (empty($category) || empty($content)) {
                throw new FileFormatException($filePath." : Category or content cannot by empty");
            }

            $preprocessing = new \IDCF\Preprocessing();
            $weighting = new \IDCF\Weighting();
            $tokens = $preprocessing->execute($content);
            $docname = "d".++$wi;
            $w = $weighting->execute($tokens, $category, $docname, $w["weight"], $w["category"]);
            $w["i"] = $wi;
        }
        return $w;
    }

    protected function xmlTrainer($filePath, $w)
    {
        $artikel = @simplexml_load_file($filePath);
        if ($artikel === false) {
            throw new FileFormatException($filePath." : Please check format xml");
        }

        $category = (string)$artikel->kata_kunci;
        $content  = (string)$artikel->isi;
        if (empty($category) || empty($content)) {
            throw new FileFormatException($filePath." : Category or content cannot by empty");
        }

        $preprocessing = new \IDCF\Preprocessing();
        $weighting = new \IDCF\Weighting();
        $tokens = $preprocessing->execute($content);
        $wi = $w["i"];
        $docname = "d".++$wi;
        $w = $weighting->execute($tokens, $category, $docname, $w["weight"], $w["category"]);
        $w["i"] = $wi;
        return $w;
    }

    protected function saveWToCsv($w)
    {
        $fp = fopen($this->idcfCsvFile, 'w');
        $converter = new ArrayConverter();
        $table = $converter->convertToTableSinggleKey($w["weight"]);
        foreach ($table as $data) {
            fputcsv($fp, $data);
        }
        fclose($fp);
    }

    protected function saveIssue($message)
    {
        $currentTime = new \DateTime();
        $this->saveToFile(
            $currentTime->format("Y-m-d H:i:s").": ".$message,
            $this->issueFile
        );
    }

    protected function saveToFile($message, $filePath, $mode = 'a')
    {
        $fp = fopen($filePath, $mode);
        fwrite($fp, $message.PHP_EOL);
        fclose($fp);
    }
}
