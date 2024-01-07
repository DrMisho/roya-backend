<?php

namespace App\Console\Commands;

use App\Http\Constants\Constant;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Subscription';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunks = Subscription::query()
            ->where('status', Constant::SUBSCRIPTION_STATUS['فعال'])
            ->get()
            ->chunk(900);

        foreach ($chunks as $chunk)
        {
            foreach ($chunk as $subscription)
            {
                $date = Carbon::createFromFormat('Y-m-d', $subscription->end_date);
                if($date->isToday())
                {
                        $subscription->status = Constant::SUBSCRIPTION_STATUS['منتهي'];
                        $subscription->save();
                        $user = $subscription->user;
                        // SubscriptionCanceled::dispatch($user, $subscription);
                }
            }
        }
        $this->info('All subscription checked successfully');
    }
}
