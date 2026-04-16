<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Debt;
use App\Models\RecurringExpense;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 2;
        $now = Carbon::now();

        // 1. Ensure User exists or update details
        $user = User::updateOrCreate(
            ['id' => $userId],
            [
                'name' => 'demo',
                'email' => 'demo@demo.com',
                'currency' => 'INR',
                'timezone' => 'Asia/Kolkata',
                'password' => bcrypt('password'), 
            ]
        );

        // Clear existing demo data
        Transaction::where('user_id', $userId)->forceDelete();
        Debt::where('user_id', $userId)->forceDelete();
        Account::where('user_id', $userId)->forceDelete();
        Category::where('user_id', $userId)->forceDelete();
        RecurringExpense::where('user_id', $userId)->forceDelete();

        // 2. Seed Categories
        $categories = [
            'Salary' => 'income',
            'Food & Dining' => 'expense',
            'Rent' => 'expense',
            'Utilities' => 'expense',
            'Entertainment' => 'expense',
            'Transport' => 'expense',
            'Shopping' => 'expense',
            'Health' => 'expense',
            'Miscellaneous' => 'expense',
        ];

        $categoryMap = [];
        foreach ($categories as $name => $type) {
            $cat = Category::create([
                'user_id' => $userId,
                'name' => $name,
                'type' => $type,
            ]);
            $categoryMap[$name] = $cat->id;
        }

        // 3. Seed Accounts
        $bank = Account::create([
            'user_id' => $userId,
            'name' => 'HDFC Savings',
            'type' => 'bank',
            'balance' => 0, 
            'currency' => 'INR',
            'is_active' => true,
        ]);

        $cash = Account::create([
            'user_id' => $userId,
            'name' => 'Cash',
            'type' => 'cash',
            'balance' => 0,
            'currency' => 'INR',
            'is_active' => true,
        ]);

        $cc = Account::create([
            'user_id' => $userId,
            'name' => 'ICICI Amazon Pay CC',
            'type' => 'credit_card',
            'balance' => 0,
            'currency' => 'INR',
            'is_active' => true,
        ]);

        // 4. Seed Recurring Expenses
        RecurringExpense::create([
            'user_id' => $userId,
            'category_id' => $categoryMap['Entertainment'],
            'name' => 'Netflix Premium',
            'amount' => 649,
            'frequency' => 'monthly',
            'due_day' => 1,
            'is_active' => true,
        ]);

        RecurringExpense::create([
            'user_id' => $userId,
            'category_id' => $categoryMap['Rent'],
            'name' => 'House Rent',
            'amount' => 25000,
            'frequency' => 'monthly',
            'due_day' => 5,
            'is_active' => true,
        ]);

        // 5. Seed Debts
        $debtLent = Debt::create([
            'user_id' => $userId,
            'contact_name' => 'Rahul Friend',
            'direction' => 'lent',
            'amount' => 5000,
            'remaining_amount' => 5000,
            'due_date' => $now->copy()->addMonths(1),
            'note' => 'For his vacation',
            'is_settled' => false,
        ]);

        $debtBorrowed = Debt::create([
            'user_id' => $userId,
            'contact_name' => 'Personal Loan',
            'direction' => 'borrowed',
            'amount' => 200000,
            'remaining_amount' => 200000,
            'due_date' => $now->copy()->addYears(2),
            'note' => 'Education loan remaining',
            'is_settled' => false,
        ]);

        // 6. Seed Transactions (Current Month)
        $txs = [
            [
                'account_id' => $bank->id,
                'category_id' => $categoryMap['Salary'],
                'type' => 'income',
                'amount' => 85000,
                'note' => 'Monthly Salary',
                'transaction_date' => $now->copy()->startOfMonth(),
            ],
            [
                'account_id' => $bank->id,
                'category_id' => $categoryMap['Rent'],
                'type' => 'expense',
                'amount' => 25000,
                'note' => 'April Rent Payment',
                'transaction_date' => $now->copy()->startOfMonth()->addDays(4),
            ],
            [
                'account_id' => $cc->id,
                'category_id' => $categoryMap['Shopping'],
                'type' => 'expense',
                'amount' => 1200,
                'note' => 'Amazon Grocery',
                'transaction_date' => $now->copy()->subDays(2),
            ],
            [
                'account_id' => $cash->id,
                'category_id' => $categoryMap['Food & Dining'],
                'type' => 'expense',
                'amount' => 450,
                'note' => 'Dinner at Hotel',
                'transaction_date' => $now->copy()->subDays(1),
            ],
            [
                'account_id' => $bank->id,
                'category_id' => $categoryMap['Utilities'],
                'type' => 'expense',
                'amount' => 1500,
                'note' => 'Electricity Bill',
                'transaction_date' => $now->copy()->subDays(5),
            ],
            [
                'account_id' => $cc->id,
                'category_id' => $categoryMap['Transport'],
                'type' => 'expense',
                'amount' => 800,
                'note' => 'Petrol',
                'transaction_date' => $now->copy()->subDays(3),
            ],
        ];

        foreach ($txs as $tx) {
            $tx['user_id'] = $userId;
            Transaction::create($tx);
        }

        $this->command->info('Demo data seeded successfully for demo@demo.com');
    }
}
