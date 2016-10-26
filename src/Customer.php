<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Customer
 *
 * @property array $data
 * @property string $email
 * @property string $id
 * @property string $note
 * @property string $phone
 */
class Customer
{
    use Gettable, Preparable, Settable;

    /**
     * The Customer data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Create a new Customer instance.
     *
     * @param array $params
     *
     * @return void
     */
    public function __construct(array $params = [])
    {
        $this->data = $this->prepare($params, [
            ['property' => 'id', 'key' => 'id'],
            ['property' => 'email', 'key' => 'email'],
            ['property' => 'phone', 'key' => 'phone'],
            ['property' => 'note', 'key' => 'note'],
        ]);
    }

    /**
     * Create a new Customer instance.
     *
     * @param array $params
     *
     * @return \CraigPaul\Moneris\Customer
     */
    public static function create(array $params = [])
    {
        return new static($params);
    }

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

        if (isset($this->data[$property]) && !is_null($this->data[$property])) {
            return $this->data[$property];
        }

        throw new \InvalidArgumentException('['.get_class($this).'] does not contain a property named ['.$property.']');
    }
}
