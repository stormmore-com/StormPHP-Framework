<?php

namespace Configuration;

use Stormmore\Framework\Form\Form;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Validation\Validator\RequiredValidator;
use Stormmore\Framework\Validation\Validators\AlphaNumValidator;

class BasicForm extends Form
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

//        $this->validators = [
//            'field' => new RequiredValidator(),
//            //'text' => [AlphaNumValidator::class, RequiredValidator::class],
//        ];
    }
}