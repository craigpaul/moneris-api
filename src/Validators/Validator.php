<?php

namespace CraigPaul\Moneris\Validators;

use CraigPaul\Moneris\Exceptions\ValidationException;

;
use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

abstract class Validator
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Append the provided errors.
     *
     * @param \Respect\Validation\Validator $rules
     * @param string $message
     *
     * @return void
     */
    protected function append(Respect $rules, string $message)
    {
        try {
            $rules->assert($message);
        } catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                array_push($this->errors, $message);
            }
        }
    }

    /**
     * Check if any errors have occurred.
     *
     * @throws \CraigPaul\Moneris\Exceptions\ValidationException
     * @return bool
     */
    protected function check()
    {
        if (count($this->errors) > 0) {
            throw new ValidationException($this->errors);
        }

        return true;
    }

    /**
     * @param \Respect\Validation\Validator $rules
     * @param mixed $params
     *
     * @throws \CraigPaul\Moneris\Exceptions\ValidationException
     * @return void
     */
    protected function execute(Respect $rules, $params)
    {
        if (!$rules->validate($params)) {
            $this->append($rules, 'params');
            $this->check();
        }
    }
}