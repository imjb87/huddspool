<?php

namespace App\Support;

class ResponseCacheTagSet
{
    /**
     * @return array<int, string>
     */
    public static function rulesetContent(): array
    {
        return [
            ResponseCacheTags::RULESETS,
            ResponseCacheTags::HISTORY,
            ResponseCacheTags::FIXTURES,
            ResponseCacheTags::TEAMS,
            ResponseCacheTags::VENUES,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function resultContent(): array
    {
        return [
            ResponseCacheTags::HOME,
            ResponseCacheTags::RESULTS,
            ResponseCacheTags::PLAYERS,
            ResponseCacheTags::TEAMS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function frameContent(): array
    {
        return [
            ResponseCacheTags::HOME,
            ResponseCacheTags::RULESETS,
            ResponseCacheTags::HISTORY,
            ResponseCacheTags::FIXTURES,
            ResponseCacheTags::RESULTS,
            ResponseCacheTags::PLAYERS,
            ResponseCacheTags::TEAMS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function sectionContent(): array
    {
        return [
            ResponseCacheTags::RULESETS,
            ResponseCacheTags::HISTORY,
            ResponseCacheTags::PLAYERS,
            ResponseCacheTags::TEAMS,
            ResponseCacheTags::VENUES,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function expulsionContent(): array
    {
        return [
            ResponseCacheTags::RULESETS,
            ResponseCacheTags::HISTORY,
            ResponseCacheTags::TEAMS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function seasonContent(): array
    {
        return [
            ResponseCacheTags::HOME,
            ResponseCacheTags::RULESETS,
            ResponseCacheTags::HISTORY,
            ResponseCacheTags::PLAYERS,
            ResponseCacheTags::TEAMS,
            ResponseCacheTags::VENUES,
            ResponseCacheTags::KNOCKOUTS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function teamContent(): array
    {
        return [
            ResponseCacheTags::RULESETS,
            ResponseCacheTags::HISTORY,
            ResponseCacheTags::PLAYERS,
            ResponseCacheTags::TEAMS,
            ResponseCacheTags::VENUES,
            ResponseCacheTags::KNOCKOUTS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function userContent(): array
    {
        return [
            ResponseCacheTags::RULESETS,
            ResponseCacheTags::HISTORY,
            ResponseCacheTags::RESULTS,
            ResponseCacheTags::PLAYERS,
            ResponseCacheTags::TEAMS,
            ResponseCacheTags::KNOCKOUTS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function venueContent(): array
    {
        return [
            ResponseCacheTags::FIXTURES,
            ResponseCacheTags::RESULTS,
            ResponseCacheTags::TEAMS,
            ResponseCacheTags::VENUES,
            ResponseCacheTags::KNOCKOUTS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function knockoutContent(): array
    {
        return [
            ResponseCacheTags::PLAYERS,
            ResponseCacheTags::TEAMS,
            ResponseCacheTags::KNOCKOUTS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function newsContent(): array
    {
        return [
            ResponseCacheTags::HOME,
            ResponseCacheTags::NEWS,
        ];
    }
}
