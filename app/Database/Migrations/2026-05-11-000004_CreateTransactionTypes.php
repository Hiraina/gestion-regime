<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateTransactionTypes extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('transaction_types');
    }
    public function down()
    {
        $this->forge->dropTable('transaction_types');
    }
}
