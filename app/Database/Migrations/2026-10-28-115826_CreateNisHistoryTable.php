<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNisHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'old_nis' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'new_nis' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'changed_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('siswa_id');
        $this->forge->addKey('old_nis');
        $this->forge->addKey('new_nis');
        $this->forge->createTable('nis_history', true);
    }

    public function down()
    {
        $this->forge->dropTable('nis_history', true);
    }
}
