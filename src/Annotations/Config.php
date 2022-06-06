<?php

declare(strict_types=1);

/**
 * This file is part of the Max package.
 *
 * (c) Cheng Yao <987861463@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max\Config\Annotations;

use Attribute;
use Max\Aop\Contracts\PropertyAttribute;
use Max\Aop\Exceptions\PropertyHandleException;
use Max\Config\Contracts\ConfigInterface;
use Max\Di\Context;
use Max\Di\Reflection;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Config implements PropertyAttribute
{
    /**
     * @param string     $key     键
     * @param mixed|null $default 默认值
     */
    public function __construct(
        protected string $key,
        protected mixed  $default = null
    )
    {
    }

    public function handle(object $object, string $property): void
    {
        try {
            $container          = Context::getContainer();
            $reflectionProperty = Reflection::property($object::class, $property);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($object, $container->make(ConfigInterface::class)->get($this->key, $this->default));
        } catch (\Throwable $throwable) {
            throw new PropertyHandleException('Property assign failed. ' . $throwable->getMessage());
        }
    }
}
