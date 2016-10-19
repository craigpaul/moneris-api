<?php

namespace CraigPaul\Moneris\Exceptions;

use Exception;

class ValidationException extends Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * Create a new exception instance.
     *
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct('The given data failed to pass validation.', 422);

        $this->errors = $errors;
    }

    /**
     * Get the errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}