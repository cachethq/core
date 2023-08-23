<?php

namespace Cachet\Data;

use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Illuminate\Http\Request;

class MetricData
{
    public function __construct(
        public string $name,
        public ?string $suffix = null,
        public ?string $description = null,
        public ?float $defaultValue = 0.0,
        public MetricTypeEnum $calcType = MetricTypeEnum::sum,
        public bool $displayChart = true,
        public int $places = 2,
        public MetricViewEnum $defaultView = MetricViewEnum::last_hour,
        public int $threshold = 5,
        public ?int $order = null,
        public bool $visible = true
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'suffix' => $this->suffix,
            'description' => $this->description,
            'default_value' => $this->defaultValue,
            'calc_type' => $this->calcType?->value,
            'display_chart' => $this->displayChart,
            'places' => $this->places,
            'default_view' => $this->defaultView?->value,
            'threshold' => $this->threshold,
            'order' => $this->order,
            'visible' => $this->visible,
        ];
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            suffix: $request->input('suffix'),
            description: $request->input('description'),
            defaultValue: $request->float('default_value'),
            calcType: $request->enum('calc_type', MetricTypeEnum::class),
            displayChart: $request->boolean('display_chart'),
            places: $request->input('places'),
            defaultView: $request->enum('default_view', MetricViewEnum::class),
            threshold: $request->integer('threshold'),
            order: $request->integer('order'),
            visible: $request->boolean('visible')
        );
    }
}
