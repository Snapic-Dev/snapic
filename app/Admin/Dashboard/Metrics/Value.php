<?php

namespace App\Admin\Dashboard\Metrics;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class Value extends Metrics
{
    /**
     * As funções disponíveis
     * @var array
     */
    private $functions = [
        'count',
        'min',
        'max',
        'sum',
        'avg'
    ];

    /**
     * Os intervalos disponíveis
     * @var array
     */
    public $ranges = [
        1,
        3,
        5,
        7,
        10,
        14,
        21,
        30,
        60,
        90
    ];

    /**
     * Obtém os valores agregados do modelo com base na função e no intervalo fornecidos
     * @param $model
     * @param $function
     * @param $range
     * @param null $column
     * @param null $dateColumn
     * @return array
     */
    public function get($model, $function, $range, $column = null, $dateColumn = null)
    {
        if (!in_array($function, $this->functions)) {
            return $this->error('Invalid function provided');
        }

        if (!in_array($range, $this->ranges)) {
            return $this->error('Invalid range provided');
        }

        return $this->aggregate($model, $function, $range, $column, $dateColumn);
    }

    /**
     * Calcula o intervalo atual
     *
     * @param $range
     * @return array
     */
    protected function calcRange($range)
    {
        return [
            now()->subDays($range),
            now()
        ];
    }

    /**
     * Calcula o intervalo anterior
     *
     * @param $range
     * @return array
     */
    protected function calcPreviousRange($range)
    {
        return [
            now()->subDays($range * 2),
            now()->subDays($range)
        ];
    }

    /**
     * Realiza a agregação de valores com base na função e intervalo fornecidos
     * @param $model
     * @param $function
     * @param $range
     * @param null $column
     * @param null $dateColumn
     * @return array
     */
    protected function aggregate($model, $function, $range, $column = null, $dateColumn = null)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $column = $column ?? $query->getModel()->getQualifiedKeyName();
        $dateColumn = $dateColumn ?? $query->getModel()->getCreatedAtColumn();

        $value = (clone $query)->whereBetween($dateColumn, $this->calcRange($range))
            ->{$function}($column);

        $previousValue = (clone $query)->whereBetween($dateColumn, $this->calcPreviousRange($range))
            ->{$function}($column);

        $current = round($value, 0);
        $previous = round($previousValue, 0);

        return [
            'value' => $current,
            'previous_value' => $previous,
            'ranges' => $this->ranges,
            'growth' => $this->calcGrowth($previous, $current)
        ];
    }
}
