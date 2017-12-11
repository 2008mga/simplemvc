<?php namespace Simple\Controllers;

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

        $metas = Meta::between($body['start_date'], $body['end_date'])
            ->groupBy('name')
            ->selectRaw('strftime("%Y", created_at) as year, name, strftime("%Y", created_at) as label')
            ->groupBy(['year'])
            ->selectRaw('avg(value) as data')
            ->get()
            ->groupBy('name');

        $response->getBody()->write(json_encode(self::data($metas)));

        return $response;
    }

    public static function byMonth (Request $request, Response $response)
    {
        $body = $request->getQueryParams();

        $metas = Meta::between($body['start_date'], $body['end_date'])
            ->groupBy('name')
            ->selectRaw('strftime("%Y", created_at) as year, name, strftime("%Y-%m", created_at) as label')
            ->groupBy(['year', 'label'])
            ->selectRaw('avg(value) as data')
            ->get()
            ->groupBy('name');

        $response->getBody()->write(json_encode(self::data($metas)));

        return $response;
    }

    public static function byHour (Request $request, Response $response)
    {
        $body = $request->getQueryParams();

        $metas = Meta::between($body['start_date'], $body['end_date'])
            ->groupBy('name')
            ->selectRaw('strftime("%H", created_at) as hour, name, strftime("%Y-%m-%d %H:00:00", created_at) as label')
            ->groupBy(['label', 'hour'])
            ->selectRaw('avg(value) as data')
            ->get()
            ->groupBy('name');

        $response->getBody()->write(json_encode(self::data($metas)));

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

        $metas = Meta::between($body['start_date'], $body['end_date'])
            ->groupBy('name')
            ->selectRaw('strftime("%d", created_at) as day, name, strftime("%Y-%m-%d", created_at) as label')
            ->groupBy(['label', 'day'])
            ->selectRaw('avg(value) as data')
            ->get()
            ->groupBy('name');




//        $response->getBody()->write(json_encode(self::data($metas)));
        $response->getBody()->write(json_encode(Meta::all()));

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