<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marfeel\Symfony\Component\Config;

use Marfeel\Symfony\Component\Config\Resource\ResourceInterface;
use Marfeel\Symfony\Component\Filesystem\Exception\IOException;
use Marfeel\Symfony\Component\Filesystem\Filesystem;

/**
 * ResourceCheckerConfigCache uses instances of ResourceCheckerInterface
 * to check whether cached data is still fresh.
 *
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class ResourceCheckerConfigCache implements ConfigCacheInterface
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var ResourceCheckerInterface[]
     */
    private $resourceCheckers;

    /**
     * @param string                     $file             The absolute cache path
     * @param ResourceCheckerInterface[] $resourceCheckers The ResourceCheckers to use for the freshness check
     */
    public function __construct($file, array $resourceCheckers = array())
    {
        $this->file = $file;
        $this->resourceCheckers = $resourceCheckers;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->file;
    }

    /**
     * Checks if the cache is still fresh.
     *
     * This implementation will make a decision solely based on the ResourceCheckers
     * passed in the constructor.
     *
     * The first ResourceChecker that supports a given resource is considered authoritative.
     * Resources with no matching ResourceChecker will silently be ignored and considered fresh.
     *
     * @return bool true if the cache is fresh, false otherwise
     */
    public function isFresh()
    {
        if (!is_file($this->file)) {
            return false;
        }

        if (!$this->resourceCheckers) {
            return true; // shortcut - if we don't have any checkers we don't need to bother with the meta file at all
        }

        $metadata = $this->getMetaFile();
        if (!is_file($metadata)) {
            return false;
        }

        $e = null;
        $meta = false;
        $time = filemtime($this->file);
        $signalingException = new \UnexpectedValueException();
        $prevUnserializeHandler = ini_set('unserialize_callback_func', '');
        $prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context) use (&$prevErrorHandler, $signalingException) {
            if (E_WARNING === $type && 'Class __PHP_Incomplete_Class has no unserializer' === $msg) {
                throw $signalingException;
            }

            return $prevErrorHandler ? $prevErrorHandler($type, $msg, $file, $line, $context) : false;
        });

        try {
            $meta = unserialize(file_get_contents($metadata));
        } catch (\Error $e) {
        } catch (\Exception $e) {
        }
        restore_error_handler();
        ini_set('unserialize_callback_func', $prevUnserializeHandler);
        if (null !== $e && $e !== $signalingException) {
            throw $e;
        }
        if (false === $meta) {
            return false;
        }

        foreach ($meta as $resource) {
            /* @var ResourceInterface $resource */
            foreach ($this->resourceCheckers as $checker) {
                if (!$checker->supports($resource)) {
                    continue; // next checker
                }
                if ($checker->isFresh($resource, $time)) {
                    break; // no need to further check this resource
                }

                return false; // cache is stale
            }
            // no suitable checker found, ignore this resource
        }

        return true;
    }

    /**
     * Writes cache.
     *
     * @param string              $content  The content to write in the cache
     * @param ResourceInterface[] $metadata An array of metadata
     *
     * @throws \RuntimeException When cache file can't be written
     */
    public function write($content, array $metadata = null)
    {
        $mode = 0666;
        $umask = umask();
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->file, $content, null);
        try {
            $filesystem->chmod($this->file, $mode, $umask);
        } catch (IOException $e) {
            // discard chmod failure (some filesystem may not support it)
        }

        if (null !== $metadata) {
            $filesystem->dumpFile($this->getMetaFile(), serialize($metadata), null);
            try {
                $filesystem->chmod($this->getMetaFile(), $mode, $umask);
            } catch (IOException $e) {
                // discard chmod failure (some filesystem may not support it)
            }
        }

        if (\function_exists('opcache_invalidate') && ini_get('opcache.enable')) {
            @opcache_invalidate($this->file, true);
        }
    }

    /**
     * Gets the meta file path.
     *
     * @return string The meta file path
     */
    private function getMetaFile()
    {
        return $this->file.'.meta';
    }
}
