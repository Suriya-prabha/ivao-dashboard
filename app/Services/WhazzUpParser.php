<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;
class WhazzUpParser{
    private array $data;
    public function __construct()
{
    $path = storage_path('app/ivao-whazzup-json.json');

    if (!file_exists($path)) {

        die('FILE NOT FOUND: ' . $path);
    }

    $raw = file_get_contents($path);

    if ($raw === false) {

        die('FAILED TO READ FILE');
    }

    $decoded = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {

        die(json_last_error_msg());
    }

    $this->data = $decoded;
}
    public function getSummary(): array
    {
        $connections = $this->data['connections'];
        return [
            'total_connections' => $connections['total'],
            'pilots'            => $connections['pilot'],
            'atc'               => $connections['atc'],
            'observers'         => $connections['observer'],
            'supervisors'       => $connections['supervisor'],
            'unique_users_24h'  => $connections['uniqueUsers24h'],
            'world_tour'        => $connections['worldTour'],
            'updated_at'        => $this->data['updatedAt'],
        ];
    }
    public function getPilots(): array
    {
        return collect($this->data['clients']['pilots'])
            ->filter(fn($p) => isset($p['lastTrack']['latitude']))
            ->map(fn($p) => [
                'id'            => $p['id'],
                'callsign'      => $p['callsign'],
                'latitude'      => $p['lastTrack']['latitude'],
                'longitude'     => $p['lastTrack']['longitude'],
                'altitude'      => $p['lastTrack']['altitude'],
                'ground_speed'  => $p['lastTrack']['groundSpeed'],
                'heading'       => $p['lastTrack']['heading'],
                'state'         => $p['lastTrack']['state'],
                'on_ground'     => $p['lastTrack']['onGround'],
                'departure'     => $p['flightPlan']['departureId'] ?? null,
                'arrival'       => $p['flightPlan']['arrivalId'] ?? null,
                'aircraft'      => $p['flightPlan']['aircraftId'] ?? null,
                'route'         => $p['flightPlan']['route'] ?? null,
                'simulator'     => $p['pilotSession']['simulatorId'] ?? null,
                'rating'        => $p['rating'],
                'arrival_dist'  => $p['lastTrack']['arrivalDistance'] ?? null,
            ])
            ->values()
            ->toArray();
    }
    public function getAirportActivity(): array
    {
        $pilots = $this->data['clients']['pilots'];
        $airports = [];
        foreach ($pilots as $pilot) {
            $dep = $pilot['flightPlan']['departureId'] ?? null;
            $arr = $pilot['flightPlan']['arrivalId'] ?? null;
            if ($dep) $airports[$dep]['departures'] = ($airports[$dep]['departures'] ?? 0) + 1;
            if ($arr) $airports[$arr]['arrivals'] = ($airports[$arr]['arrivals'] ?? 0) + 1;
        }
        return collect($airports)
            ->map(fn($v, $k) => [
                'airport'    => $k,
                'departures' => $v['departures'] ?? 0,
                'arrivals'   => $v['arrivals'] ?? 0,
                'total'      => ($v['departures'] ?? 0) + ($v['arrivals'] ?? 0),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(20)
            ->toArray();
    }
    public function getATC(): array
    {
        return collect($this->data['clients']['atcs'] ?? [])
            ->map(fn($a) => [
                'callsign'  => $a['callsign'],
                'rating'    => $a['rating'],
                'frequency' => $a['atcSession']['frequency'] ?? null,
                'position'  => $a['atcSession']['position'] ?? null,
            ])
            ->toArray();
    }
    public function getAircraftTypes(): array
    {
        return collect($this->data['clients']['pilots'])
            ->pluck('flightPlan.aircraftId')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->map(fn($count, $type) => ['type' => $type, 'count' => $count])
            ->values()
            ->toArray();
    }
}
?>