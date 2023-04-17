<?php

namespace App\Http\Livewire;

use App\Models\Expense;
use App\Models\SiteVisit\SiteVisit;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $colors = [
        '#f6ad55',
        '#fc8181',
        '#90cdf4',
        '#66DA26',
        '#cbd5e0',
    ];

    public $firstRun = true;

    public $showDataLabels = false;

    protected $listeners = [
        'onPointClick' => 'handleOnPointClick',
        'onSliceClick' => 'handleOnSliceClick',
        'onColumnClick' => 'handleOnColumnClick',
        'onBlockClick' => 'handleOnBlockClick',
    ];

    public function handleOnPointClick($point)
    {
        dd($point);
    }

    public function handleOnSliceClick($slice)
    {
        dd($slice);
    }

    public function handleOnColumnClick($column)
    {
        dd($column);
    }

    public function handleOnBlockClick($block)
    {
        dd($block);
    }

    public function render()
    {
        $expenses = SiteVisit::query()
            ->select(['id', 'city'])
            ->whereDate('created_at','>=', Carbon::now()->subDays(7)->format('Y-m-d'))
            ->get();

        $pieChartModel = $expenses->groupBy('city')
            ->reduce(function ($pieChartModel, $data) {
                $id = $data->first()->id;
                $type = $data->first()->city;
                $value = $data->count('id');

                return $pieChartModel->addSlice($type, $value, $this->colors[$id]);
            }, LivewireCharts::pieChartModel()
                ->setTitle('Статистика посещений за последние 7 дней')
                ->setAnimated($this->firstRun)
                ->withOnSliceClickEvent('onSliceClick')
                //->withoutLegend()
                ->legendPositionBottom()
                ->legendHorizontallyAlignedCenter()
                ->setDataLabelsEnabled($this->showDataLabels)
                ->setColors(['#b01a1b', '#d41b2c', '#ec3c3b', '#f66665'])
            );
        $expenses = SiteVisit::query()
            ->select([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H") as created_by_hour'),
                DB::raw('COUNT(id) as count')
            ])
            ->whereDate('created_at','>=', Carbon::now()->subDays(7)->format('Y-m-d'))
            ->groupBy('created_by_hour')
            ->get();

        $lineChartModel = $expenses
            ->reduce(function ($lineChartModel, $data) {
                return $lineChartModel->addPoint($data->created_by_hour, $data->count);
            }, LivewireCharts::lineChartModel()
                ->setTitle('Активность по часам')
                ->setAnimated($this->firstRun)
                ->withOnPointClickEvent('onPointClick')
                ->setSmoothCurve()
                ->setXAxisVisible(true)
                ->legendPositionBottom()
                ->setDataLabelsEnabled($this->showDataLabels)
                ->sparklined()
            );

        $this->firstRun = false;

        return view('dashboard', [
            'pieChartModel' => $pieChartModel,
            'lineChartModel' => $lineChartModel,
        ]);
    }


}
