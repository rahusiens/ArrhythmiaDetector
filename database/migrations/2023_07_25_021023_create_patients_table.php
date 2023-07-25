<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('nama_depan');
            $table->string('nama_belakang');
            $table->string('alamat');
            $table->string('no_telp');
            $table->string('no_emergency');
            $table->string('kelamin');
            $table->string('umur');
            $table->string('dokter');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('patients')->insert([
            'nama_depan' => 'Pasien',
            'nama_belakang' => 'Satu',
            'alamat' => 'Bandung',
            'no_telp' => '081100001111',
            'no_emergency' => '081100001110',
            'kelamin' => 'P',
            'umur' => '21',
            'dokter' => 'doktersatu',
            'email' => 'pasiensatu@mail.com',
            'username' => 'pasiensatu',
            'password' => Hash::make('12345678'),
        ]);
        DB::table('patients')->insert([
            'nama_depan' => 'Pasien',
            'nama_belakang' => 'Dua',
            'alamat' => 'Bandung',
            'no_telp' => '081100002222',
            'no_emergency' => '081100002220',
            'kelamin' => 'L',
            'umur' => '22',
            'dokter' => 'doktersatu',
            'email' => 'pasiendua@mail.com',
            'username' => 'pasiendua',
            'password' => Hash::make('12345678'),
        ]);
        DB::table('patients')->insert([
            'nama_depan' => 'Pasien',
            'nama_belakang' => 'Tiga',
            'alamat' => 'Bekasi',
            'no_telp' => '081100003333',
            'no_emergency' => '081100003330',
            'kelamin' => 'L',
            'umur' => '20',
            'dokter' => 'dokterdua',
            'email' => 'pasientiga@mail.com',
            'username' => 'pasientiga',
            'password' => Hash::make('12345678'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
