<?php

namespace App\Service;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\UTCDateTime;
use App\Entity\User;

final class MongoLogService
{
    private Collection $collection;

    public function __construct(
        private Client $client,
        private string $dbName,
        private string $collectionName
    ) {
        $this->collection = $this->client->selectCollection($this->dbName, $this->collectionName);
    }

    public function log(string $type, array $payload = [], ?User $user = null): void
    {
        $this->collection->insertOne([
            'type'      => $type,
            'payload'   => $payload,
            'userId'    => $user?->getId(),
            'userEmail' => $user?->getEmail(),
            'createdAt' => new UTCDateTime((new \DateTimeImmutable())->getTimestamp() * 1000),
        ]);
    }

    public function recent(int $limit = 20): array
    {
        // On récupère directement un tableau au lieu de manipuler un Cursor
        $docs = iterator_to_array(
            $this->collection->find([], [
                'sort'  => ['createdAt' => -1],
                'limit' => $limit,
            ])
        );

        $out = [];
        foreach ($docs as $doc) {
            $out[] = [
                'type'      => $doc['type'] ?? null,
                'payload'   => $doc['payload'] ?? [],
                'userId'    => $doc['userId'] ?? null,
                'userEmail' => $doc['userEmail'] ?? null,
                'createdAt' => isset($doc['createdAt'])
                    ? $doc['createdAt']->toDateTime()->format('Y-m-d H:i:s')
                    : null,
                '_id'       => (string)($doc['_id'] ?? ''),
            ];
        }

        return $out;
    }

    public function clearAll(): void
    {
        $this->collection->deleteMany([]);
    }

    public function getTripReservationsWithStats(
        int $limit = 50,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        ?int $driverId = null
    ): array {
        $filter = ['type' => 'trip.reservation'];

        if ($startDate && $endDate) {
            $start = (new \DateTimeImmutable($startDate->format('Y-m-d')))->setTime(0, 0, 0);
            $end = (new \DateTimeImmutable($endDate->format('Y-m-d')))->setTime(23, 59, 59);

            $filter['createdAt'] = [
                '$gte' => new \MongoDB\BSON\UTCDateTime($start->getTimestamp() * 1000),
                '$lte' => new \MongoDB\BSON\UTCDateTime($end->getTimestamp() * 1000),
            ];
        } elseif ($startDate) {
            $start = (new \DateTimeImmutable($startDate->format('Y-m-d')))->setTime(0, 0, 0);
            $filter['createdAt'] = [
                '$gte' => new \MongoDB\BSON\UTCDateTime($start->getTimestamp() * 1000),
            ];
        } elseif ($endDate) {
            $end = (new \DateTimeImmutable($endDate->format('Y-m-d')))->setTime(23, 59, 59);
            $filter['createdAt'] = [
                '$lte' => new \MongoDB\BSON\UTCDateTime($end->getTimestamp() * 1000),
            ];
        }

        if ($driverId !== null) {
            $filter['payload.driverId'] = $driverId;
        }

        $cursor = $this->collection->find(
            $filter,
            [
                'sort'  => ['createdAt' => -1],
                'limit' => $limit,
            ]
        );

        $docs = iterator_to_array($cursor);

        $logs = [];
        $totalCredits = 0;
        foreach ($docs as $doc) {
            $price = $doc['payload']['price'] ?? 0;
            $totalCredits += $price;

            $logs[] = [
                'tripId'       => $doc['payload']['tripId'] ?? null,
                'driverId'     => $doc['payload']['driverId'] ?? null,
                'driverPseudo' => $doc['payload']['driverPseudo'] ?? null,
                'price'        => $price,
                'seatsBooked'  => $doc['payload']['seatsBooked'] ?? null,
                'departure'    => $doc['payload']['departure'] ?? null,
                'arrival'      => $doc['payload']['arrival'] ?? null,
                'userId'       => $doc['userId'] ?? null,
                'userEmail'    => $doc['userEmail'] ?? null,
                'createdAt'    => isset($doc['createdAt'])
                    ? $doc['createdAt']->toDateTime()->format('Y-m-d H:i:s')
                    : null,
                '_id'          => (string)($doc['_id'] ?? ''),
            ];
        }

        return [
            'logs' => $logs,
            'totalCredits' => $totalCredits,
            'platformCredits' => count($logs) * 2
        ];
    }
}
