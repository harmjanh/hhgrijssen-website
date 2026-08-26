<?php

namespace App\Enums;

enum CatechesisGroup: string
{
    case Monday1213 = 'monday_12_13';
    case Monday1819 = 'monday_18_19';
    case Monday20Plus = 'monday_20_plus';
    case Tuesday1415 = 'tuesday_14_15';
    case Tuesday1617 = 'tuesday_16_17';

    public function weekday(): string
    {
        return match ($this) {
            self::Monday1213, self::Monday1819, self::Monday20Plus => 'Maandagavond',
            self::Tuesday1415, self::Tuesday1617 => 'Dinsdagavond',
        };
    }

    public function timeRange(): string
    {
        return match ($this) {
            self::Monday1213 => '18.30 - 19.15',
            self::Monday1819, self::Tuesday1415 => '19.30 - 20.15',
            self::Monday20Plus, self::Tuesday1617 => '20.30 - 21.15',
        };
    }

    public function ageRange(): string
    {
        return match ($this) {
            self::Monday1213 => '12 - 13 jaar',
            self::Tuesday1415 => '14 - 15 jaar',
            self::Tuesday1617 => '16 - 17 jaar',
            self::Monday1819 => '18 - 19 jaar',
            self::Monday20Plus => '20 jaar en ouder',
        };
    }

    public function label(): string
    {
        return "{$this->weekday()} · {$this->timeRange()} · {$this->ageRange()}";
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        $options = [];

        foreach (self::cases() as $group) {
            $options[$group->weekday()][$group->value] = $group->timeRange().' | '.$group->ageRange();
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $group): array => [$group->value => $group->label()])
            ->all();
    }

    /**
     * @return list<array{day: string, options: list<array{value: string, time: string, age: string}>}>
     */
    public static function groupedForFrontend(): array
    {
        $groups = [];

        foreach (self::cases() as $group) {
            $day = $group->weekday();
            $groups[$day]['day'] = $day;
            $groups[$day]['options'][] = [
                'value' => $group->value,
                'time' => $group->timeRange(),
                'age' => $group->ageRange(),
            ];
        }

        return array_values($groups);
    }
}
