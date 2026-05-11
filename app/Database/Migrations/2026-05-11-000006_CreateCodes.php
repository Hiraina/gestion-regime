<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateCodes extends Migration
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
            'code_value' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '19,4',
            ],
            'is_used' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('codes');
    }
    public function down()
    {
        $this->forge->dropTable('codes');
    }
}
