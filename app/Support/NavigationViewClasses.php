<?php

namespace App\Support;

class NavigationViewClasses
{
    /**
     * @return array{
     *     mobileDrawerPanelClasses: string,
     *     mobileDrawerPanelContentClasses: string,
     *     mobileDrawerListClasses: string,
     *     mobileDrawerBackButtonClasses: string,
     *     mobileDrawerBackLabelClasses: string,
     *     mobileDrawerLinkClasses: string,
     *     mobileDrawerTextLinkClasses: string
     * }
     */
    public static function defaults(): array
    {
        return [
            'mobileDrawerPanelClasses' => 'absolute inset-0 overflow-y-auto bg-white px-4 py-4 dark:bg-zinc-900',
            'mobileDrawerPanelContentClasses' => 'space-y-5',
            'mobileDrawerListClasses' => 'space-y-1',
            'mobileDrawerBackButtonClasses' => 'block w-full border-b border-gray-200 pb-3 text-left dark:border-gray-800',
            'mobileDrawerBackLabelClasses' => 'flex items-center gap-3 py-3 text-base font-semibold leading-7 text-gray-900 transition hover:text-gray-700 dark:text-gray-100 dark:hover:text-gray-200',
            'mobileDrawerLinkClasses' => 'flex w-full items-center justify-between rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 transition hover:text-gray-700 dark:text-gray-100 dark:hover:text-gray-200',
            'mobileDrawerTextLinkClasses' => 'block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 transition hover:text-gray-700 dark:text-gray-100 dark:hover:text-gray-200',
        ];
    }
}
