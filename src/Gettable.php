<?php

namespace CraigPaul\Moneris;

trait Gettable
{
    /**
     * Retrieve a property off of the class.
     *
     * @param string $property
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new \InvalidArgumentException('['.get_class($this).'] does not contain a property named ['.$property.']');
    }
}
