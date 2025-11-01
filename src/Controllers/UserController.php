<?php
class UserController {
    private PDO $database;

    public function __construct(PDO $database) {
        $this->database = $database;
    }

    public function index(): void {
        try {
            $statement = $this->database->query('SELECT id, name FROM users ORDER BY id');
            $users = $statement->fetchAll();
            $this->respond(true, $users);
        } catch (PDOException $exception) {
            http_response_code(500);
            $this->respond(false, null, 'Database error: ' . $exception->getMessage());
        }
    }

    public function show(string $id): void {
        try {
            $statement = $this->database->prepare('SELECT id, name FROM users WHERE id = :id LIMIT 1');
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $user = $statement->fetch();

            if ($user === false) {
                http_response_code(404);
                $this->respond(false, null, 'User not found');
                return;
            }

            $this->respond(true, $user);
        } catch (PDOException $exception) {
            http_response_code(500);
            $this->respond(false, null, 'Database error: ' . $exception->getMessage());
        }
    }

    private function respond(bool $success, $data = null, string $message = ''): void {
        $response = ['success' => $success];

        if ($message !== '') {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
    }
}
?>