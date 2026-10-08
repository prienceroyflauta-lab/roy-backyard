<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class HealthApi extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('api');
        handle_cors();
        $this->call->database();
    }

    public function index()
    {
        $database_ok = (string) $this->db->raw('SELECT 1')->fetchColumn() === '1';
        $status = $database_ok ? 200 : 503;

        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        http_response_code($status);
        echo json_encode([
            'status' => $database_ok ? 'ok' : 'error',
            'database' => $database_ok ? 'ok' : 'unavailable',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
