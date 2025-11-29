<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingExtensionsTable extends Migration
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
            'extension_hours' => [
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => true,
                'comment' => 'Number of additional hours requested',
            ],
            'extension_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Cost for the extension (hours * hourly_rate)',
            ],
            'extension_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Reason for requesting extension',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected', 'completed'],
                'default' => 'pending',
                'comment' => 'Status of the extension request',
            ],
            'requested_by_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'requested_by' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Name of the person requesting extension',
            ],
            'requested_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When the extension was requested',
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID of admin/facilitator who approved',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When the extension was approved',
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'received', 'waived'],
                'default' => 'pending',
                'comment' => 'Payment status for the extension',
            ],
            'payment_order_generated' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'comment' => 'Whether a payment order has been generated',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new \DateTime(),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new \DateTime(),
            ],
        ]);

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('requested_by_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'SET NULL');

        $this->forge->addKey('status');
        $this->forge->addKey('payment_status');
        $this->forge->addKey('requested_by_id');
        $this->forge->addKey('approved_by');

        $this->forge->createTable('booking_extensions');
    }

    public function down()
    {
        $this->forge->dropTable('booking_extensions');
    }
}
