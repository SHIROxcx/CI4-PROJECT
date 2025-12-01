<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameBookingTypeEnum extends Migration
{
    public function up()
    {
        // Add 'faculty' to booking_type enum
        // Change from ('user','student') to ('user','student','faculty','external')
        $this->db->query("
            ALTER TABLE bookings 
            MODIFY booking_type ENUM('student', 'faculty', 'user', 'external') DEFAULT 'user'
        ");
    }

    public function down()
    {
        // Revert back to original enum
        $this->db->query("
            ALTER TABLE bookings 
            MODIFY booking_type ENUM('user', 'student') DEFAULT 'user'
        ");
    }
}
