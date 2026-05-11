<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateRecommendations extends Migration
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
            'template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'generated_at' => [
                'type' => 'DATETIME',
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'trigger_measurement_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('template_id', 'plan_templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('trigger_measurement_id', 'body_measurements', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recommendations');
    }
    public function down()
    {
        $this->forge->dropTable('recommendations');
    }
}
