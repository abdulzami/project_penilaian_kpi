<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Jabatan;
use App\Models\Struktural;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $stuktural = [
            [
                'nama_struktural' => 'Divisi',
            ]
        ];
        $bidang = [
            [
                'id_struktural' => 1,
                'nama_bidang' => 'Oil dan gas',
            ]
        ];
        $jabatan = [
            [
                'nama_jabatan' => 'General Manager',
                'id_bidang' => 1
            ],
            [
                'nama_jabatan' => 'Senior Manager',
                'id_bidang' => 1,
                'id_penilai' => 1
            ],
            [
                'nama_jabatan' => 'Staff',
                'id_bidang' => 1,
                'id_penilai' => 2
            ]
        ];
        $user = [
            [
                'npk' => '999999',
                'nama' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('adminbarata'),
                'status_user' => 'aktif',
                'level' => 'admin',
            ],
            [
                "npk"=> "000001",
                "nama"=> "Arif Rahman Hakim",
                "email"=> "arifrahmanhakim@gmail.com",
                'password' => bcrypt('adminbarata'),
                "status_user"=> "aktif",
                "level"=> "pegawai",
                "id_jabatan"=> 1,
            ],
            [
                "npk"=> "000002",
                "nama"=> "Shindi Purnama Putri",
                "email"=> "shindipp@gmail.com",
                'password' => bcrypt('adminbarata'),
                "status_user"=> "aktif",
                "level"=> "pegawai",
                "id_jabatan"=> 2,
            ],
            [
                "npk"=> "000003",
                "nama"=> "Abdul Aziz Zam Zami",
                "email"=> "abdulaziz@gmail.com",
                'password' => bcrypt('adminbarata'),
                "status_user"=> "aktif",
                "level"=> "pegawai",
                "id_jabatan"=> 3,
            ]
        ];
        foreach ($stuktural as $key => $value) {
            Struktural::create($value);
        }
        foreach ($bidang as $key => $value) {
            Bidang::create($value);
        }
        foreach ($jabatan as $key => $value) {
            Jabatan::create($value);
        }
        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
