<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorizationService
{
    /**
     * Predetermined rules for auto-categorization based on keywords.
     */
    protected array $rules = [
        'Salary' => ['salary', 'wages', 'payout'],
        'Food & Dining' => ['zomato', 'swiggy', 'restaurant', 'cafe', 'food', 'hotel', 'dining', 'lunch', 'dinner', 'breakfast'],
        'Bills & Utilities' => ['kseb', 'electricity', 'water', 'internet', 'recharge', 'jio', 'airtel', 'vi', 'bill', 'BSNL', 'broaband'],
        'Transport & Ticket ' => ['uber', 'ola', 'metro', 'auto', 'train'],
        'Grocery & Stationery' => ['amazon', 'flipkart', 'myntra', 'grocery', 'big basket', 'supermarket', 'mall', 'swiggy instamart', 'instamart', 'blinkit'],
        'Entertainment' => ['netflix', 'hotstar', 'prime video', 'cinema', 'theatre', 'game', 'steam'],
        'Education' => ['udemy', 'coursera', 'college', 'school', 'fees', 'book', 'stationary'],
        'Medicine & Health' => ['hospital', 'pharmacy', 'medicine', 'clinic', 'doctor', 'lab', 'medplus'],
        'Rent & Housing' => ['rent', 'maintenance', 'flat', 'apartment'],
        'Bank Fees & Charges' => ['MIN BAL CHGS', 'IMPS Commission', 'Debit Card Charges'],
        'Family / Personal Transfer (Out)' => ['To Amma', 'To Shelna', 'To Dhanam Periyamma', 'To'],
        'Family / Personal Transfer (In)' => ['From Amma', 'From Shelna', 'From Dhanam Periyamma', 'From'],
        'Fuel Expenses' => ['fuel', 'petrol', 'diesel'],
        'Vehicle Maintenance' => ['service', 'repair', 'maintenance', 'garage', 'battery'],
        'Refund' => ['refund'],
        'Cashback' => ['cashback'],
        'Stiching & Lining' => ['stiching', 'lining', 'radha krishna', 'krishnas'],
        'Subscriptions' => ['subscription', 'netflix', 'prime', 'hotstar', 'youtube', 'google'],
        'Lifestyle & Shopping' => ['lifestyle', 'fashion', 'clothing', 'accessories', 'beauty', 'cosmetics', 'dress'],
    ];

    /**
     * Suggest a category ID based on the transaction description.
     */
    public function suggestCategoryId(string $description, int $userId): ?int
    {
        $descriptionLower = Str::lower($description);

        // 1. TIER 1: Historical Memory (Learning from User)
        // Look for the most frequently used category for this exact description
        $historicalCategory = Transaction::where('user_id', $userId)
            ->where('note', 'like', "%$description%")
            ->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->orderByDesc('count')
            ->first();

        if ($historicalCategory?->category_id) {
            return $historicalCategory->category_id;
        }

        // 2. TIER 2: Keyword Rules
        foreach ($this->rules as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($descriptionLower, Str::lower($keyword))) {
                    $category = Category::where('user_id', $userId)
                        ->where('name', 'like', "%$categoryName%")
                        ->first();

                    if ($category) {
                        return $category->id;
                    }

                    // Fallback to global category
                    $globalCategory = Category::whereNull('user_id')
                        ->where('name', 'like', "%$categoryName%")
                        ->first();

                    return $globalCategory?->id;
                }
            }
        }

        return null;
    }
}
