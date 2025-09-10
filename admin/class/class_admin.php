<?php
class Admin
{
    private $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $email, string $password): array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, password_hash, role, status 
                                    FROM users 
                                    WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if (!$u) return ['ok' => false, 'err' => 'User not found'];
        if ((int)$u['status'] !== 1) return ['ok' => false, 'err' => 'Account disabled'];

        if (!password_verify($password, $u['password_hash'])) {
            return ['ok' => false, 'err' => 'Invalid email or password'];
        }

        // ✅ Session set
        $_SESSION = []; // পুরানো সেশন ক্লিয়ার করে নতুন দিচ্ছি
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id']   = (int)$u['id'];
        $_SESSION['user_email'] = $u['email'];
        $_SESSION['user_name'] = $u['username'];
        $_SESSION['user_role'] = strtolower($u['role']); // admin / manager / staff

        return ['ok' => true];
    }

    public static function requireLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['logged_in'])) {
            header('Location: ../login.php');
            exit;
        }
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }
}
