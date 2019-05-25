<?php

namespace bee\console\command;

use bee\console\Command;
use bee\console\Input;
use bee\console\Output;
use bee\console\input\Argument as InputArgument;
use bee\console\input\Option as InputOption;
use bee\console\input\Definition as InputDefinition;

class Lists extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('list')->setDefinition($this->createDefinition())->setDescription('Lists commands')->setHelp(<<<EOF
The <info>%command.name%</info> command lists all commands:

  <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:

  <info>php %command.full_name% test</info>

It's also possible to get raw list of commands (useful for embedding command runner):

  <info>php %command.full_name% --raw</info>
EOF
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeDefinition()
    {
        return $this->createDefinition();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(Input $input, Output $output)
    {
        $output->describe($this->getConsole(), [
            'raw_text'  => $input->getOption('raw'),
            'namespace' => $input->getArgument('namespace'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    private function createDefinition()
    {
        return new InputDefinition([
            new InputArgument('namespace', InputArgument::OPTIONAL, 'The namespace name'),
            new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command list')
        ]);
    }
}
