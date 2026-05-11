<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateBodyMeasurements extends Migration
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
            'height' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'weight' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'created_at' => [
                'type' => 'DATE',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('body_measurements');
    }
    public function down()
    {
        $this->forge->dropTable('body_measurements');
    }
}
