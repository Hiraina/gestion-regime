<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateWallets extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'balance' => [
                'type' => 'DECIMAL',
                'constraint' => '19,4',
                'default' => 0.0000,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('wallets');
    }
    public function down()
    {
        $this->forge->dropTable('wallets');
    }
}
