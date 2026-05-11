<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateTransactions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'wallet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '19,4',
            ],
            'transaction_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('wallet_id', 'wallets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('transaction_type_id', 'transaction_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('transactions');
    }
    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}
