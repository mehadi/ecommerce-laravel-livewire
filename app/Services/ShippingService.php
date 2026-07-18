<?php

namespace App\Services;

use App\Models\ShippingCityRate;
use App\Models\ShippingSetting;

class ShippingService
{
    /**
     * Calculate shipping cost based on weight and city.
     */
    public function calculate(float $weightKg, ?int $cityId = null): float
    {
        $setting = ShippingSetting::getActive();

        if (! $setting || ! $setting->is_active) {
            return 0;
        }

        return match ($setting->type) {
            'flat' => $this->calculateFlat($setting),
            'weight' => $this->calculateWeight($setting, $weightKg),
            'city' => $this->calculateCity($setting, $weightKg, $cityId),
            default => 0,
        };
    }

    /**
     * Calculate flat shipping rate.
     */
    protected function calculateFlat(ShippingSetting $setting): float
    {
        return (float) ($setting->flat_rate ?? 0);
    }

    /**
     * Calculate weight-based shipping rate.
     */
    protected function calculateWeight(ShippingSetting $setting, float $weightKg): float
    {
        $baseRate = (float) ($setting->base_rate ?? 0);
        $baseWeight = (float) ($setting->base_weight_kg ?? 1);
        $perKgRate = (float) ($setting->per_kg_rate ?? 0);

        if ($weightKg <= $baseWeight) {
            return $baseRate;
        }

        $additionalWeight = $weightKg - $baseWeight;
        $additionalCost = ceil($additionalWeight) * $perKgRate;

        return $baseRate + $additionalCost;
    }

    /**
     * Calculate city + weight-based shipping rate.
     */
    protected function calculateCity(ShippingSetting $setting, float $weightKg, ?int $cityId): float
    {
        // If city ID is provided, try to find city-specific rate
        if ($cityId) {
            $cityRate = ShippingCityRate::where('city_id', $cityId)
                ->where('is_active', true)
                ->first();

            if ($cityRate) {
                return $this->calculateRateFromCityRate($cityRate, $weightKg);
            }
        }

        // If no city-specific rate found, check for "Rest of All Cities" rate
        $restOfAllCitiesRate = ShippingCityRate::whereNull('city_id')
            ->where('is_active', true)
            ->first();

        if ($restOfAllCitiesRate) {
            return $this->calculateRateFromCityRate($restOfAllCitiesRate, $weightKg);
        }

        // Fallback to default weight-based calculation
        return $this->calculateWeight($setting, $weightKg);
    }

    /**
     * Calculate rate from a city rate model.
     */
    protected function calculateRateFromCityRate(ShippingCityRate $cityRate, float $weightKg): float
    {
        $baseRate = (float) $cityRate->base_rate;
        $baseWeight = (float) $cityRate->base_weight_kg;
        $perKgRate = (float) $cityRate->per_kg_rate;

        if ($weightKg <= $baseWeight) {
            return $baseRate;
        }

        $additionalWeight = $weightKg - $baseWeight;
        $additionalCost = ceil($additionalWeight) * $perKgRate;

        return $baseRate + $additionalCost;
    }
}
