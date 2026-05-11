<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateDietDurationPricings extends Migration
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
            'diet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'duration_days' => [
                'type' => 'INT',
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '19,4',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('diet_id', 'diets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('diet_duration_pricings');
    }
    public function down()
    {
        $this->forge->dropTable('diet_duration_pricings');
    }
}
