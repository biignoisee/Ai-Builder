<?php

namespace User\Domain\Entity;

class User
{
  public function __construct(
    public readonly int $id,
    public readonly string $name,
    public readonly string $email
  ) {}
}
