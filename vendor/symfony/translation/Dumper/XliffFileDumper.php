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
 * XliffFileDumper generates xliff files from a message catalogue.
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
class XliffFileDumper extends FileDumper
{
    /**
     * {@inheritdoc}
     */
    public function formatCatalogue(MessageCatalogue $messages, $domain, array $options = array())
    {
        $xliffVersion = '1.2';
        if (array_key_exists('xliff_version', $options)) {
            $xliffVersion = $options['xliff_version'];
        }

        if (array_key_exists('default_locale', $options)) {
            $defaultLocale = $options['default_locale'];
        } else {
            $defaultLocale = \Locale::getDefault();
        }

        if ('1.2' === $xliffVersion) {
            return $this->dumpXliff1($defaultLocale, $messages, $domain, $options);
        }
        if ('2.0' === $xliffVersion) {
            return $this->dumpXliff2($defaultLocale, $messages, $domain, $options);
        }

        throw new \InvalidArgumentException(sprintf('No support implemented for dumping XLIFF version "%s".', $xliffVersion));
    }

    /**
     * {@inheritdoc}
     */
    protected function format(MessageCatalogue $messages, $domain)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since Symfony 2.8 and will be removed in 3.0. Use the formatCatalogue() method instead.', E_USER_DEPRECATED);

        return $this->formatCatalogue($messages, $domain);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'xlf';
    }

    private function dumpXliff1($defaultLocale, MessageCatalogue $messages, $domain, array $options = array())
    {
        $toolInfo = array('tool-id' => 'symfony', 'tool-name' => 'Symfony');
        if (array_key_exists('tool_info', $options)) {
            $toolInfo = array_merge($toolInfo, $options['tool_info']);
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $xliff = $dom->appendChild($dom->createElement('xliff'));
        $xliff->setAttribute('version', '1.2');
        $xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');

        $xliffFile = $xliff->appendChild($dom->createElement('file'));
        $xliffFile->setAttribute('source-language', str_replace('_', '-', $defaultLocale));
        $xliffFile->setAttribute('target-language', str_replace('_', '-', $messages->getLocale()));
        $xliffFile->setAttribute('datatype', 'plaintext');
        $xliffFile->setAttribute('original', 'file.ext');

        $xliffHead = $xliffFile->appendChild($dom->createElement('header'));
        $xliffTool = $xliffHead->appendChild($dom->createElement('tool'));
        foreach ($toolInfo as $id => $value) {
            $xliffTool->setAttribute($id, $value);
        }

        $xliffBody = $xliffFile->appendChild($dom->createElement('body'));
        foreach ($messages->all($domain) as $source => $target) {
            $translation = $dom->createElement('trans-unit');

            $translation->setAttribute('id', md5($source));
            $translation->setAttribute('resname', $source);

            $s = $translation->appendChild($dom->createElement('source'));
            $s->appendChild($dom->createTextNode($source));

            // Does the target contain characters requiring a CDATA section?
            $text = 1 === preg_match('/[&<>]/', $target) ? $dom->createCDATASection($target) : $dom->createTextNode($target);

            $targetElement = $dom->createElement('target');
            $metadata = $messages->getMetadata($source, $domain);
            if ($this->hasMetadataArrayInfo('target-attributes', $metadata)) {
                foreach ($metadata['target-attributes'] as $name => $value) {
                    $targetElement->setAttribute($name, $value);
                }
            }
            $t = $translation->appendChild($targetElement);
            $t->appendChild($text);

            if ($this->hasMetadataArrayInfo('notes', $metadata)) {
                foreach ($metadata['notes'] as $note) {
                    if (!isset($note['content'])) {
                        continue;
                    }

                    $n = $translation->appendChild($dom->createElement('note'));
                    $n->appendChild($dom->createTextNode($note['content']));

                    if (isset($note['priority'])) {
                        $n->setAttribute('priority', $note['priority']);
                    }

                    if (isset($note['from'])) {
                        $n->setAttribute('from', $note['from']);
                    }
                }
            }

            $xliffBody->appendChild($translation);
        }

        return $dom->saveXML();
    }

    private function dumpXliff2($defaultLocale, MessageCatalogue $messages, $domain, array $options = array())
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $xliff = $dom->appendChild($dom->createElement('xliff'));
        $xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:2.0');
        $xliff->setAttribute('version', '2.0');
        $xliff->setAttribute('srcLang', str_replace('_', '-', $defaultLocale));
        $xliff->setAttribute('trgLang', str_replace('_', '-', $messages->getLocale()));

        $xliffFile = $xliff->appendChild($dom->createElement('file'));
        $xliffFile->setAttribute('id', $domain.'.'.$messages->getLocale());

        foreach ($messages->all($domain) as $source => $target) {
            $translation = $dom->createElement('unit');
            $translation->setAttribute('id', md5($source));

            $segment = $translation->appendChild($dom->createElement('segment'));

            $s = $segment->appendChild($dom->createElement('source'));
            $s->appendChild($dom->createTextNode($source));

            // Does the target contain characters requiring a CDATA section?
            $text = 1 === preg_match('/[&<>]/', $target) ? $dom->createCDATASection($target) : $dom->createTextNode($target);

            $targetElement = $dom->createElement('target');
            $metadata = $messages->getMetadata($source, $domain);
            if ($this->hasMetadataArrayInfo('target-attributes', $metadata)) {
                foreach ($metadata['target-attributes'] as $name => $value) {
                    $targetElement->setAttribute($name, $value);
                }
            }
            $t = $segment->appendChild($targetElement);
            $t->appendChild($text);

            $xliffFile->appendChild($translation);
        }

        return $dom->saveXML();
    }

    /**
     * @param string     $key
     * @param array|null $metadata
     *
     * @return bool
     */
    private function hasMetadataArrayInfo($key, $metadata = null)
    {
        return null !== $metadata && array_key_exists($key, $metadata) && ($metadata[$key] instanceof \Traversable || is_array($metadata[$key]));
    }
}
