<?php
class UserController {
    private $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    // ✅ GET: Ambil semua user
    public function getAllUsers(): void {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, email FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($users) {
                echo json_encode($users);
            } else {
                echo json_encode(["message" => "No users found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // ✅ GET: Ambil user berdasarkan ID
    public function getUserById($id): void {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "User not found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // ✅ POST: Tambah user baru
    public function createUser(): void {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (empty($data["username"]) || empty($data["email"]) || empty($data["password"])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"]);
            return;
        }

        try {
            $hashedPassword = password_hash($data["password"], PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$data["username"], $data["email"], $hashedPassword]);

            http_response_code(201);
            echo json_encode(["message" => "User created successfully"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // ✅ PUT: Update user berdasarkan ID
    public function updateUser($id): void {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input - minimal satu field harus ada
        if (empty($data["username"]) && empty($data["email"]) && empty($data["password"])) {
            http_response_code(400);
            echo json_encode(["message" => "At least one field is required"]);
            return;
        }

        try {
            $updates = [];
            $params = [];

            if (!empty($data["username"])) {
                $updates[] = "username = ?";
                $params[] = $data["username"];
            }

            if (!empty($data["email"])) {
                $updates[] = "email = ?";
                $params[] = $data["email"];
            }

            if (!empty($data["password"])) {
                $updates[] = "password = ?";
                $params[] = password_hash($data["password"], PASSWORD_DEFAULT);
            }

            $params[] = $id;

            $query = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["message" => "User updated successfully"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "User not found or no changes made"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // ✅ DELETE: Hapus user berdasarkan ID
    public function deleteUser($id): void {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["message" => "User deleted successfully"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "User not found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}