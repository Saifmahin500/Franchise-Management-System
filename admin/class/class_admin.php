<?php
class Admin
{
    private $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function login(string $email, string $password): array
    {
        $stmt = $this->db->prepare("SELECT id,username,email,password_hash,role,status FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if (!$u) return ['ok' => false, 'err' => 'User not found'];
        if ((int)$u['status'] !== 1) return ['ok' => false, 'err' => 'Account disabled'];

        if (!password_verify($password, $u['password_hash'])) {
            return ['ok' => false, 'err' => 'Invalid email or password'];
        }

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_id']   = (int)$u['id'];
        $_SESSION['user_name'] = $u['username'];
        $_SESSION['user_role'] = $u['role'];

        // ব্রাঞ্চ ম্যানেজারের ডিফল্ট ব্রাঞ্চ (থাকলে) সেট করো
        $_SESSION['branch_id'] = null;
        if ($u['role'] === 'branch_manager') {
            $q = $this->db->prepare("SELECT branch_id FROM user_branches WHERE user_id = ? LIMIT 1");
            $q->execute([$u['id']]);
            $m = $q->fetch();
            if ($m) $_SESSION['branch_id'] = (int)$m['branch_id'];
        }

        return ['ok' => true];
    }

    public static function requireLogin()
    {
        if (empty($_SESSION['admin_logged_in'])) {
            header('Location: /franchise_management/admin/login.php');
            exit;
        }
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
    }
}
