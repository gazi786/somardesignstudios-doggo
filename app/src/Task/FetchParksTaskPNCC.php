<?php

namespace Doggo\Task;

use Doggo\Model\Park;
use GuzzleHttp\Client;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

class FetchParksTaskPNCC extends BuildTask
{
    private static $api_url;

    private static $api_title;

    public function run($request)
    {
        $client = new Client();

        $response = $client->request(
            'GET',
            $this->config()->get('api_url'),
            ['User-Agent' => 'Doggo (www.somar.co.nz)']
        );

        if ($response->getStatusCode() !== 200) {
            user_error('Could not access ' . $this->config()->get('api_url'));
            exit;
        }

        /*
         * Mark existing records as IsToPurge.
         *
         * As we encounter each record in the API source, we unset this.
         * Once done, any still set are deleted.
         */
        $existingParks = Park::get()->filter('Provider', $this->config()->get('api_title'));
        foreach ($existingParks as $park) {
            $park->IsToPurge = true;
            $park->write();
        }

        $data = json_decode($response->getBody());

        $parks = $data->features;
        foreach ($parks as $park) {
            $parkObject = Park::get()->filter([
                'Provider' => $this->config()->get('api_title'),
                'ProviderCode' => $park->properties->OBJECTID,
            ])->first();
            $status = 'changed';

            if (!$parkObject) {
                $status = 'created';
                $parkObject = Park::create();
                $parkObject->Provider = $this->config()->get('api_title');
                $parkObject->ProviderCode = $park->properties->OBJECTID;
            }

             if ($park->properties->RESERVE_NAME === null || $park->properties->RESERVE_NAME === ' ') {
                continue;
            }

            if ($park->properties->DESCRIPTION === 'Dog on leash area' || $park->properties->DESCRIPTION === 'Leased area') {
                $leash = 'On-leash';
            } elseif ($park->properties->DESCRIPTION === 'Dogs prohibited') {
                continue;
            } else {
                $leash = 'Off-leash';
            }

            if ($park->geometry->type === 'MultiPolygon') {
                $geometry = $park->geometry->coordinates[0][0][0];
            }
            else {
                $geometry = $park->geometry->coordinates[0][0];
            }

            $parkObject->update([
                'IsToPurge' => false,
                'Title' => $park->properties->RESERVE_NAME,
                'Latitude' => $geometry[0],
                'Longitude' => $geometry[1],
                'Notes' => $park->properties->DESCRIPTION,
                'GeoJson' => json_encode($park),
                'FeatureOnOffLeash' => $leash,
            ]);

            $parkObject->write();

            DB::alteration_message('[' . $parkObject->ProviderCode . '] ' . $parkObject->Title, $status);
        }

        $existingParks = Park::get()->filter([
            'Provider' => $this->config()->get('api_title'),
            'IsToPurge' => true,
        ]);
        foreach ($existingParks as $park) {
            DB::alteration_message('[' . $parkObject->ProviderCode . '] ' . $parkObject->Title, 'deleted');
            $park->delete();
        }
    }
}
