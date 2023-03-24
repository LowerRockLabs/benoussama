<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Link;
use App\Models\Order;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectDropdownFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\NumberFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use LowerRockLabs\LaravelLivewireTablesAdvancedFilters\DatePickerFilter;
use LowerRockLabs\LaravelLivewireTablesAdvancedFilters\DateRangeFilter;
use LowerRockLabs\LaravelLivewireTablesAdvancedFilters\NumberRangeFilter;
use LowerRockLabs\LaravelLivewireTablesAdvancedFilters\SlimSelectFilter;
use LowerRockLabs\LaravelLivewireTablesAdvancedFilters\SmartSelectFilter;

class LinkTable extends DataTableComponent
{

    use LivewireAlert;

    protected $model = Link::class;

    public $filterData = [];
    public array $cartItems = [];

    public array $arrayOfCountries = [];
    public int $maxASRange = 0;  // Changed to Public to maintain state

    protected $listeners = [
        'createOrder'
    ];

    public function createOrder(Link $link)
    {
        $this->cartItems[] = (string) $link->id;
        if (!$link->is_on_order) {
            $this->alert('error', "You already purchased this link!");
            return;
        }

        $order = Order::create([
            'user_id' => '1',
            'link_id' => $link->id,
            'price' =>  $link->price + 150,
        ]);

        $this->alert('success', "Order {$order->id} created Successfully!");
    }

    public function configure(): void
    {
        if ($this->maxASRange == 0)
        { 
            $this->maxASRange = \Illuminate\Support\Facades\DB::select("select max(`as`) as 'maxAS' from links")[0]->maxAS;
        }
        $this->setPrimaryKey('id')
            ->setSingleSortingDisabled()
            ->setOfflineIndicatorEnabled()
            ->setQueryStringDisabled()
            // ->setFilterLayoutSlideDown()
            ->setTableAttributes(["x-data" => "{ cartItems: \$wire.entangle('cartItems') }"]);
            //->setEagerLoadAllRelationsEnabled(); // Not needed when you are including the relationships anyway!

        if (empty($this->arrayOfCountries)) {
            $this->arrayOfCountries = Country::select('id', 'name', 'code')
                ->orderBy('name')
                ->get()
                ->map(function ($country) {
                    $countryValue['id'] = $country->id;
                    $countryValue['name'] = $country->name;
                    $countryValue['htmlName'] = "<span><span class='fi fi-" . strtolower($country->code) . "'></span>" . $country->name . "</span>";

                    return $countryValue;
                })
                ->keyBy('id')
                ->toArray();
        }
    }



    public function columns(): array
    {
        return [
            Column::make("Cart")
            ->label(
                fn($row, Column $column) => view('addToCartButton')->withValue($row->id)
            ),

            Column::make("ID", "id")
                ->sortable()
                ->searchable(),

            Column::make("Site", "site")
                ->sortable()
                ->searchable()
                ->secondaryHeader(
                    $this->getFilterByKey('site')
                )
                ->footer(
                    $this->getFilterByKey('site')
                ),

                // The below columns use a relationship that has already been loaded by the Builder, so you can use any fields/functions, without needing extra DB queries
                Column::make("Total Orders")
                ->label(function ($row, $column) {
                    return $row->orders->sum('price') ?? '0';
                }),
                Column::make("Total - Status 1 or 2")
                ->label(function ($row, $column) {
                    return $row->orders->whereIn('status',['1','2'])->sum('price') ?? '0';
                }),
                Column::make("Total - Status 3 or 4")
                ->label(function ($row, $column) {
                    return $row->orders->whereIn('status',['3','4'])->sum('price') ?? '0';
                }),
                Column::make("Total - Status 5")
                ->label(function ($row, $column) {
                    return $row->orders->where('status', 5)->sum('price') ?? '0';
                }),
                // End of Section
                    
                Column::make("Price", "price")
                    ->sortable()
                    ->format(function ($value, $column, $row) {
                        return $value . ' €';
                    }),
                

                Column::make("Authortiy Score", "as")
                    ->sortable(),

                Column::make("Organic Search Traffic", "traffic")
                    ->sortable(),

                Column::make("Purchased Before")
                    ->sortable()
                    ->eagerLoadRelations()
                    ->label(
                        function ($row, Column $column) {
                            if ($row->orders->where('user_id', auth()->id())->isEmpty()) {
                                return '<p class="text-red-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></p>';
                            } else {
                                return '<p class="flex items-center gap-2 text-green-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                            </svg> Order Id: ' . $row->orders->first()->id . '
                            </p>';
                            }
                        }
                    )
                    ->html()
                ->secondaryHeader(
                    $this->getFilterByKey('purchased_before')
                )
                ->footer(
                    $this->getFilterByKey('purchased_before')
                ),

            Column::make("Country", "country.name")
                ->sortable()
                ->searchable(),

            Column::make("Industry", "industry")
                ->sortable()
                ->searchable()
                ->secondaryHeader(
                    $this->getFilterByKey('industry')
                )
                ->footer(
                    $this->getFilterByKey('industry')
                ),
        ];
    }

