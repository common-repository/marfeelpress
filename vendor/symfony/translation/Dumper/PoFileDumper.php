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
 * PoFileDumper generates a gettext formatted string representation of a message catalogue.
 *
 * @author Stealth35
 */
class PoFileDumper extends FileDumper
{
    /**
     * {@inheritdoc}
     */
    public function format(MessageCatalogue $messages, $domain = 'messages')
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since Symfony 2.8 and will be removed in 3.0. Use the formatCatalogue() method instead.', E_USER_DEPRECATED);

        return $this->formatCatalogue($messages, $domain);
    }

    /**
     * {@inheritdoc}
     */
    public function formatCatalogue(MessageCatalogue $messages, $domain, array $options = array())
    {
        $output = 'msgid ""'."\n";
        $output .= 'msgstr ""'."\n";
        $output .= '"Content-Type: text/plain; charset=UTF-8\n"'."\n";
        $output .= '"Content-Transfer-Encoding: 8bit\n"'."\n";
        $output .= '"Language: '.$messages->getLocale().'\n"'."\n";
        $output .= "\n";

        $newLine = false;
        foreach ($messages->all($domain) as $source => $target) {
            if ($newLine) {
                $output .= "\n";
            } else {
                $newLine = true;
            }
            $output .= sprintf('msgid "%s"'."\n", $this->escape($source));
            $output .= sprintf('msgstr "%s"', $this->escape($target));
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'po';
    }

    private function escape($str)
    {
        return addcslashes($str, "\0..\37\42\134");
    }
}
