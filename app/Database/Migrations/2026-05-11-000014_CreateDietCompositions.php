<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateDietCompositions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'diet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,4',
            ],
        ]);
        $this->forge->addKey(['diet_id', 'category_id'], true);
        $this->forge->addForeignKey('diet_id', 'diets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'food_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('diet_compositions');
    }
    public function down()
    {
        $this->forge->dropTable('diet_compositions');
    }
}
