<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Customer
 *
 * @property string|null $id
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $note
 */
class Customer
{
    use Gettable, Settable;

    /**
     * The Customer ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Customer email.
     *
     * @var string
     */
    protected $email;

    /**
     * The Customer phone.
     *
     * @var string
     */
    protected $phone;

    /**
     * The Customer note.
     *
     * @var string
     */
    protected $note;

    /**
     * Create a new Customer instance.
     *
     * @param array $params
     *
     * @return void
     */
    public function __construct(array $params = [])
    {
        $this->id = isset($params['id']) ? $params['id'] : null;
        $this->email = isset($params['email']) ? $params['email'] : null;
        $this->phone = isset($params['phone']) ? $params['phone'] : null;
        $this->note = isset($params['note']) ? $params['note'] : null;
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
}
