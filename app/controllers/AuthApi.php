<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthApi extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('api');
        $this->call->database();
    }

    public function register()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit('auth-register', 5, 300);
        $body = $this->api->body();

        $username = trim((string) ($body['username'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($username === '' || mb_strlen($username) > 100) {
            $this->api->respond_error('Username is required and must be at most 100 characters.', 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            $this->api->respond_error('A valid email address is required.', 422);
        }

        if (strlen($password) < 8) {
            $this->api->respond_error('Password must be at least 8 characters.', 422);
        }

        $existing = $this->db->raw(
            'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1',
            [$username, $email]
        )->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $this->api->respond_error('Username or email is already registered.', 409);
        }

        $user_id = $this->db->table('users')->insert([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
        ]);

        $user = [
            'id' => $user_id,
            'username' => $username,
            'email' => $email,
            'role' => 'user',
        ];
        $tokens = $this->api->issue_tokens([
            'id' => $user_id,
            'role' => 'user',
            'scopes' => ['read', 'write', 'delete'],
        ]);

        $this->api->respond(['user' => $user, 'tokens' => $tokens], 201);
    }

    public function login()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit('auth-login', 10, 300);
        $body = $this->api->body();

        $identity = trim((string) ($body['identity'] ?? $body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($identity === '' || $password === '') {
            $this->api->respond_error('Username/email and password are required.', 422);
        }

        $stmt = $this->db->raw(
            'SELECT id, username, email, password, role FROM users WHERE (username = ? OR email = ?) AND is_active = 1 LIMIT 1',
            [$identity, $identity]
        );
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->api->respond_error('Invalid username/email or password.', 401);
        }

        $tokens = $this->api->issue_tokens([
            'id' => $user['id'],
            'role' => $user['role'],
            'scopes' => ['read', 'write', 'delete'],
        ]);

        unset($user['password']);
        $this->api->respond(['user' => $user, 'tokens' => $tokens]);
    }

    public function refresh()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit('auth-refresh', 20, 300);
        $body = $this->api->body();
        $refresh_token = trim((string) ($body['refresh_token'] ?? ''));

        if ($refresh_token === '') {
            $this->api->respond_error('Refresh token is required.', 422);
        }

        $this->api->refresh_access_token($refresh_token);
    }

    public function logout()
    {
        $this->api->require_method('POST');
        $body = $this->api->body();
        $refresh_token = trim((string) ($body['refresh_token'] ?? ''));

        if ($refresh_token === '') {
            $this->api->respond_error('Refresh token is required.', 422);
        }

        if (!$this->api->validate_jwt($refresh_token, 'refresh')) {
            $this->api->respond_error('Invalid refresh token.', 403);
        }

        $this->api->revoke_refresh_token($refresh_token);
        $this->api->respond(['message' => 'Logged out successfully.']);
    }

    public function me()
    {
        $payload = $this->api->require_jwt();
        $stmt = $this->db->raw(
            'SELECT id, username, email, role FROM users WHERE id = ? AND is_active = 1 LIMIT 1',
            [$payload['sub']]
        );
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->api->respond_error('Unauthorized.', 401);
        }

        $this->api->respond(['user' => $user]);
    }
}
