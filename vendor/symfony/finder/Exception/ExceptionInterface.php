<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marfeel\Symfony\Component\Finder\Exception;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
interface ExceptionInterface
{
    /**
     * @return \Marfeel\Symfony\Component\Finder\Adapter\AdapterInterface
     */
    public function getAdapter();
}
