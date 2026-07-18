<?php

namespace App\Livewire\Dashboard\Concerns;

use App\Models\UserDashboardPreference;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

/**
 * The drag-reorder / show-hide customization system shared by every
 * dashboard sub-page. Every query and write is scoped to the current page
 * via pageKey(), so the same card_key can have independent preferences on
 * different pages.
 */
trait HasCardPreferences
{
    public bool $isCustomizing = false;

    public bool $showResetConfirmation = false;

    public function mountHasCardPreferences(): void
    {
        $this->initializeUserPreferences();
    }

    #[Computed]
    public function userPreferences(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        $preferences = UserDashboardPreference::where('user_id', $user->id)
            ->where('page', $this->pageKey())
            ->get();

        // If no preferences exist, create defaults
        if ($preferences->isEmpty()) {
            $this->createDefaultPreferences($user->id);

            return UserDashboardPreference::where('user_id', $user->id)
                ->where('page', $this->pageKey())
                ->get();
        }

        return $preferences;
    }

    #[Computed]
    public function orderedMetricCards(): Collection
    {
        $preferences = $this->userPreferences;
        $available = $this->availableMetricCards;

        // Get cards with preferences
        $cardsWithPrefs = $preferences
            ->where('card_type', 'metric')
            ->where('is_visible', true)
            ->sortBy('order')
            ->map(function ($pref) use ($available) {
                $card = $available[$pref->card_key] ?? null;
                if ($card) {
                    $card['key'] = $pref->card_key;
                    $card['preference'] = $pref;
                    $card['order'] = $pref->order;
                }

                return $card;
            })
            ->filter();

        // Get cards without preferences (new cards) - default to visible
        $maxOrder = $preferences->where('card_type', 'metric')->max('order') ?? 0;
        $cardsWithoutPrefs = collect($available)
            ->filter(function ($card, $key) use ($preferences) {
                return ! $preferences->contains('card_key', $key);
            })
            ->map(function ($card, $key) use (&$maxOrder) {
                $card['key'] = $key;
                $card['order'] = ++$maxOrder;

                return $card;
            });

        // Merge and sort by order
        return $cardsWithPrefs
            ->merge($cardsWithoutPrefs)
            ->sortBy('order')
            ->values();
    }

    #[Computed]
    public function orderedChartCards(): Collection
    {
        $preferences = $this->userPreferences;
        $available = $this->availableChartCards;

        return $preferences
            ->where('card_type', 'chart')
            ->where('is_visible', true)
            ->sortBy('order')
            ->map(function ($pref) use ($available) {
                $card = $available[$pref->card_key] ?? null;
                if ($card) {
                    $card['key'] = $pref->card_key;
                    $card['preference'] = $pref;
                }

                return $card;
            })
            ->filter();
    }

    protected function initializeUserPreferences(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $existing = UserDashboardPreference::where('user_id', $user->id)
            ->where('page', $this->pageKey())
            ->count();
        if ($existing === 0) {
            $this->createDefaultPreferences($user->id);
        }
    }

    protected function createDefaultPreferences(int $userId): void
    {
        $metrics = array_flip(array_keys($this->availableMetricCards));
        $charts = array_flip(array_keys($this->availableChartCards));
        $insights = array_flip(array_keys($this->availableInsightCards));

        foreach ($metrics as $key => $order) {
            UserDashboardPreference::create([
                'user_id' => $userId,
                'card_key' => $key,
                'order' => $order + 1,
                'is_visible' => true,
                'card_type' => 'metric',
                'page' => $this->pageKey(),
            ]);
        }

        foreach ($insights as $key => $order) {
            UserDashboardPreference::create([
                'user_id' => $userId,
                'card_key' => $key,
                'order' => $order + 1,
                'is_visible' => true,
                'card_type' => 'insight',
                'page' => $this->pageKey(),
            ]);
        }

        foreach ($charts as $key => $order) {
            UserDashboardPreference::create([
                'user_id' => $userId,
                'card_key' => $key,
                'order' => $order + 1,
                'is_visible' => true,
                'card_type' => 'chart',
                'page' => $this->pageKey(),
            ]);
        }
    }

    public function toggleCardVisibility(string $cardKey): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        // Determine card type first
        $cardType = $this->availableMetricCards[$cardKey]['type']
            ?? $this->availableChartCards[$cardKey]['type']
            ?? $this->availableInsightCards[$cardKey]['type']
            ?? 'metric';

        // Find preference matching both key and type on this page
        $pref = UserDashboardPreference::where('user_id', $user->id)
            ->where('card_key', $cardKey)
            ->where('card_type', $cardType)
            ->where('page', $this->pageKey())
            ->first();

        if ($pref) {
            $newVisibility = ! $pref->is_visible;
            $pref->update(['is_visible' => $newVisibility]);
        } else {
            // Create preference if it doesn't exist (for new cards)
            $maxOrder = UserDashboardPreference::where('user_id', $user->id)
                ->where('card_type', $cardType)
                ->where('page', $this->pageKey())
                ->max('order') ?? 0;

            UserDashboardPreference::create([
                'user_id' => $user->id,
                'card_key' => $cardKey,
                'card_type' => $cardType,
                'order' => $maxOrder + 1,
                'is_visible' => true,
                'page' => $this->pageKey(),
            ]);
        }

        // Clear computed property cache
        unset($this->orderedMetricCards, $this->orderedChartCards, $this->userPreferences, $this->dashboardInsights);
    }

    public function updateCardOrder(array $cardOrder): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        foreach ($cardOrder as $index => $cardKey) {
            UserDashboardPreference::where('user_id', $user->id)
                ->where('card_key', $cardKey)
                ->where('page', $this->pageKey())
                ->update(['order' => $index + 1]);
        }

        // Clear computed property cache to refresh order
        unset($this->orderedMetricCards, $this->orderedChartCards, $this->userPreferences, $this->dashboardInsights);
    }

    public function toggleCustomization(): void
    {
        $this->isCustomizing = ! $this->isCustomizing;

        if (! $this->isCustomizing) {
            $this->showResetConfirmation = false;
        }
        // Clear cache to refresh cards
        unset($this->orderedMetricCards, $this->orderedChartCards, $this->dashboardInsights);

        // Dispatch event for JavaScript
        $this->dispatch('customization-toggled');
    }

    public function resetDashboardPreferences(): void
    {
        $user = auth()->user();
        if (! $user) {
            session()->flash('error', __('You must be logged in to reset dashboard preferences.'));

            return;
        }

        UserDashboardPreference::where('user_id', $user->id)
            ->where('page', $this->pageKey())
            ->delete();
        $this->createDefaultPreferences($user->id);

        unset(
            $this->orderedMetricCards,
            $this->orderedChartCards,
            $this->userPreferences,
            $this->dashboardInsights,
            $this->filteredOrders,
            $this->filteredOrderItems
        );

        $this->showResetConfirmation = true;
        session()->flash('message', __('Dashboard layout was reset to the default arrangement.'));

        $this->dispatch('dashboard-preferences-reset');
    }
}
