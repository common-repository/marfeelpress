<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marfeel\Symfony\Component\Translation\DataCollector;

use Marfeel\Symfony\Component\HttpFoundation\Request;
use Marfeel\Symfony\Component\HttpFoundation\Response;
use Marfeel\Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Marfeel\Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Marfeel\Symfony\Component\Translation\DataCollectorTranslator;

/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
class TranslationDataCollector extends DataCollector implements LateDataCollectorInterface
{
    private $translator;

    public function __construct(DataCollectorTranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        $messages = $this->sanitizeCollectedMessages($this->translator->getCollectedMessages());

        $this->data = $this->computeCount($messages);
        $this->data['messages'] = $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return isset($this->data['messages']) ? $this->data['messages'] : array();
    }

    /**
     * @return int
     */
    public function getCountMissings()
    {
        return isset($this->data[DataCollectorTranslator::MESSAGE_MISSING]) ? $this->data[DataCollectorTranslator::MESSAGE_MISSING] : 0;
    }

    /**
     * @return int
     */
    public function getCountFallbacks()
    {
        return isset($this->data[DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK]) ? $this->data[DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK] : 0;
    }

    /**
     * @return int
     */
    public function getCountDefines()
    {
        return isset($this->data[DataCollectorTranslator::MESSAGE_DEFINED]) ? $this->data[DataCollectorTranslator::MESSAGE_DEFINED] : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'translation';
    }

    private function sanitizeCollectedMessages($messages)
    {
        $result = array();
        foreach ($messages as $key => $message) {
            $messageId = $message['locale'].$message['domain'].$message['id'];

            if (!isset($result[$messageId])) {
                $message['count'] = 1;
                $message['parameters'] = !empty($message['parameters']) ? array($message['parameters']) : array();
                $messages[$key]['translation'] = $this->sanitizeString($message['translation']);
                $result[$messageId] = $message;
            } else {
                if (!empty($message['parameters'])) {
                    $result[$messageId]['parameters'][] = $message['parameters'];
                }

                ++$result[$messageId]['count'];
            }

            unset($messages[$key]);
        }

        return $result;
    }

    private function computeCount($messages)
    {
        $count = array(
            DataCollectorTranslator::MESSAGE_DEFINED => 0,
            DataCollectorTranslator::MESSAGE_MISSING => 0,
            DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK => 0,
        );

        foreach ($messages as $message) {
            ++$count[$message['state']];
        }

        return $count;
    }

    private function sanitizeString($string, $length = 80)
    {
        $string = trim(preg_replace('/\s+/', ' ', $string));

        if (false !== $encoding = mb_detect_encoding($string, null, true)) {
            if (mb_strlen($string, $encoding) > $length) {
                return mb_substr($string, 0, $length - 3, $encoding).'...';
            }
        } elseif (strlen($string) > $length) {
            return substr($string, 0, $length - 3).'...';
        }

        return $string;
    }
}
