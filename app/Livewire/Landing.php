<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;

class Landing extends Component
{
    public $trendData = [];

    public function mount()
    {
        $this->generateSampleData();
    }

    public function render()
    {
        return view('livewire.landing');
    }

    private function generateSampleData()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $trendData = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $trendData[] = [
                'date' => $date->format('Y-m-d'),
                'earnings' => rand(500, 1500),
                'expenses' => rand(300, 1000),
            ];
        }

        // Calculate balance
        foreach ($trendData as &$day) {
            $day['balance'] = $day['earnings'] - $day['expenses'];
        }

        $this->trendData = $trendData;
    }
}
