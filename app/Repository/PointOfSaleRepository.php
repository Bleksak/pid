<?php

namespace App\Repository;

use App\Entity\Hours;
use App\Entity\OpeningHours;
use App\Entity\PointOfSale;
use Nette\Database\Connection;
use Nette\Database\Explorer;
use DateTime;

class PointOfSaleRepository
{
    public function __construct(
        private readonly Explorer $explorer,
        private readonly Connection $connection,
    ) {}

    /**
     * @return array<int, PointOfSale>
     */
    public function findByDateTime(DateTime $dateTime): array
    {
        $day = $dateTime->format('w');
        $currentHour = $dateTime->format('H:i:s');

        $pointsOfSale = $this
            ->explorer
            ->fetchAll(
                '
                SELECT pos.id, pos.name, pos.type, pos.address, ST_X(pos.location) AS lon, ST_Y(pos.location) AS lat, pos.services, pos.payMethods
                    FROM points_of_sale pos
                    INNER JOIN points_of_sale_opening_days posod ON posod.points_of_sale_id = pos.id
                    INNER JOIN days d ON d.id = posod.days_id
                    INNER JOIN points_of_sale_opening_hours posoh ON posoh.points_of_sale_opening_days_id = posod.id
                    INNER JOIN hours h ON h.id = posoh.hours_id
                    WHERE d.from <= ? AND d.to >= ?
                    AND h.from <= ? AND h.to > ?
                ',
                $day,
                $day,
                $currentHour,
                $currentHour
            );

        $pointsOfSaleEntities = [];

        foreach ($pointsOfSale as $row) {
            $pos = new PointOfSale($row->id, $row->name, $row->type, $row->address, $row->lon, $row->lat, $row->services, $row->payMethods, []);

            $posOpeningDays = $this->explorer->fetchAll(
                '
                    SELECT *
                    FROM points_of_sale_opening_days posod
                    WHERE posod.points_of_sale_id = ?
                ',
                $row->id
            );

            foreach ($posOpeningDays as $posOpeningDay) {
                $openingDays = $this->explorer->fetch(
                    '
                        SELECT *
                        FROM days d
                        WHERE d.id = ?
                    ',
                    $posOpeningDay->days_id
                );

                $hours = $this->explorer->fetchAll(
                    '
                        SELECT h.*
                        FROM points_of_sale_opening_hours posoh
                        INNER JOIN hours h ON h.id = posoh.hours_id
                        WHERE posoh.points_of_sale_opening_days_id = ?
                    ',
                    $posOpeningDay->id
                );

                $hours = array_map(fn($hour) => new Hours($hour->from, $hour->to), $hours);

                $pos->openingHours[] = new OpeningHours($openingDays->from, $openingDays->to, $hours);
            }

            $pointsOfSaleEntities[] = $pos;
        }

        return $pointsOfSaleEntities;
    }
}
