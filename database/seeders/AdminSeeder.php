<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Request $request)
    {
        //
        Admin::create([
            'name' => "المشرف الاساسي",
            'email' => "admin@admin.com",
            'phone' => "0500000000",
            'id_number' => '999999999',
            'image' => '999999999',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);
    }
}
