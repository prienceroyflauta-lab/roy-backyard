<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductsApi extends Controller
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
        $products = $this->db
            ->table('products')
            ->order_by('id', 'DESC')
            ->get_all();

        $this->api->respond(['data' => $products]);
    }

    public function show($id)
    {
        $this->api->require_method('GET');
        $this->api->require_jwt();
        $product = $this->find_product($id);

        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }

        $this->api->respond(['data' => $product]);
    }

    public function store()
    {
        $this->api->require_method('POST');
        $this->api->require_jwt();
        $fields = $this->validated_product($this->api->body());
        $id = $this->db->table('products')->insert($fields);
        $this->api->respond(['message' => 'Product created.', 'data' => $this->find_product($id)], 201);
    }

    public function update($id)
    {
        $this->api->require_method($_SERVER['REQUEST_METHOD']);
        $this->api->require_jwt();
        $product = $this->find_product($id);

        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }

        $body = $this->api->body();
        if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            $body = array_merge([
                'product_name' => $product['product_name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
            ], $body);
        }
        $fields = $this->validated_product($body);
        $this->db->table('products')->where('id', '=', (int) $id)->update($fields);
        $this->api->respond(['message' => 'Product updated.', 'data' => $this->find_product($id)]);
    }

    public function destroy($id)
    {
        $this->api->require_method('DELETE');
        $this->api->require_jwt();
        $product = $this->find_product($id);

        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }

        $this->db->table('products')->where('id', '=', (int) $id)->delete();
        $this->api->respond(['message' => 'Product deleted.']);
    }

    private function find_product($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            $this->api->respond_error('Invalid product ID.', 400);
        }

        return $this->db->table('products')->where('id', '=', $id)->get();
    }

    private function validated_product(array $body)
    {
        $name = trim((string) ($body['product_name'] ?? ''));
        $description = trim((string) ($body['description'] ?? ''));
        $price = $body['price'] ?? null;
        $quantity = $body['quantity'] ?? null;

        if ($name === '' || mb_strlen($name) > 100) {
            $this->api->respond_error('Product name is required and must be at most 100 characters.', 422);
        }

        if (mb_strlen($description) > 10000) {
            $this->api->respond_error('Description must be at most 10,000 characters.', 422);
        }

        if (!is_numeric($price) || (float) $price < 0 || (float) $price > 99999999.99) {
            $this->api->respond_error('Price must be a non-negative amount up to 99,999,999.99.', 422);
        }

        $quantity = filter_var($quantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($quantity === false) {
            $this->api->respond_error('Quantity must be a non-negative whole number.', 422);
        }

        return [
            'product_name' => $name,
            'description' => $description,
            'price' => number_format((float) $price, 2, '.', ''),
            'quantity' => $quantity,
        ];
    }
}
