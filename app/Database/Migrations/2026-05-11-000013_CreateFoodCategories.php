<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateFoodCategories extends Migration
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
        $this->forge->createTable('food_categories');
    }
    public function down()
    {
        $this->forge->dropTable('food_categories');
    }
}
