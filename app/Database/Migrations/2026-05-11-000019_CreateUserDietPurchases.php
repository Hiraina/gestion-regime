<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateUserDietPurchases extends Migration
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
            'diet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'duration_days' => [
                'type' => 'INT',
            ],
            'price_paid' => [
                'type' => 'DECIMAL',
                'constraint' => '19,4',
            ],
            'discount_applied' => [
                'type' => 'DECIMAL',
                'constraint' => '19,4',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('diet_id', 'diets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_diet_purchases');
    }
    public function down()
    {
        $this->forge->dropTable('user_diet_purchases');
    }
}
