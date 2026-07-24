<?php

namespace Modules\SupportTicket\Console;

use Illuminate\Console\Command;
use Modules\SupportTicket\Entities\SupportTicket;

class FlagDelayedSupportTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supportticket:flag-delayed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flip open support tickets past their TAT deadline to Delayed, across every business.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $count = SupportTicket::flagOverdueAsDelayed();
            $this->info("Flagged {$count} support ticket(s) as delayed.");
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
        }
    }
}
