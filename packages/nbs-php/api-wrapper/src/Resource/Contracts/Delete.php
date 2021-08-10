<?php

namespace NbsPhp\ApiWrapper\Resource\Contracts;

use GuzzleHttp\Client as GuzzleClient;

interface Delete
{
    /**
     * Call the "Delete" Route for this Resource.
     * @param string|int $id
     * @param array $options
     * @param GuzzleClient|null $client
     * @return void
     */
    public static function delete($id, array $options = [], GuzzleClient $client = null): void;

    /**
     * Call the "Delete" Route for this ApiResource instance.
     * @param array $options
     * @param GuzzleClient|null $client
     * @return void
     */
    public function destroy(array $options = [], GuzzleClient $client = null): void;
}
