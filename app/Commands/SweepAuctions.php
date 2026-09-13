<?php

namespace App\Commands;

use App\Services\AuctionService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Run periodically (e.g. via cron every minute: `php spark auctions:sweep`)
 * to flip upcoming auctions live and close out auctions past their end_time.
 * The web layer also runs this opportunistically on page loads so demos
 * work correctly even without a cron worker configured.
 */
class SweepAuctions extends BaseCommand
{
    protected $group       = 'Auctions';
    protected $name        = 'auctions:sweep';
    protected $description = 'Starts due auctions and closes ended ones, handing winners off to the POS.';

    public function run(array $params)
    {
        $result = (new AuctionService())->runSweep();

        CLI::write("Started: {$result['started']}  Closed: {$result['closed']}", 'green');
    }
}
