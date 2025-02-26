<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marfeel\Symfony\Component\Translation\Dumper;

use Marfeel\Symfony\Component\Translation\MessageCatalogue;

/**
 * FileDumper is an implementation of DumperInterface that dump a message catalogue to file(s).
 * Performs backup of already existing files.
 *
 * Options:
 * - path (mandatory): the directory where the files should be saved
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
abstract class FileDumper implements DumperInterface
{
    /**
     * A template for the relative paths to files.
     *
     * @var string
     */
    protected $relativePathTemplate = '%domain%.%locale%.%extension%';

    /**
     * Make file backup before the dump.
     *
     * @var bool
     */
    private $backup = true;

    /**
     * Sets the template for the relative paths to files.
     *
     * @param string $relativePathTemplate A template for the relative paths to files
     */
    public function setRelativePathTemplate($relativePathTemplate)
    {
        $this->relativePathTemplate = $relativePathTemplate;
    }

    /**
     * Sets backup flag.
     *
     * @param bool
     */
    public function setBackup($backup)
    {
        $this->backup = $backup;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(MessageCatalogue $messages, $options = array())
    {
        if (!array_key_exists('path', $options)) {
            throw new \InvalidArgumentException('The file dumper needs a path option.');
        }

        // save a file for each domain
        foreach ($messages->getDomains() as $domain) {
            // backup
            $fullpath = $options['path'].'/'.$this->getRelativePath($domain, $messages->getLocale());
            if (file_exists($fullpath)) {
                if ($this->backup) {
                    copy($fullpath, $fullpath.'~');
                }
            } else {
                $directory = dirname($fullpath);
                if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
                    throw new \RuntimeException(sprintf('Unable to create directory "%s".', $directory));
                }
            }
            // save file
            file_put_contents($fullpath, $this->formatCatalogue($messages, $domain, $options));
        }
    }

    /**
     * Transforms a domain of a message catalogue to its string representation.
     *
     * Override this function in child class if $options is used for message formatting.
     *
     * @param MessageCatalogue $messages
     * @param string           $domain
     * @param array            $options
     *
     * @return string representation
     */
    public function formatCatalogue(MessageCatalogue $messages, $domain, array $options = array())
    {
        @trigger_error('The '.__METHOD__.' method will replace the format method in 3.0. You should overwrite it instead of overwriting format instead.', E_USER_DEPRECATED);

        return $this->format($messages, $domain);
    }

    /**
     * Transforms a domain of a message catalogue to its string representation.
     *
     * @param MessageCatalogue $messages
     * @param string           $domain
     *
     * @return string representation
     *
     * @deprecated since version 2.8, to be removed in 3.0. Overwrite formatCatalogue() instead.
     */
    protected function format(MessageCatalogue $messages, $domain)
    {
        throw new \LogicException('The "FileDumper::format" method needs to be overwritten, you should implement either "format" or "formatCatalogue".');
    }

    /**
     * Gets the file extension of the dumper.
     *
     * @return string file extension
     */
    abstract protected function getExtension();

    /**
     * Gets the relative file path using the template.
     *
     * @param string $domain The domain
     * @param string $locale The locale
     *
     * @return string The relative file path
     */
    private function getRelativePath($domain, $locale)
    {
        return strtr($this->relativePathTemplate, array(
            '%domain%' => $domain,
            '%locale%' => $locale,
            '%extension%' => $this->getExtension(),
        ));
    }
}
