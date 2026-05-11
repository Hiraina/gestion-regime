<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateTemplateActivities extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'activity_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey(['template_id', 'activity_id'], true);
        $this->forge->addForeignKey('template_id', 'plan_templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('activity_id', 'activities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('template_activities');
    }
    public function down()
    {
        $this->forge->dropTable('template_activities');
    }
}
