@foreach($componentGroups as $componentGroup)
    <x-cachet::component-group :component-group="$componentGroup"/>
@endforeach

@foreach($ungroupedComponents as $component)
    <x-cachet::component-ungrouped :component="$component" />
@endforeach
