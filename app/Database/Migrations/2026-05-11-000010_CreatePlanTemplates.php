<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreatePlanTemplates extends Migration
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
            'goal_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'imc_min' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'imc_max' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'duration' => [
                'type' => 'INT',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('goal_id', 'goals', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('plan_templates');
    }
    public function down()
    {
        $this->forge->dropTable('plan_templates');
    }
}
