<?php
/**
 * This file is part of the fnayou/instapush-php project.
 *
 * Copyright (c) 2017. Aymen FNAYOU <fnayou.aymen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fnayou\InstapushPHP\Transformer;

use Fnayou\InstapushPHP\Exception\TransformerException;
use Fnayou\InstapushPHP\Model\FromArrayInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ModelTransformer.
 */
class ModelTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(ResponseInterface $response, string $class)
    {
        $body = $response->getBody()->__toString();
        $data = \json_decode($body, true);

        if (JSON_ERROR_NONE !== \json_last_error()) {
            throw new TransformerException(
                \sprintf(
                    'Invalid json response format : Error %d when trying to \json_decode response',
                    \json_last_error()
                )
            );
        }

        $reflection = new \ReflectionClass($class);
        if (true === $reflection->implementsInterface(FromArrayInterface::class)) {
            $object = \call_user_func($class.'::fromArray', $data);
        } else {
            $object = new $class($data);
        }

        return $object;
    }
}
