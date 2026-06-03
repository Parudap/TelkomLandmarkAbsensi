<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bidangList = [
            [ 'nama_bidang' => 'Operation', 'is_active' => true ],
            [ 'nama_bidang' => 'GA & Corcom', 'is_active' => true ],
            [ 'nama_bidang' => 'Financial Accounting', 'is_active' => true ],
            [ 'nama_bidang' => 'IT & Digitization', 'is_active' => true ],
            [ 'nama_bidang' => 'Engineering', 'is_active' => true ],
            [ 'nama_bidang' => 'Business Development & Performance Management', 'is_active' => true ],
            [ 'nama_bidang' => 'Procurement', 'is_active' => true ],
            [ 'nama_bidang' => 'Financial & Risk Management', 'is_active' => true ],
            [ 'nama_bidang' => 'Human Capital Management', 'is_active' => true ],
            [ 'nama_bidang' => 'Engineering / Mechanical Electrical', 'is_active' => true ],
            [ 'nama_bidang' => 'Quality Assurance Services Management', 'is_active' => true ],
        ];

        foreach ($bidangList as $bidang) {
            Bidang::create($bidang);
        }
    }
}
