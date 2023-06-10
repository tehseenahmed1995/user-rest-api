<?php

use App\Config\Db;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Faker\Factory as FakerFactory;


// require_once __DIR__ . '/../middleware/dataValidator.php';

/**
 * Route to get all users
 * @param Request $request
 * @param Response $response
 * 
 * @return json json
 */

$app->get('/users', function (Request $request, Response $response) {
  $queryParams = $request->getQueryParams();
  // Default to page 1 if not provided
  $page = trim($queryParams['page']) ?? 1;
  // Default to 30 records per page if not provided
  $perPage = trim($queryParams['per_page']) ?? 30;
  // Calculate the OFFSET based on the page and perPage values
  $offset = ($page - 1) * $perPage;

  $sql = "SELECT id,name,email FROM users ORDER BY id LIMIT :perPage OFFSET :offset";

  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->prepare($sql);
 
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db = null;

    $response->getBody()->write(json_encode(['data' => $users]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
    //todo - need to log error message for debugging
    $response->getBody()->write(json_encode(["message" => "Error while fetching users"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});

/**
 * Route to get single user
 * @param Request $request
 * @param Response $response
 * 
 * @return json json
 */

$app->get('/user/{id}', function (Request $request, Response $response, array $args) {
  $id = $request->getAttribute('id');
  $sql = "SELECT id,name,email FROM users WHERE id=$id";

  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $user = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    $response->getBody()->write(json_encode(['data' => $user]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    //todo - need to log error message for debugging
    $response->getBody()->write(json_encode(["message" => "Error while fetching user"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});

/**
 * Route to add new user
 * @param Request $request
 * @param Response $response
 * @param array $args
 * 
 * @return json json
 */
$app->post('/user/add', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();
  $name = $data["name"];
  $email = $data["email"];

  $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";

  try {
    $db = new Db();
    $conn = $db->connect();

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);


    $result = $stmt->execute();

    $db = null;
    $response->getBody()->write(json_encode(["message" => "User created successfully"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    //todo - need to log error message for debugging

    $response->getBody()->write(json_encode(["message" => "Error while creating user"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});


/**
 * Route to update user
 * @param Request $request
 * @param Response $response
 * @param array $args
 * 
 * @return json json
 */
$app->put('/user/{id}', function (Request $request, Response $response, array $args) {
  $id = $request->getAttribute('id');
  $data = $request->getParsedBody();
  $name = $data["name"];
  $email = $data["email"];

  $sql = "UPDATE users SET
            name = :name,
            email = :email
  WHERE id = $id";

  try {
    $db = new Db();
    $conn = $db->connect();

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);

    $result = $stmt->execute();

    $db = null;

    $response->getBody()->write(json_encode(["message" => "User updated successfully"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
    //todo - need to log error message for debugging
    $response->getBody()->write(json_encode(["message" => "Error while updating user"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});

/**
 * Route to delete user
 * @param Request $request
 * @param Response $response
 * @param array $args
 * 
 * @return json json
 */
$app->delete('/user/{id}', function (Request $request, Response $response, array $args) {
  $id = $request->getAttribute('id');
  $sql = "DELETE FROM users WHERE id = $id";

  try {
    $db = new Db();
    $conn = $db->connect();

    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();

    $db = null;
    $response->getBody()->write(json_encode(["message" => "User deleted successfully"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode(["message" => "Error while deleting user"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});


/**
 * Route to add fake user records
 * @param Request $request
 * @param Response $response
 * 
 * @return json json
 */
$app->post('/user/add-fake-records', function (Request $request, Response $response) {
  $faker = FakerFactory::create();
  $numRecords = 500;
  $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";

  try {
    $db = new Db();
    $conn = $db->connect();

    $stmt = $conn->prepare($sql);

    for ($i = 0; $i < $numRecords; $i++) {
      $name = $faker->name();
      $email = $faker->email();

      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':email', $email);

      $stmt->execute();
    }

    $response->getBody()->write(json_encode(["message" => "Users created successfully"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    //todo - need to log error message for debugging

    $response->getBody()->write(json_encode(["message" => "Error while creating users"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});


/**
 * Route to add fake records in locations and transactions
 * @note make sure to set max_execution_time = 500 in php.ini to avoid fatel error as we need to insert 100K records in DB
 * @param Request $request
 * @param Response $response
 *
 * @return json json
 */
$app->post('/location-transaction/add-fake-records', function (Request $request, Response $response) {
  $faker = FakerFactory::create();
  $numRecords = 100000;
  $sqlTransactions = "INSERT INTO transactions (user_id, amount, transaction_date) VALUES (:user_id, :amount, :transaction_date)";
  $sqlLocations = "INSERT INTO locations (user_id, location) VALUES (:user_id, :location)";

  try {
    $db = new Db();
    $conn = $db->connect();

    $stmtTransactions = $conn->prepare($sqlTransactions);
    $stmtLocations = $conn->prepare($sqlLocations);

    for ($i = 0; $i < $numRecords; $i++) {

      $user_id = $faker->numberBetween(1, 500);
      $amount = $faker->randomFloat(2, 0, 1000);
      $transaction_date = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
      $location = $faker->city;

      // Bind parameters and execute for transactions table
      $stmtTransactions->bindParam(':user_id', $user_id);
      $stmtTransactions->bindParam(':amount', $amount);
      $stmtTransactions->bindParam(':transaction_date', $transaction_date);
      $stmtTransactions->execute();

      // Bind parameters and execute for locations table
      $stmtLocations->bindParam(':user_id', $user_id);
      $stmtLocations->bindParam(':location', $location);
      $stmtLocations->execute();
    }

    $response->getBody()->write(json_encode(["message" => "Transactions and locations created successfully"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    //todo - need to log error message for debugging

    $response->getBody()->write(json_encode(["message" => "Error while creating transactions and locations"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});

/**
 * Route to get user transactions based on location in specific date
 * @param Request $request
 * @param Response $response
 * 
 * @return json json
 */
$app->post('/user/location-transactions', function (Request $request, Response $response) {

  $queryParams = $request->getQueryParams();
  // Default to page 1 if not provided
  $page = trim($queryParams['page']) ?? 1;
  // Default to 30 records per page if not provided
  $perPage = trim($queryParams['per_page']) ?? 30;
  // Calculate the OFFSET based on the page and perPage values
  $offset = ($page - 1) * $perPage;

  $data = $request->getParsedBody();

  $location = $data["location"];
  $date = $data["date"];

  $sql = "SELECT u.id,u.name,l.location,t.transaction_date,COUNT(*) as total_transactions
          FROM transactions t
          JOIN users u ON t.user_id = u.id
          JOIN locations l ON u.id = l.user_id
          WHERE l.location = :location
          AND t.transaction_date = :date
          GROUP BY u.id, u.name, l.location, t.transaction_date
          ORDER BY u.id
          LIMIT :perPage OFFSET :offset";

  try {
    $db = new Db();
    $conn = $db->connect();

    $stmt = $conn->prepare($sql);
    

    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    $response->getBody()->write(json_encode(['data' => $result]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
    //todo - need to log error message for debugging
    $response->getBody()->write(json_encode(["message" =>  "Error while fetching user transactions and locations"]));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});
