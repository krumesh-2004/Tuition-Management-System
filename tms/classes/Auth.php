<?php
require_once __DIR__ . '/Admin.php';
require_once __DIR__ . '/Student.php';
require_once __DIR__ . '/Tutor.php';

/**
 * Auth
 * Central authentication/authorisation helper (FR-01 / UC-01, FR-12 / UC-09).
 * Tries Admin, then Tutor, then Student tables for a matching email/password,
 * since the SRS allows any of the three actors to log in from one form.
 */
class Auth
{
    public static function attemptLogin(string $email, string $password): array
    {
        $admin = new Admin();
        if ($row = $admin->login($email, $password)) {
            self::createSession('admin', $row);
            return ['success' => true, 'role' => 'admin'];
        }

        $tutor = new Tutor();
        if ($row = $tutor->login($email, $password)) {
            self::createSession('tutor', $row);
            return ['success' => true, 'role' => 'tutor'];
        }

        $student = new Student();
        if ($row = $student->login($email, $password)) {
            if (($row['status'] ?? 'Active') !== 'Active') {
                return ['success' => false, 'message' => 'Your account is inactive. Please contact the administration.'];
            }
            self::createSession('student', $row);
            return ['success' => true, 'role' => 'student'];
        }

        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    private static function createSession(string $role, array $row): void
    {
        session_regenerate_id(true);
        $_SESSION['role']       = $role;
        $_SESSION['user_id']    = $row[$role === 'admin' ? 'admin_id' : ($role === 'tutor' ? 'tutor_id' : 'student_id')];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_name']  = $role === 'student' ? ($row['first_name'] . ' ' . $row['last_name']) : $row['name'];
        $_SESSION['photo_path'] = $row['photo_path'] ?? null;
        $_SESSION['last_activity'] = time();
    }

    public static function isLoggedIn(): bool
    {
        if (!isset($_SESSION['role'], $_SESSION['user_id'])) return false;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
            self::logout();
            return false;
        }
        $_SESSION['last_activity'] = time();
        return true;
    }

    public static function requireRole(string $role): void
    {
        if (!self::isLoggedIn() || $_SESSION['role'] !== $role) {
            header('Location: ' . self::loginUrl());
            exit;
        }
    }

    public static function loginUrl(): string
    {
        return self::basePathPrefix() . 'index.php';
    }

    /** Works out relative prefix ("" at root, "../" inside admin/student/tutor folders) */
    public static function basePathPrefix(): string
    {
        $script = $_SERVER['SCRIPT_NAME'];
        return (strpos($script, '/admin/') !== false || strpos($script, '/student/') !== false || strpos($script, '/tutor/') !== false)
            ? '../' : '';
    }

    public static function dashboardUrlForRole(string $role): string
    {
        return match ($role) {
            'admin'   => 'admin/dashboard.php',
            'tutor'   => 'tutor/dashboard.php',
            'student' => 'student/dashboard.php',
            default   => 'index.php',
        };
    }

    public static function logout(): void
    {
        User::logout();
    }
}
