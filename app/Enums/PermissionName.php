<?php

namespace App\Enums;

enum PermissionName: string
{
    case AccessAdminPanel = 'access admin panel';
    case ViewPulse = 'view pulse';
    case ImpersonateUsers = 'impersonate users';
    case ManageUsers = 'manage users';
    case ManageTeams = 'manage teams';
    case ManageVenues = 'manage venues';
    case ManageSeasons = 'manage seasons';
    case ManageSections = 'manage sections';
    case ManageFixtures = 'manage fixtures';
    case ManageRulesets = 'manage rulesets';
    case ManageKnockouts = 'manage knockouts';
    case ManageNews = 'manage news';
    case ManagePages = 'manage pages';
    case ManageSupportTickets = 'manage support tickets';
    case SubmitLeagueResults = 'submit league results';
    case SubmitKnockoutResults = 'submit knockout results';
}
