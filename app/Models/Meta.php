<?php namespace Simple\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $fillable = [
        'name',
        'value',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['name','day'];

    public static function between($start_date, $end_date, $get = false)
    {
        $query = self::query()
            ->where('created_at', '<=', $end_date)
            ->where('created_at', '>=', $start_date);
        return ($get) ? $query->get() : $query;
    }

    public function prepareData($labels, $index, Carbon $end)
    {
        $key = $index;
        $index = (int) $this->{$index};
        $result = [];

        foreach ($labels as $label) {
            $label = (int) $label;

            if ($end->gt(Carbon::now()) && Carbon::now()->{$key} < $label) {
                $result[$label] = 0;

            } else {
                $result[$label] = ($index == $label) ? $this->data : (($index < $label) ? $this->data : 0);
            }

        }

        return array_values($result);
    }
}