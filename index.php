<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\http\Response;

$loader = new Loader();

$loader->registerNamespaces(
  [
    'Store\Toys' => __DIR__ . '/models/',
  ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
  'db',
  function () {
    return new PdoMysql(
      [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'patients',
      ]
    );
  }
);


$app = new Micro($container);

// Define The Route Here
$app->get(
  '/api/patients',
  function () use ($app) {
    $phql = 'SELECT * FROM `patients` ORDER BY name';

    $patients = $app->modelsManager->executeQuery($phql);

    $data = [];

    foreach ($patients as $patient) {
      $data[] = [
        'id' => $patient->id,
        'name' => $patient->name,
        'sex' => $patient->sex,
        'religion' => $patient->religion,
        'phone' => $patient->phone,
        'address' => $patient->address,
        'nik' => $patient->nik,
      ];
    }
    echo json_encode($data);
  }
);

$app->get(
  '/api/patients/search/{name}',
  function ($name) use ($app) {
    $phql = 'SELECT * FROM `patients` WHERE name LIKE :name: ORDER BY name';
    $patients = $app->modelsManager->executeQuery(
      $phql,
      [
        'name' => '%' . $name . '%'
      ]
    );
    $data = [];

    foreach($patients as $patient) {
      $data[] = [
        'id' => $patient->id,
        'name' => $patient->name,
        'sex' => $patient->sex,
        'religion' => $patient->religion,
        'phone' => $patient->phone,
        'address' => $patient->address,
        'nik' => $patient->nik,
      ];
    }
    echo json_encode($data);
  }
);

$app->get(
  '/api/patients/{id:[0-9]+}',
  function ($id) use ($app) {
    $phql = 'SELECT * FROM `patients` WHERE id = :id:';
    $patients = $app->modelsManager->executeQuery(
      $phql,
      [
        'id' => $id,
      ]
    )->getFirst();

    //Create Response
    $response = new Response();
    if ($patients == false) {
      $response->setJsonContent(
        [
          'status' => 'NOT-FOUND'
        ]
      );
    } else {
      $response->setJsonContent(
        [
          'status' => 'FOUND',
          'data' => [
            'id' => $patients->id,
            'name' => $patients->name,
            'sex' => $patients->sex,
            'religion' => $patients->religion,
            'phone' => $patients->phone,
            'address' => $patients->address,
            'nik' => $patients->nik,
          ]
        ]
      );
    }

    return $response;
  }
);

$app->post(
  '/api/patients',
  function () use ($app) {
    $patient = $app->request->getJsonRawBody();

    $phql = 'INSERT INTO `patients` (name, type, year) Values (:name:, :type:, :year:)';

    $status = $app->modelsManager->executeQuery(
      $phql,
      [
        'id' => $patient->id,
        'name' => $patient->name,
        'sex' => $patient->sex,
        'religion' => $patient->religion,
        'phone' => $patient->phone,
        'address' => $patient->address,
        'nik' => $patient->nik,
      ]
    );
    $response = new Response();

    if( status->success() === true ) {
      $response->setStatusCode(201, 'Created');
      $patient->id = $status->getModel()->id;
      $response->setJsonContent(
        [
          'status' => 'OK',
          'data' => $patient,
        ]
      );
    }else{
      $response->setStatusCode(409, 'Conflict');
      $error = [];
      foreach ($status->getMessages() as $message) {
        $error[] = $message->getMessage();
      }
      $response->setJsonContent(
        [
          'status' => 'ERROR',
          'message' => $error,
        ]
      );
    }
    return $response;
  }
);

$app->put(
  '/api/patients/{id:[0-9]+}',
  function($id) use ($app) {
    $patient = $app->request->getJsonRawBody();

    $phql = 'UPDATE `patients` SET name = :name:, type = :type:, year = :year: WHERE id = :id:';

    $status = $app->modelManager->executeQuery(
      $phql,
      [
        'id' => $id,
        'name' => $patient->name,
        'sex' => $patient->sex,
        'religion' => $patient->religion,
        'phone' => $patient->phone,
        'address' => $patient->address,
        'nik' => $patient->nik,
      ]
    );
    $response = new Response();
    if($status->success() === true) {
      $response->setJsonContent(
        [
          'status' => 'OK'
        ]
      );
    } else {
      $response->setStatusCode(409, 'Conflict');
      $error = [];
      foreach ($status->getMessages() as $message) {
        $error[] = $message->getMessage();
      }
      $response->setJsonContent(
        [
          'status' => 'ERROR',
          'message' => $error,
        ]
      );
    }
    return $response;
  }
);

$app->delete(
  '/api/robots/{id:[0-9]+}',
  function ($id) use ($app) {
    $phql = 'DELETE FROM `robots` WHERE id = :id:';
    $status = $app->modelsManager->executeQuery(
      $phql,
      [
        'id' => $id,
      ]
    );
    $response = new Response();
    if($status->success() === true) {
      $response->setJsonContent(
        [
          'status' => 'OK'
        ]
      );
    } else {
      $response->setStatusCode(409, 'Conflict');
      $error = [];
      foreach($status->getMessages() as $message) {
        $error[] = $message->getMessage();

        $response->setJsonContent(
          [
            'status' => 'ERROR',
            'message' => $error,
          ]
        );
        return $response;
      }
    }
  }
);

$app->handle(
  $_SERVER["REQUEST_URI"]
);