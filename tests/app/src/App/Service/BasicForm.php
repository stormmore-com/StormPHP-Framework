<?php

namespace src\App\Service;

use DateTime;
use Stormmore\Framework\Form\Form;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Validation\Field;

class BasicForm extends Form
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->validator
        ->field('alpha', function(Field $field) {
            $field->alpha()->required();
        })
        ->field('alphaMin', function(Field $field) {
            $field->alpha()->min(2)->required();
        })
        ->field('alphaMax', function(Field $field) {
            $field->alpha()->max(5)->required();
        });
        $this->validator->add(Field::for('alphaNum')->alphaNumeric()->required());
        $this->validator->for('alphaNum')->alphaNumeric()->required();
        $this->validator->for('radio')->values(array('on', 'off'))->required();
        $this->validator->for('radioBool')->values(array(true, false))->required();
        $this->validator->for('email')->email()->required();
        $this->validator->for('float')->float()->required();
        $this->validator->for('int')->int()->required();
        $this->validator->for('checkbox')->required();
        $this->validator->for('vegetables')->values(array('onion', 'carrot'))->required();
        $this->validator->for('image')->file()->required();
        $this->validator->for('min')->int()->min(8)->required();
        $this->validator->for('max')->int()->max(10)->required();
        $this->validator->for('after')->dateTime()->after(new DateTime('01-01-2010'))->required();
        $this->validator->for('before')->dateTime()->before(new DateTime('01-01-2020'))->required();
        $this->validator->for('num')->number()->required();
        $this->validator->for('regexp')->regexp('#^[A-Z][a-zA-Z0-9]*$#')->required();
        $this->validator->for('image')->image(types: [IMAGETYPE_JPEG]);
        $this->validator->for('file')->file(extensions: ['txt'], size: 10);
        $this->validator->for('day')->values(['Saturday', 'Sunday'])->required();
        $this->validator->for('password')->required();
        $this->validator->for('password_confirm')->callback(function() {
            $pass = $this->request->post->get('password');
            $confirm = $this->request->post->get('password_confirm');
            return $pass == $confirm;
        }, 'Passwords does not match');
        $this->validator->for("domain")->domainUrl("Nieprawidłowa domena")->required();
        if ($this->request->getDefault('files-required', false)) {
            $this->validator->for('image')->required();
            $this->validator->for('file')->required();
        }
    }
}