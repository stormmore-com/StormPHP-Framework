<?php

namespace Configuration;

use Stormmore\Framework\Form\Form;
use Stormmore\Framework\Request\Request;

class CustomMessagesForm extends Form
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->validator->for('required')->required(message: "Required validator return error");
        $this->validator->for('alpha')->alpha(message: "Alpha validator return error");
        $this->validator->for('alphaNum')->alphaNumeric(message: "Alpha numeric return error");
        $this->validator->for('regexp')->regexp('#^[A-Z][a-zA-Z0-9]*$#', message: "Regexp validator return error");
        $this->validator->for('values')->values([1,2], message: "Values validator return error");
        $this->validator->for('email')->email(message: "Email validator return error");
        $this->validator->for('min')->min(1, message: 'Min validator return error');
        $this->validator->for('max')->max(10, message: 'Max validator return error');
        $this->validator->for('int')->int(message: "Integer validator returns error");
        $this->validator->for('float')->float(message: "Float validator return error");
        $this->validator->for('number')->number(message: "Number validator return error");
        $this->validator->for('file')->file(size: 10, message: "File validator return error");
        $this->validator->for('image')->image(types: array(IMAGETYPE_JPEG), message: "Image validator return error");
    }
}