<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UsersApi extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('api');
        $this->call->database();
    }

    public function index()
    {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $users = $this->db->raw(
            'SELECT id, username, email, role, is_active, created_at, updated_at FROM users ORDER BY id DESC'
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->api->respond(['data' => $users]);
    }

    public function update($id)
    {
        $this->api->require_method('PUT');
        $actor = $this->api->require_jwt();
        $id = $this->validated_id($id);
        $user = $this->find_user($id);

        if (!$user) {
            $this->api->respond_error('User not found.', 404);
        }

        if ($user['role'] === 'admin' && ($actor['role'] ?? '') !== 'admin') {
            $this->api->respond_error('Admin access is required to update an admin account.', 403);
        }

        $body = $this->api->body();
        $fields = [];

        if (array_key_exists('username', $body)) {
            $username = trim((string) $body['username']);
            if ($username === '' || mb_strlen($username) > 100) {
                $this->api->respond_error('Username is required and must be at most 100 characters.', 422);
            }
            $fields['username'] = $username;
        }

        if (array_key_exists('email', $body)) {
            $email = trim((string) $body['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
                $this->api->respond_error('A valid email address is required.', 422);
            }
            $fields['email'] = $email;
        }

        if (array_key_exists('role', $body)) {
            $role = trim((string) $body['role']);
            if (($actor['role'] ?? '') !== 'admin') {
                if ($role !== $user['role']) {
                    $this->api->respond_error('Admin access is required to change user roles.', 403);
                }
            }
            if (!in_array($role, ['admin', 'moderator', 'user'], true)) {
                $this->api->respond_error('Role must be admin, moderator, or user.', 422);
            }
            if ($user['role'] === 'admin' && $role !== 'admin') {
                $this->require_another_active_admin($id);
            }
            $fields['role'] = $role;
        }

        if (!$fields) {
            $this->api->respond_error('At least one of username, email, or role is required.', 422);
        }

        $conflict = $this->db->raw(
            'SELECT id FROM users WHERE id != ? AND (username = ? OR email = ?) LIMIT 1',
            [
                $id,
                $fields['username'] ?? $user['username'],
                $fields['email'] ?? $user['email'],
            ]
        )->fetch(PDO::FETCH_ASSOC);

        if ($conflict) {
            $this->api->respond_error('Username or email is already in use.', 409);
        }

        $fields['updated_at'] = date('Y-m-d H:i:s');
        $this->db->table('users')->where('id', '=', $id)->update($fields);
        $this->api->respond(['message' => 'User updated.', 'data' => $this->find_user($id)]);
    }

    public function destroy($id)
    {
        $this->api->require_method('DELETE');
        $actor = $this->api->require_jwt();
        $id = $this->validated_id($id);
        $user = $this->find_user($id);

        if (!$user) {
            $this->api->respond_error('User not found.', 404);
        }

        if ($user['role'] === 'admin') {
            if (($actor['role'] ?? '') !== 'admin') {
                $this->api->respond_error('Admin access is required to delete an admin account.', 403);
            }
            $this->require_another_active_admin($id);
        }

        $this->db->raw('DELETE FROM refresh_tokens WHERE user_id = ?', [$id]);
        $this->db->table('users')->where('id', '=', $id)->delete();
        $this->api->respond(['message' => 'User deleted.']);
    }

    private function validated_id($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            $this->api->respond_error('Invalid user ID.', 400);
        }
        return $id;
    }

    private function find_user($id)
    {
        return $this->db->raw(
            'SELECT id, username, email, role, is_active, created_at, updated_at FROM users WHERE id = ? LIMIT 1',
            [$id]
        )->fetch(PDO::FETCH_ASSOC);
    }

    private function require_another_active_admin($excluded_id)
    {
        $admin = $this->db->raw(
            "SELECT id FROM users WHERE role = 'admin' AND is_active = 1 AND id != ? LIMIT 1",
            [$excluded_id]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $this->api->respond_error('At least one active admin account must remain.', 409);
        }
    }
}
