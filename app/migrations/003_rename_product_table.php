<?php

class Rename_product_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        $legacy_exists = $this->_lava->dbforge->table_exists('product');
        $products_exists = $this->_lava->dbforge->table_exists('products');

        if ($legacy_exists && $products_exists) {
            throw new RuntimeException('Both product and products tables exist. Reconcile their data before running this migration.');
        }

        if ($legacy_exists) {
            $this->_lava->dbforge->rename_table('product', 'products');
            return;
        }

        if ($products_exists) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'id' => [
                    'type' => 'INT',
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                    'null' => FALSE,
                ],
                'product_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => FALSE,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => FALSE,
                    'default' => '0.00',
                ],
                'quantity' => [
                    'type' => 'INT',
                    'null' => FALSE,
                    'default' => 0,
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => FALSE,
                    'default' => 'CURRENT_TIMESTAMP',
                ],
            ])
            ->add_key('id', primary: TRUE)
            ->create_table('products');
    }

    public function down()
    {
        $legacy_exists = $this->_lava->dbforge->table_exists('product');
        $products_exists = $this->_lava->dbforge->table_exists('products');

        if ($legacy_exists && $products_exists) {
            throw new RuntimeException('Both product and products tables exist. Reconcile their data before rolling back this migration.');
        }

        if ($products_exists) {
            $this->_lava->dbforge->rename_table('products', 'product');
        }
    }
}
