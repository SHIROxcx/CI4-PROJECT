<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEquipmentScheduleTable extends Migration
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
            'equipment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'event_date' => [
                'type' => 'DATE',
                'comment' => 'Date when equipment is scheduled',
            ],
            'total_quantity' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'Total available on this date',
            ],
            'booked_quantity' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'Quantity already booked',
            ],
            'available_quantity' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'Available for booking (total - booked)',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addKey('equipment_id');
        $this->forge->addKey('event_date');
        $this->forge->addKey(['equipment_id', 'event_date'], false, false, 'idx_equipment_date');
        $this->forge->createTable('equipment_schedule');

        // Add foreign key
        $this->db->query('ALTER TABLE equipment_schedule ADD CONSTRAINT fk_equipment_schedule 
                         FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->forge->dropTable('equipment_schedule');
    }
}
