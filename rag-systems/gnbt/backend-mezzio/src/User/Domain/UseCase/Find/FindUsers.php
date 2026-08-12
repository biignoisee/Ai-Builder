<?php

namespace User\Domain\UseCase\Find;

use User\Domain\Entity\User;

class FindUsers
{
  public function execute(): array
  {
    return [
      new User(1, 'Arch Linux', 'arch@example.com'),
      new User(2, 'N8N Master', 'admin@example.com')
    ];
  }
}
