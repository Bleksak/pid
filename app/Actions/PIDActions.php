<?php

namespace App\Actions;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Explorer;
use DateTime;
use DateTimeInterface;

class PIDActions
{
    public function __construct(private readonly Explorer $explorer) {}

    /**
     * @param array<int,mixed> $pointOfSale
     */
    private function insertPointsOfSale(array $pointOfSale): ?ActiveRow
    {
        $posRow = $this->explorer->table('points_of_sale')->wherePrimary($pointOfSale['id'])->fetch();

        if ($posRow) {
            return null;
        }

        return $this->explorer->table('points_of_sale')->insert([
            'id' => $pointOfSale['id'],
            'type' => $pointOfSale['type'],
            'name' => $pointOfSale['name'],
            'address' => $pointOfSale['address'],
            'location' => $this->explorer->literal("ST_GeomFromText('POINT({$pointOfSale['lat']} {$pointOfSale['lon']})')"),
            'services' => $pointOfSale['services'],
            'payMethods' => $pointOfSale['payMethods'],
        ]);
    }

    private function insertOpeningDays(string $pointOfSaleId, int $from, int $to): ActiveRow
    {
        $daysRow = $this
            ->explorer
            ->table('days')
            ->where(['from' => $from, 'to' => $to])
            ->fetch();

        if (!$daysRow) {
            $daysRow = $this->explorer->table('days')->insert([
                'from' => $from,
                'to' => $to,
            ]);
        }

        return $this->explorer->table('points_of_sale_opening_days')->insert([
            'points_of_sale_id' => $pointOfSaleId,
            'days_id' => $daysRow->id,
        ]);
    }

    private function insertOpeningHours(int $openingDaysId, DateTimeInterface $from, DateTimeInterface $to): ActiveRow
    {
        $hoursRow = $this
            ->explorer
            ->table('hours')
            ->where(['from' => $from->format('H:i'), 'to' => $to->format('H:i')])
            ->fetch();

        if (!$hoursRow) {
            $hoursRow = $this->explorer->table('hours')->insert([
                'from' => $from->format('H:i'),
                'to' => $to->format('H:i'),
            ]);
        }

        return $this->explorer->table('points_of_sale_opening_hours')->insert([
            'points_of_sale_opening_days_id' => $openingDaysId,
            'hours_id' => $hoursRow->id,
        ]);
    }

    /**
     * @param array<int,mixed> $data
     */
    public function insertFromJson(array $data): void
    {
        // NOTE: v realny aplikaci validovat data
        $this->explorer->beginTransaction();

        foreach ($data as $pointOfSale) {
            $posRow = $this->insertPointsOfSale($pointOfSale);

            if ($posRow == null) {
                continue;
            }

            $openingHours = $pointOfSale['openingHours'];

            foreach ($openingHours as $openingHour) {
                $matches = [];
                // NOTE: proc to API vraci pomlcky s ruznym unicode znakem?
                if (preg_match_all('/(\d{1,2}:\d{2})/', $openingHour['hours'], $matches) == 0) {
                    continue;
                }

                $matches = $matches[0];

                $posOpeningDaysRow = $this->insertOpeningDays($posRow->id, $openingHour['from'], $openingHour['to']);

                foreach (array_chunk($matches, 2) as $time) {
                    $hrsFrom = DateTime::createFromFormat('H:i', $time[0]);
                    $hrsTo = DateTime::createFromFormat('H:i', $time[1]);

                    $this->insertOpeningHours($posOpeningDaysRow->id, $hrsFrom, $hrsTo);
                }
            }
        }

        $this->explorer->commit();
    }
}
