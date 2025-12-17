<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateFacilityForeignKey extends Migration
{
    public function up()
    {
        // Drop the existing foreign key constraint
        $this->forge->dropForeignKey('bookings', 'bookings_ibfk_1');

        // Add the new foreign key constraint with ON DELETE SET NULL
        $this->forge->addForeignKey('facility_id', 'facilities', 'id', '', 'SET NULL');
    }

    public function down()
    {
        // Drop the updated foreign key constraint
        $this->forge->dropForeignKey('bookings', 'bookings_ibfk_1');

        // Restore the original foreign key constraint (without ON DELETE SET NULL)
        $fields = [
            'facility_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ];
        $this->forge->modifyColumn('bookings', $fields);
        $this->forge->addForeignKey('facility_id', 'facilities', 'id');
    }
}
