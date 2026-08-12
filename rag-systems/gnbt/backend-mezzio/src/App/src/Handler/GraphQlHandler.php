<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;

class GraphQLHandler implements RequestHandlerInterface
{
  private static $users = [
    ['id' => 1, 'name' => 'Arch Linux', 'email' => 'arch@linux.org'],
    ['id' => 2, 'name' => 'N8N Master', 'email' => 'admin@n8n.io']
  ];

  public function handle(ServerRequestInterface $request): ResponseInterface
  {
    $input = json_decode($request->getBody()->getContents(), true);
    $query = $input['query'] ?? null;
    $variables = $input['variables'] ?? null;

    $userType = new ObjectType([
      'name' => 'User',
      'fields' => [
        'id'    => Type::nonNull(Type::int()),
        'name'  => Type::string(),
        'email' => Type::string(),
      ]
    ]);

    $queryType = new ObjectType([
      'name' => 'Query',
      'fields' => [
        'users' => [
          'type' => Type::listOf($userType),
          'resolve' => function () {
            return self::$users;
          }
        ]
      ]
    ]);

    $mutationType = new ObjectType([
      'name' => 'Mutation',
      'fields' => [
        'createUser' => [
          'type' => $userType,
          'args' => [
            'name'  => Type::nonNull(Type::string()),
            'email' => Type::nonNull(Type::string()),
          ],
          'resolve' => function ($rootValue, array $args) {
            $newUser = [
              'id'    => count(self::$users) + 1,
              'name'  => $args['name'],
              'email' => $args['email'],
            ];
            self::$users[] = $newUser;
            return $newUser;
          }
        ],
        'deleteUser' => [
          'type' => Type::string(), 
          'args' => [
            'id' => Type::nonNull(Type::int())
          ],
          'resolve' => function ($rootValue, array $args) {
            foreach (self::$users as $key => $user) {
              if ($user['id'] === $args['id']) {
                unset(self::$users[$key]);
                return "Usuario eliminado con éxito";
              }
            }
            return "Usuario no encontrado";
          }
        ]
      ]
    ]);

    // 5. Armar el Schema y Ejecutar
    $schema = new Schema([
      'query' => $queryType,
      'mutation' => $mutationType
    ]);

    try {
      $result = GraphQL::executeQuery($schema, $query, null, null, $variables);
      $output = $result->toArray();
    } catch (\Exception $e) {
      $output = ['errors' => [['message' => $e->getMessage()]]];
    }

    // 6. Configurar headers CORS para Angular
    return (new JsonResponse($output))
      ->withHeader('Access-Control-Allow-Origin', '*')
      ->withHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
      ->withHeader('Access-Control-Allow-Headers', 'Content-Type');
  }
}
