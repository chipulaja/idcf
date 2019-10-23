<?php

namespace IDCF\Console;

use Symfony\Component\Console;

class CliA extends Console\Application
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), $this->getApplicationCommands());
    }

    /**
     * Gets application commands.
     *
     * @return array a list of available application commands
     */
    protected function getApplicationCommands()
    {
        $commands = [
            'idcf' => new \IDCF\Console\Command\Idcf(),
            'trainingAdd' => new \IDCF\Console\Command\TrainingAdd(),
            'trainingDelete' => new \IDCF\Console\Command\TrainingDelete()
        ];

        return $commands;
    }
}
