<?php

namespace App\Services;

use App\Models\Category;
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
        'Transport' => ['uber', 'ola', 'fuel', 'petrol', 'diesel', 'metro', 'auto', 'train'],
        'Shopping' => ['amazon', 'flipkart', 'myntra', 'grocery', 'big basket', 'supermarket', 'mall', 'swiggy instamart', 'instamart', 'blinkit'],
        'Entertainment' => ['netflix', 'hotstar', 'prime video', 'cinema', 'theatre', 'game', 'steam'],
        'Education' => ['udemy', 'coursera', 'college', 'school', 'fees', 'book', 'stationary'],
        'Health' => ['hospital', 'pharmacy', 'medicine', 'clinic', 'doctor', 'lab', 'medplus'],
        'Rent & Housing' => ['rent', 'maintenance', 'flat', 'apartment'],
        'Bank Fees' => ['MIN BAL CHGS', 'IMPS Commission', 'Debit Card Charges'],
        'Family / Personal Transfer (Out)' => ['To Amma', 'To Shelna', 'To Dhanam Periyamma', 'To'],
        'Family / Personal Transfer (In)' => ['From Amma', 'From Shelna', 'From Dhanam Periyamma', 'From'],
    ];

    /**
     * Suggest a category ID based on the transaction description.
     */
    public function suggestCategoryId(string $description, int $userId): ?int
    {
        $description = Str::lower($description);

        foreach ($this->rules as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($description, $keyword)) {
                    $category = Category::where('user_id', $userId)
                        ->where('name', 'like', "%$categoryName%")
                        ->first();

                    if ($category) {
                        return $category->id;
                    }

                    // Fallback to global category if user-specific one doesn't exist
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
