<?php

namespace CraigPaul\Moneris\Validators;

use Respect\Validation\Validator as V;

class Purchase extends Validator implements Validatable
{
    /**
     * Validate the supplied parameters.
     *
     * @param array $params
     *
     * @throws \CraigPaul\Moneris\Exceptions\ValidationException
     * @return void
     */
    public function validate(array $params)
    {
        $rules = V::key('order_id')->key('amount')->key('pan')->key('expdate');
        $this->execute($rules, $params);

        $rules = V::stringType()->length(3, 50);
        $this->execute($rules, $params['order_id']);

        $rules = V::stringType()->length(3, 9)->contains('.');
        $this->execute($rules, $params['amount']);

        $rules = V::creditCard();
        $this->execute($rules, $params['pan']);

        $rules = V::date('yd');
        $this->execute($rules, $params['expdate']);
    }
}