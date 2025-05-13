<?php

namespace App\Orchid\Layouts\Charts;

use Orchid\Screen\Layouts\Chart;

class typeDocsChart extends Chart
{
    /**
     * Available options:
     * 'bar', 'line', 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'line';

    /**
     * Set the target for the data passed.
     *
     * @var string
     */
    protected $target = 'courbesData';

    /**
     * Configuring line.
     *
     * @var array
     */
    protected $lineOptions = [
        'spline'     => 1,
        'regionFill' => 1,
        'hideDots'   => 0,
        'hideLine'   => 0,
        'heatline'   => 0,
        'dotSize'    => 3,
    ];

    /**
     * Determines whether to display the export button.
     *
     * @var bool
     */
    protected $export = true; // Optionnel, mais activé si tu veux l'export
}

