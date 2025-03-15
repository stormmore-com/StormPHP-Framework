<?php

namespace Configuration;

use Stormmore\Framework\Form\Form;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Validation\Validator;

class BasicForm extends Form
{
    public function __construct(Request $request, Validator $validator)
    {
        parent::__construct($request, $validator);

        $this->validator->for('alpha')->alpha();
        $this->validator->for('alphaMin')->alpha()->min(2)->required();
        $this->validator->for('alphaMax')->alpha()->max(5)->required();
        $this->validator->for('alphaNum')->alphaNumeric()->required();
        $this->validator->for('radio')->values(array('on', 'off'))->required();
        $this->validator->for('radioBool')->values(array(true, false))->required();
        $this->validator->for('email')->email()->required();
        $this->validator->for('float')->float()->required();
        $this->validator->for('int')->int()->required();
        $this->validator->for('checkbox')->required();
        $this->validator->for('vegetables')->values(array('onion', 'carrot'))->required();
        $this->validator->for('min')->int()->min(8)->required();
        $this->validator->for('max')->int()->max(10)->required();
        $this->validator->for('num')->number()->required();
        $this->validator->for('regexp')->regexp('#^[A-Z][a-zA-Z0-9]*$#')->required();
        $this->validator->for('image')->image(types: [IMAGETYPE_JPEG]);
        $this->validator->for('file')->file(extensions: ['txt'], size: 10);
        $this->validator->for('day')->values(['Saturday', 'Sunday'])->required();
        if ($this->request->getParameter('files-required', false)) {
            $this->validator->for('image')->required();
            $this->validator->for('file')->required();
        }
    }
}