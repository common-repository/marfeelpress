<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marfeel\Symfony\Component\Console\Command;

use Marfeel\Symfony\Component\Console\Helper\DescriptorHelper;
use Marfeel\Symfony\Component\Console\Input\InputArgument;
use Marfeel\Symfony\Component\Console\Input\InputOption;
use Marfeel\Symfony\Component\Console\Input\InputInterface;
use Marfeel\Symfony\Component\Console\Output\OutputInterface;

/**
 * HelpCommand displays the help for a given command.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HelpCommand extends Command
{
    private $command;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('help')
            ->setDefinition(array(
                new InputArgument('command_name', InputArgument::OPTIONAL, 'The command name', 'help'),
                new InputOption('xml', null, InputOption::VALUE_NONE, 'To output help as XML'),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command help'),
            ))
            ->setDescription('Displays help for a command')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays help for a given command:

  <info>php %command.full_name% list</info>

You can also output the help in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml list</info>

To display the list of available commands, please use the <info>list</info> command.
EOF
            )
        ;
    }

    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->command) {
            $this->command = $this->getApplication()->find($input->getArgument('command_name'));
        }

        if ($input->getOption('xml')) {
            @trigger_error('The --xml option was deprecated in version 2.7 and will be removed in version 3.0. Use the --format option instead.', E_USER_DEPRECATED);

            $input->setOption('format', 'xml');
        }

        $helper = new DescriptorHelper();
        $helper->describe($output, $this->command, array(
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
        ));

        $this->command = null;
    }
}
