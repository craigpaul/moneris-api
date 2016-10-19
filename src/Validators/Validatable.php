<?php

namespace CraigPaul\Moneris\Validators;

interface Validatable
{
    /**
     * Validate the supplied parameters.
     *
     * @param array $params
     *
     * @return void
     */
    public function validate(array $params);
}