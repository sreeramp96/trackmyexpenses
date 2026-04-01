<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Income
            ['name' => 'Salary', 'type' => 'income', 'icon' => 'briefcase', 'color' => '#22c55e'],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => 'laptop', 'color' => '#16a34a'],
            ['name' => 'Investment', 'type' => 'income', 'icon' => 'trending-up', 'color' => '#15803d'],
            ['name' => 'Other Income', 'type' => 'income', 'icon' => 'plus', 'color' => '#4ade80'],

            // Expense
            ['name' => 'Food & Dining', 'type' => 'expense', 'icon' => 'utensils', 'color' => '#f97316'],
            ['name' => 'Transport', 'type' => 'expense', 'icon' => 'car', 'color' => '#fb923c'],
            ['name' => 'Shopping', 'type' => 'expense', 'icon' => 'shopping-bag', 'color' => '#f43f5e'],
            ['name' => 'Bills & Utilities', 'type' => 'expense', 'icon' => 'zap', 'color' => '#eab308'],
            ['name' => 'Health', 'type' => 'expense', 'icon' => 'heart', 'color' => '#ef4444'],
            ['name' => 'Entertainment', 'type' => 'expense', 'icon' => 'film', 'color' => '#a855f7'],
            ['name' => 'Education', 'type' => 'expense', 'icon' => 'book-open', 'color' => '#3b82f6'],
            ['name' => 'Rent & Housing', 'type' => 'expense', 'icon' => 'home', 'color' => '#64748b'],
            ['name' => 'Other Expense', 'type' => 'expense', 'icon' => 'minus', 'color' => '#94a3b8'],

            // Transfer
            ['name' => 'Account Transfer', 'type' => 'transfer', 'icon' => 'repeat', 'color' => '#06b6d4'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name'], 'user_id' => null],
                $cat
            );
        }
    }
}
