<?php

namespace CraigPaul\Moneris;

trait Settable
{
    /**
     * Set a property that exists on the class.
     *
     * @param string $property
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new \InvalidArgumentException('['.get_class($this).'] does not contain a property named ['.$property.']');
        }
    }
}
