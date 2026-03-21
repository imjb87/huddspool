<?php

namespace App\Support;

use Illuminate\Http\Request;

class NavigationViewState
{
    /**
     * @return array{
     *     knockoutNavIsActive: bool,
     *     historyNavIsActive: bool,
     *     handbookNavIsActive: bool
     * }
     */
    public static function fromRequest(Request $request): array
    {
        $currentPage = $request->route('page');

        return [
            'knockoutNavIsActive' => $request->routeIs('knockout.*')
                || ($request->routeIs('page.show') && $currentPage === 'knockout-dates'),
            'historyNavIsActive' => $request->routeIs('history.*'),
            'handbookNavIsActive' => $request->routeIs('page.show') && $currentPage === 'handbook',
        ];
    }
}