    public function filters(): array
    {
        
        return [

            SmartSelectFilter::make('Country', 'cuntry')
                ->config(['displayHtmlName' => true])
                ->options(
                    $this->arrayOfCountries
                )
                ->filter(function (Builder $builder, array $values) {
                    $builder->whereIn('country_id', $values);
                }),

            NumberRangeFilter::make('AS Range', 'as_range')
                ->config(
                    [
                        'minRange' => 100,
                        'maxRange' => $this->maxASRange
                    ]
                )
                ->filter(function (Builder $builder, array $numberRange) {
                    $builder->where('as', '>=', $numberRange['min'])->where('as', '<=', $numberRange['max']);
                }),

            NumberRangeFilter::make('price Range')
                ->options(
                    [
                        'min' => 100,
                        'max' => 1000
                    ]
                )
                ->filter(function (Builder $builder, array $numberRange) {
                    $builder->where('price', '>=', $numberRange['min'])->where('price', '<=', $numberRange['max']);
                }),

            NumberRangeFilter::make('traffic Range')
                ->options(
                    [
                        'min' => 100,
                        'max' => 1000
                    ]
                )
                ->filter(function (Builder $builder, array $numberRange) {
                    $builder->where('traffic', '>=', $numberRange['min'])->where('traffic', '<=', $numberRange['max']);
                }),



            TextFilter::make('INDUSTRY', 'industry')
                ->hiddenFromMenus()
                ->config([
                    'placeholder' => 'Search industry',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('industry', 'like', '%' . $value . '%');
                }),

            DateRangeFilter::make('Order Date','order_date')
                ->config([
                    'ariaDateFormat' => 'F j, Y',
                    'dateFormat' => 'Y-m-d',
                    'earliestDate' => '2020-01-01',
                    'latestDate' => '2023-07-01',
                ])->filter(function(Builder $builder, array $date_range) {
                    // This will load all orders belonging to the link WHERE the date is in range, all other orders are excluded
                    $builder->withWhereHas('orders', function ($builder) use ($date_range) {
                        $builder->whereDate('orders.created_at', '>=', $date_range['minDate'])
                                ->whereDate('orders.created_at', '<=', $date_range['maxDate']);
                    });
                }),

            TextFilter::make('Website', 'site')
                ->hiddenFromMenus()
                ->config([
                    'placeholder' => 'Search Site',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('site', 'like', '%' . $value . '%');
                }),


            SelectFilter::make('Purchased Before')
                ->hiddenFromMenus()
                ->options([
                    '' => 'All',
                    'yes' => 'Yes',
                    'no' => 'No',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'yes') {
                        $builder->whereHas('orders', function ($query) {
                            $query->where('user_id', auth()->id());
                        });
                    } elseif ($value === 'no') {
                        $builder->whereDoesntHave('orders', function ($query) {
                            $query->where('user_id', auth()->id());
                        });
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        // the WHEN will only run IF there is no value in the order_date filter.
        return Link::when(!$this->getAppliedFilterWithValue('order_date'), fn ($query) => 
            $query->withWhereHas('orders')
        )
        ->withSum([ // This will get the sum of ALL orders regardless of the filter value
            'orders as orders_all_time_price'
        ], 'price')        
        ->withCount([
            'orders as total_orders',
        ]);
        // ->groupBy('site');
    }
}
