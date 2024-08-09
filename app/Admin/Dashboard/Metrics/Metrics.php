<?php

namespace App\Admin\Dashboard\Metrics;

class Metrics
{
    /**
     * Calcula o crescimento
     *
     * @param $previous (valor anterior)
     * @param $current (valor atual)
     * @return array|int
     */
    protected function calcGrowth($previous, $current)
    {
        if ($previous == 0 || $previous == null || $current == 0) {
            return 0;
        }

        return $result = (($current - $previous) / $previous * 100);
    }

    /**
     * @param $message (mensagem)
     * @return array
     */
    protected function error($message)
    {
        return ['error' => $message];
    }
}
