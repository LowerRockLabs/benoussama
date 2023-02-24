<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Link;
use App\Models\Order;
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


class LinkTable extends DataTableComponent
{

    use LivewireAlert;

    protected $model = Link::class;

    public array $arrayOfCountries = [];

    protected $listeners = [
        'createOrder'
    ];

    public function createOrder(Link $link)
    {
        if (!$link->is_on_order) {
            $this->alert('error', "You already purchased this link!");
            return;
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'link_id' => $link->id,
            'price' =>  $link->price + 150,
        ]);

        $this->alert('success', "Order {$order->id} created Successfully!");
    }

    public function mount()
    {
        $this->arrayOfCountries = Link::select('cuntry')->distinct()->pluck('cuntry')->toArray();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setSingleSortingDisabled()
            ->setOfflineIndicatorEnabled()
            ->setQueryStringDisabled()
            ->setEagerLoadAllRelationsEnabled();
        $this->setFilterLayoutSlideDown();
    }

    public function builder(): Builder
    {
        return Link::query()
            ->with(['orders']);
        // ->groupBy('site');
    }

    public function columns(): array
    {
        return [
            Column::make("Cart")
                ->label(
                    function ($row, Column $column) {
                        if ($row->orders->where('user_id', auth()->id())->isEmpty()) {
                            return '<svg wire:click="$emit(\'createOrder\', {link:' . $row->id . '})" class="w-5 h-5 text-blue-600 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                          </svg>';
                        } else {
                            return '<svg class="w-5 h-5 text-gray-600 cursor-not-allowed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                          </svg>';
                        }
                    }
                )
                ->html(),

            Column::make("#", "id")
                ->sortable()
                ->searchable(),

            Column::make("Site", "site")
                ->sortable()
                ->searchable(),

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
                ->html(),

            Column::make("Country", "cuntry")
                ->sortable()
                ->searchable(),

            Column::make("Industry", "industry")
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [
            MultiSelectDropdownFilter::make('Country', 'cuntry')
                ->options(
                    $this->arrayOfCountries
                )
                ->filter(function (Builder $builder, array  $value) {
                    // get the values as text from the arrayOfCountries array
                    $values = array_map(function ($value) {
                        return $this->arrayOfCountries[$value];
                    }, $value);
                    $builder->whereIn('cuntry', $values);
                }),

            //AS
            NumberFilter::make('AS bigger than')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('as', '>=', (int)$value);
                }),
            NumberFilter::make('AS Less than')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('as', '<=', (int)$value);
                }),

            //Price
            NumberFilter::make('Price bigger than')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('price', '>=', (int)$value);
                }),
            NumberFilter::make('Price Less than')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('price', '<=', (int)$value);
                }),

            NumberFilter::make('ID')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('id', (int)$value);
                }),

            //Traffic
            NumberFilter::make('Traffic bigger than')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('traffic', '>=', (int)$value);
                }),
            NumberFilter::make('Traffic Less than')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('traffic', '<=', (int)$value);
                }),

            TextFilter::make('Website')
                ->config([
                    'placeholder' => 'Search Site',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('site', 'like', '%' . $value . '%');
                }),

            TextFilter::make('INDUSTRY')
                ->config([
                    'placeholder' => 'Search industry',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('industry', 'like', '%' . $value . '%');
                }),

            // add a date from and a date to filter
            DateFilter::make('Date From', 'date_from')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('created_at', '>=', $value);
                }),
            DateFilter::make('Date To', 'date_to')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('created_at', '<=', $value);
                }),

            SelectFilter::make('Purchased Before')
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
                })




        ];
    }
}
