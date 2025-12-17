<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingSurveyResponsesTable extends Migration
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
            'booking_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'survey_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'unique' => true,
            ],
            // STAFF SECTION
            'staff_punctuality' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'staff_courtesy_property' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'staff_courtesy_audio' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'staff_courtesy_janitor' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            // FACILITY SECTION
            'facility_level_expectations' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'facility_cleanliness_function_hall' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'facility_cleanliness_classrooms' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'facility_cleanliness_restrooms' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'facility_cleanliness_reception' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            // EQUIPMENT FUNCTION
            'equipment_airconditioning' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_lighting' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_electric_fans' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_tables' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_monobloc_chairs' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_chair_cover' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_podium' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_multimedia_projector' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_sound_system' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_microphone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'equipment_others' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            // OVERALL EXPERIENCE
            'overall_would_rent_again' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'overall_would_recommend' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'overall_how_found_facility' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            // COMMENTS
            'comments_suggestions' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('booking_id');
        $this->forge->addKey('survey_token');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('booking_survey_responses');
    }

    public function down()
    {
        $this->forge->dropTable('booking_survey_responses');
    }
}
