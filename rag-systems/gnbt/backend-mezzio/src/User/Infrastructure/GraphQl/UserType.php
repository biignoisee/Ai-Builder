<?php

namespace User\Infrastructure\GraphQL;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class UserType extends ObjectType
{
  public function __construct()
  {
    parent::__construct([
      'name' => 'User',
      'fields' => [
        'id' => Type::int(),
        'name' => Type::string(),
        'email' => Type::string(),
      ]
    ]);
  }
}
