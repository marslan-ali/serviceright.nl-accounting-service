<?php

namespace App\Console\Commands;

use App\Models\Common\Contact;
use Illuminate\Console\Command;
use Moneybird;

class ImportMoneyBirdContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MoneyBird:import-contact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all the contacts from MoneyBird';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit',0);
        $this->info("Importing Contacts from Money Bird .....");

        //@todo need to discuss what company id will be
        session(['company_id' => 1]);
        $mbContacts = Moneybird::contact()->getAll();

        $bar = $this->getOutput()->createProgressBar(count($mbContacts));
        $bar->setFormat('verbose');

        $bar->start();


        foreach ($mbContacts as $mbContact) {

            Contact::updateOrCreate(
                ["reference" => $mbContact->id],
                [
                    "company_id" => 1,
                    "user_id" => null,
                    "type" => "moneybird_contacts",
                    "name" => $mbContact->firstname . " " . $mbContact->lastname,
                    "email" => $mbContact->email,
                    "tax_number" => $mbContact->tax_number,
                    "phone" => $mbContact->phone,
                    "address" => $mbContact->address1 . $mbContact->address2,
                    "website" => "",
                    "currency_code" => "USD",
                    "enabled" => 1,
                    "created_at" => $mbContact->created_at,
                    "updated_at" => $mbContact->updated_at,
                ]
            );
            $bar->advance();

        }
        $bar->finish();
        $this->info(' ');
        $this->info("Import completed successfully");
    }

}
