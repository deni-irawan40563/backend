<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class Robots extends Model
{
  public function validation()
  {
    $validator = new Validation();

    $validator->add(
      "type",
      new InclusionIn(
        [
          'message' => 'Type must be "droid", "mechanical", or "virtual"',
          'domain' => [
            'droid',
            'mechanical',
            'virtual',
          ],
        ]
      )
    );

    $validator->add(
      'name',
      new Uniqueness(
        [
          'field' => 'name',
          'message' => 'The patients name must be unique',
        ]
      )
    );

    return $this->validate($validator);
  }
}