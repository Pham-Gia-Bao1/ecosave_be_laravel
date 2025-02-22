<?php
namespace Database\Seeders;

use App\Models\PurchaseHistory;
use Illuminate\Database\Seeder;

class PurchaseHistorySeeder extends Seeder
{
    public function run(): void
    {
        PurchaseHistory::factory()->count(30)->create(); // Tạo 50 bản ghi mẫu
    }
}
