<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserSettings extends Component
{
    public $currency;
    public $timezone;

    public function mount()
    {
        $user = Auth::user();
        $this->currency = $user->currency;
        $this->timezone = $user->timezone;
    }

    public function save()
    {
        $this->validate([
            'currency' => 'required|string|max:10',
            'timezone' => 'required|timezone',
        ]);

        Auth::user()->update([
            'currency' => $this->currency,
            'timezone' => $this->timezone,
        ]);

        session()->flash('success', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.user-settings', [
            'timezones' => \DateTimeZone::listIdentifiers(),
            'currencies' => [
                'INR' => '₹ Indian Rupee',
                'USD' => '$ US Dollar',
                'EUR' => '€ Euro',
                'GBP' => '£ British Pound',
                'JPY' => '¥ Japanese Yen',
                'AED' => 'د.إ UAE Dirham',
            ],
        ])->layout('layouts.app', ['heading' => 'Settings']);
    }
}
