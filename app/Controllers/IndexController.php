<?php namespace Simple\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Simple\Models\Meta;

class IndexController {

    public static function index (Request $request, Response $response)
    {
        $response->getBody()->write(json_encode(Meta::all()->toArray()));

        return $response;
    }

    public static function byYear (Request $request, Response $response)
    {
        $body = $request->getQueryParams();

        $queryDate = Carbon::parse($body['date']);

        $startMonth = $queryDate->copy()->startOfYear();
        $endMonth = $queryDate->copy()->endOfYear();

        $daysCount = $endMonth->diffInMonths($startMonth) + 1;

        $labels = Collection::make(range(1, $daysCount));

        $datasets = Meta::query()
            ->where('created_at', '>=', $startMonth->toDateTimeString())
            ->selectRaw('strftime("%m", created_at) as month, name')
            ->groupBy(['name', 'month'])
            ->selectRaw('avg(value) as data')
            ->get()
            ->map(function (&$data) use ($labels, $endMonth) {
                $data['label'] = $data->name;
                $data['data'] = $data->prepareData($labels, 'month', $endMonth);

                return $data;
            })->toArray();

        $response->getBody()->write(json_encode([
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'daysCount' => $daysCount,
            'labels' => $labels,
            'datasets' => $datasets
        ]));

        return $response;
    }

    public static function byMonth (Request $request, Response $response)
    {
        $body = $request->getQueryParams();

        $queryDate = Carbon::parse($body['date']);

        $startDay = $queryDate->copy()->startOfMonth();
        $endDay = $queryDate->copy()->endOfMonth();

        $daysCount = $endDay->diffInDays($startDay) + 1;

        $labels = Collection::make(range(1, $daysCount));

        $datasets = Meta::query()
            ->where('created_at', '>=', $startDay->toDateTimeString())
            ->where('created_at', '<=', $endDay->toDateTimeString())
            ->selectRaw('strftime("%d", created_at) as day, name')
            ->groupBy(['name', 'day'])
            ->selectRaw('avg(value) as data')
            ->get()
            ->map(function (&$data) use ($labels, $endDay) {
                $data['label'] = $data->name;
                $data['data'] = $data->prepareData($labels, 'day', $endDay);

                return $data;
            })->toArray();

        $response->getBody()->write(json_encode([
            'startMonth' => $startDay,
            'endMonth' => $endDay,
            'daysCount' => $daysCount,
            'labels' => $labels,
            'datasets' => $datasets
        ]));

        return $response;
    }

    public static function byHour (Request $request, Response $response)
    {
        $body = $request->getQueryParams();

        $queryDate = Carbon::parse($body['date']);

        $startMinute = $queryDate->copy()->minute(0)->second(0);
        $endMinute = $queryDate->copy()->minute(59)->second(59);

        $daysCount = $endMinute->diffInMinutes($startMinute) + 1;

        $labels = Collection::make(range(1, $daysCount));

        $datasets = Meta::query()
            ->where('created_at', '>=', $startMinute->toDateTimeString())
            ->where('created_at', '<=', $startMinute->copy()->minute(59)->toDateTimeString())
            ->selectRaw('strftime("%M", created_at) as minute, name')
            ->groupBy(['name'])
            ->selectRaw('avg(value) as data')
            ->get()
            ->map(function (&$data) use ($labels, $endMinute) {
                $data['label'] = $data->name;
                $data['data'] = $data->prepareData($labels, 'hour', $endMinute);

                return $data;
            })->toArray();

        $response->getBody()->write(json_encode([
            'startMonth' => $startMinute,
            'endMonth' => $endMinute,
            'daysCount' => $daysCount,
            'labels' => $labels,
            'datasets' => $datasets
        ]));

        return $response;
    }

    private static function data($d)
    {
        $labels = [];
        $data = [];
        $i = 0;
        $d->each(function ($metas, $name) use (&$labels, &$data, &$i) {

            foreach ($metas as $meta) {
                $data[$i]['label'] = $name;
                $data[$i]['data'][] = $meta['data'];
                $data[$i]['labels'][] = $meta['label'];
            }

            $i++;
        });

        return $data;
    }

    public static function byDay (Request $request, Response $response)
    {
        $body = $request->getQueryParams();

        $queryDate = Carbon::parse($body['date']);


        $startHour = $queryDate->copy()->hour(0)->minute(0)->second(0);
        $endHour = $queryDate->copy()->hour(23)->minute(59)->second(59);

        $daysCount = $endHour->diffInHours($startHour) + 1;

        $labels = Collection::make(range(1, $daysCount));

        $datasets = Meta::query()
            ->where('created_at', '>=', $startHour->toDateTimeString())
            ->where('created_at', '<=', $endHour->toDateTimeString())
            ->selectRaw('strftime("%H", created_at) as hour, name')
            ->groupBy(['name'])
            ->selectRaw('avg(value) as data')
            ->get()
            ->map(function (&$data) use ($labels, $endHour) {
                $data['label'] = $data->name;
                $data['data'] = $data->prepareData($labels, 'hour', $endHour);

                return $data;
            })->toArray();

        $response->getBody()->write(json_encode([
            'startMonth' => $startHour,
            'endMonth' => $endHour,
            'daysCount' => $daysCount,
            'labels' => $labels,
            'datasets' => $datasets
        ]));

        return $response;
    }

    public static function insert (Request $request, Response $response)
    {
        $query = $request->getQueryParams();
        $body = $request->getParsedBody();

        $params = array_merge(
            (key_exists('meta', $query)) ? $query['meta'] : [],
            (key_exists('meta', $body)) ? $body['meta'] : []
        );

        foreach ($params as $name =>  $param) {
            // insert params
            $meta = new Meta();
            $meta->name = $name;
            $meta->value = $param;
            $meta->save();
        }

        $response->getBody()->write(json_encode(['status' => 'Ok!']));

        return $response;
    }
}