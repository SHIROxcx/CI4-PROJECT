<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExtensionFilesTable extends Migration
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
            'extension_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'comment' => 'Relative path to the file',
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'payment_order, receipt, approval, etc',
            ],
            'uploaded_by_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'uploaded_by' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'deleted'],
                'default' => 'active',
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
        $this->forge->addForeignKey('extension_id', 'booking_extensions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('uploaded_by_id', 'users', 'id', 'SET NULL', 'SET NULL');

        $this->forge->addKey('extension_id');
        $this->forge->addKey('file_type');
        $this->forge->addKey('status');

        $this->forge->createTable('extension_files');
    }

    public function down()
    {
        $this->forge->dropTable('extension_files');
    }
}
