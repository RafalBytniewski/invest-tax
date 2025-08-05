<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Builder::macro('search', function ($fields, $string) {
            if (!$string) {
                return $this;
            }

            return $this->where(function ($query) use ($fields, $string) {
                foreach ((array) $fields as $field) {
                    if (str_contains($field, '.')) {
                        $parts = explode('.', $field);
                        $column = array_pop($parts); // ostatnia część = nazwa kolumny
                        $relation = implode('.', $parts); // wszystko przed ostatnią = relacja (nawet zagnieżdżona)

                        $query->orWhereHas($relation, function ($q) use ($column, $string) {
                            $q->where($column, 'like', '%' . $string . '%');
                        });
                    } else {
                        // Zwykłe pole w tabeli
                        $query->orWhere($field, 'like', '%' . $string . '%');
                    }
                }
            });
        });
    }
}
