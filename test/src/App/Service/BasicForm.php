<?php

namespace Configuration;

use Stormmore\Framework\Form\Form;
use Stormmore\Framework\Request\Request;

class BasicForm extends Form
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
}