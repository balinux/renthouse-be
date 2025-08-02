<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{

    private function getPercentage(int $from, int $to): int
    {
        return $to - $from / ($to + $from) * 100;
    }

    protected function getStats(): array
    {
        // Get new listing this month
        $newListing = Listing::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();

        // Get transactions this month
        $transactions = Transaction::whereStatus('approved')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);

        // Get previous month transactions
        $previousMonthTransactions = Transaction::whereStatus('approved')->whereMonth('created_at', Carbon::now()->subMonth()->month)->whereYear('created_at', Carbon::now()->subMonth()->year);

        // Get transactions percentage
        $transactionsPercentage = $this->getPercentage($previousMonthTransactions->count(), $transactions->count());

        // Get revenue percentage
        $revenuePercentage = $this->getPercentage($previousMonthTransactions->sum('total_price'), $transactions->sum('total_price'));

        return [
            Stat::make('New Listing this month', $newListing),
            Stat::make('Transactions this month', $transactions->count())
                ->description($transactionsPercentage > 0 ? 'up ' . $transactionsPercentage . '%' : 'down ' . $transactionsPercentage . '%')
                ->icon($transactionsPercentage > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($transactionsPercentage > 0 ? 'success' : 'danger'),
            Stat::make('Revenue this month', Number::currency($transactions->sum('total_price'), 'IDR'))
                ->description($revenuePercentage > 0 ? 'up ' . $revenuePercentage . '%' : 'down ' . $revenuePercentage . '%')
                ->icon($revenuePercentage > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($revenuePercentage > 0 ? 'success' : 'danger'),
        ];
    }
}
