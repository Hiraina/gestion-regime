<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateTemplateDiets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'diet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey(['template_id', 'diet_id'], true);
        $this->forge->addForeignKey('template_id', 'plan_templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('diet_id', 'diets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('template_diets');
    }
    public function down()
    {
        $this->forge->dropTable('template_diets');
    }
}
