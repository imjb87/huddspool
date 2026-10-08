<?php

namespace App\Support;

use Illuminate\Http\Request;

class NavigationViewState
{
    /**
     * @return array{
     *     knockoutNavIsActive: bool,
     *     historyNavIsActive: bool,
     *     officialNavIsActive: bool
     * }
     */
    public static function fromRequest(Request $request): array
    {
        $currentPage = $request->route('page');

        return [
            'knockoutNavIsActive' => $request->routeIs('knockout.*')
                || ($request->routeIs('page.show') && $currentPage === 'knockout-dates'),
            'historyNavIsActive' => $request->routeIs('history.*'),
            'officialNavIsActive' => $request->routeIs('downloads.index')
                || ($request->routeIs('page.show') && $currentPage === 'handbook'),
        ];
    }
}
