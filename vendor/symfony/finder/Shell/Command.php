<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marfeel\Symfony\Component\Finder\Shell;

@trigger_error('The '.__NAMESPACE__.'\Command class is deprecated since Symfony 2.8 and will be removed in 3.0.', E_USER_DEPRECATED);

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 *
 * @deprecated since 2.8, to be removed in 3.0.
 */
class Command
{
    private $parent;
    private $bits = array();
    private $labels = array();

    /**
     * @var \Closure|null
     */
    private $errorHandler;

    public function __construct(Command $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Returns command as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->join();
    }

    /**
     * Creates a new Command instance.
     *
     * @return self
     */
    public static function create(Command $parent = null)
    {
        return new self($parent);
    }

    /**
     * Escapes special chars from input.
     *
     * @param string $input A string to escape
     *
     * @return string The escaped string
     */
    public static function escape($input)
    {
        return escapeshellcmd($input);
    }

    /**
     * Quotes input.
     *
     * @param string $input An argument string
     *
     * @return string The quoted string
     */
    public static function quote($input)
    {
        return escapeshellarg($input);
    }

    /**
     * Appends a string or a Command instance.
     *
     * @param string|Command $bit
     *
     * @return $this
     */
    public function add($bit)
    {
        $this->bits[] = $bit;

        return $this;
    }

    /**
     * Prepends a string or a command instance.
     *
     * @param string|Command $bit
     *
     * @return $this
     */
    public function top($bit)
    {
        array_unshift($this->bits, $bit);

        foreach ($this->labels as $label => $index) {
            $this->labels[$label] += 1;
        }

        return $this;
    }

    /**
     * Appends an argument, will be quoted.
     *
     * @param string $arg
     *
     * @return $this
     */
    public function arg($arg)
    {
        $this->bits[] = self::quote($arg);

        return $this;
    }

    /**
     * Appends escaped special command chars.
     *
     * @param string $esc
     *
     * @return $this
     */
    public function cmd($esc)
    {
        $this->bits[] = self::escape($esc);

        return $this;
    }

    /**
     * Inserts a labeled command to feed later.
     *
     * @param string $label The unique label
     *
     * @return self|string
     *
     * @throws \RuntimeException If label already exists
     */
    public function ins($label)
    {
        if (isset($this->labels[$label])) {
            throw new \RuntimeException(sprintf('Label "%s" already exists.', $label));
        }

        $this->bits[] = self::create($this);
        $this->labels[$label] = count($this->bits) - 1;

        return $this->bits[$this->labels[$label]];
    }

    /**
     * Retrieves a previously labeled command.
     *
     * @param string $label
     *
     * @return self|string
     *
     * @throws \RuntimeException
     */
    public function get($label)
    {
        if (!isset($this->labels[$label])) {
            throw new \RuntimeException(sprintf('Label "%s" does not exist.', $label));
        }

        return $this->bits[$this->labels[$label]];
    }

    /**
     * Returns parent command (if any).
     *
     * @return self
     *
     * @throws \RuntimeException If command has no parent
     */
    public function end()
    {
        if (null === $this->parent) {
            throw new \RuntimeException('Calling end on root command doesn\'t make sense.');
        }

        return $this->parent;
    }

    /**
     * Counts bits stored in command.
     *
     * @return int The bits count
     */
    public function length()
    {
        return count($this->bits);
    }

    /**
     * @return $this
     */
    public function setErrorHandler(\Closure $errorHandler)
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    /**
     * @return \Closure|null
     */
    public function getErrorHandler()
    {
        return $this->errorHandler;
    }

    /**
     * Executes current command.
     *
     * @return array The command result
     *
     * @throws \RuntimeException
     */
    public function execute()
    {
        if (null === $errorHandler = $this->errorHandler) {
            exec($this->join(), $output);
        } else {
            $process = proc_open($this->join(), array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
            $output = preg_split('~(\r\n|\r|\n)~', stream_get_contents($pipes[1]), -1, PREG_SPLIT_NO_EMPTY);

            if ($error = stream_get_contents($pipes[2])) {
                $errorHandler($error);
            }

            proc_close($process);
        }

        return $output ?: array();
    }

    /**
     * Joins bits.
     *
     * @return string
     */
    public function join()
    {
        return implode(' ', array_filter(
            array_map(function ($bit) {
                return $bit instanceof Command ? $bit->join() : ($bit ?: null);
            }, $this->bits),
            function ($bit) { return null !== $bit; }
        ));
    }

    /**
     * Insert a string or a Command instance before the bit at given position $index (index starts from 0).
     *
     * @param string|Command $bit
     * @param int            $index
     *
     * @return $this
     */
    public function addAtIndex($bit, $index)
    {
        array_splice($this->bits, $index, 0, $bit instanceof self ? array($bit) : $bit);

        return $this;
    }
}
