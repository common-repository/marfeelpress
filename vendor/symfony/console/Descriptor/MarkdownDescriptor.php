<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marfeel\Symfony\Component\Console\Descriptor;

use Marfeel\Symfony\Component\Console\Application;
use Marfeel\Symfony\Component\Console\Command\Command;
use Marfeel\Symfony\Component\Console\Helper\Helper;
use Marfeel\Symfony\Component\Console\Input\InputArgument;
use Marfeel\Symfony\Component\Console\Input\InputDefinition;
use Marfeel\Symfony\Component\Console\Input\InputOption;

/**
 * Markdown descriptor.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 *
 * @internal
 */
class MarkdownDescriptor extends Descriptor
{
    /**
     * {@inheritdoc}
     */
    protected function describeInputArgument(InputArgument $argument, array $options = array())
    {
        $this->write(
            '**'.$argument->getName().':**'."\n\n"
            .'* Name: '.($argument->getName() ?: '<none>')."\n"
            .'* Is required: '.($argument->isRequired() ? 'yes' : 'no')."\n"
            .'* Is array: '.($argument->isArray() ? 'yes' : 'no')."\n"
            .'* Description: '.preg_replace('/\s*[\r\n]\s*/', "\n  ", $argument->getDescription() ?: '<none>')."\n"
            .'* Default: `'.str_replace("\n", '', var_export($argument->getDefault(), true)).'`'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputOption(InputOption $option, array $options = array())
    {
        $this->write(
            '**'.$option->getName().':**'."\n\n"
            .'* Name: `--'.$option->getName().'`'."\n"
            .'* Shortcut: '.($option->getShortcut() ? '`-'.implode('|-', explode('|', $option->getShortcut())).'`' : '<none>')."\n"
            .'* Accept value: '.($option->acceptValue() ? 'yes' : 'no')."\n"
            .'* Is value required: '.($option->isValueRequired() ? 'yes' : 'no')."\n"
            .'* Is multiple: '.($option->isArray() ? 'yes' : 'no')."\n"
            .'* Description: '.preg_replace('/\s*[\r\n]\s*/', "\n  ", $option->getDescription() ?: '<none>')."\n"
            .'* Default: `'.str_replace("\n", '', var_export($option->getDefault(), true)).'`'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputDefinition(InputDefinition $definition, array $options = array())
    {
        if ($showArguments = count($definition->getArguments()) > 0) {
            $this->write('### Arguments:');
            foreach ($definition->getArguments() as $argument) {
                $this->write("\n\n");
                $this->write($this->describeInputArgument($argument));
            }
        }

        if (count($definition->getOptions()) > 0) {
            if ($showArguments) {
                $this->write("\n\n");
            }

            $this->write('### Options:');
            foreach ($definition->getOptions() as $option) {
                $this->write("\n\n");
                $this->write($this->describeInputOption($option));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function describeCommand(Command $command, array $options = array())
    {
        $command->getSynopsis();
        $command->mergeApplicationDefinition(false);

        $this->write(
            $command->getName()."\n"
            .str_repeat('-', Helper::strlen($command->getName()))."\n\n"
            .'* Description: '.($command->getDescription() ?: '<none>')."\n"
            .'* Usage:'."\n\n"
            .array_reduce(array_merge(array($command->getSynopsis()), $command->getAliases(), $command->getUsages()), function ($carry, $usage) {
                return $carry.'  * `'.$usage.'`'."\n";
            })
        );

        if ($help = $command->getProcessedHelp()) {
            $this->write("\n");
            $this->write($help);
        }

        if ($command->getNativeDefinition()) {
            $this->write("\n\n");
            $this->describeInputDefinition($command->getNativeDefinition());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function describeApplication(Application $application, array $options = array())
    {
        $describedNamespace = isset($options['namespace']) ? $options['namespace'] : null;
        $description = new ApplicationDescription($application, $describedNamespace);

        $this->write($application->getName()."\n".str_repeat('=', Helper::strlen($application->getName())));

        foreach ($description->getNamespaces() as $namespace) {
            if (ApplicationDescription::GLOBAL_NAMESPACE !== $namespace['id']) {
                $this->write("\n\n");
                $this->write('**'.$namespace['id'].':**');
            }

            $this->write("\n\n");
            $this->write(implode("\n", array_map(function ($commandName) {
                return '* '.$commandName;
            }, $namespace['commands'])));
        }

        foreach ($description->getCommands() as $command) {
            $this->write("\n\n");
            $this->write($this->describeCommand($command));
        }
    }
}
