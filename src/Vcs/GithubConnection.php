<?php


namespace App\Vcs;

use App\Dto\VcsProjectInfo;
use App\Entity\VcsConnectionInfo;
use Github\Client;

class GithubConnection implements VcsConnectionInterface
{
    private $client;
    private $info;

    public function __construct(VcsConnectionInfo $connectionInfo, Client $client)
    {
        $client->authenticate($connectionInfo->getToken(), null, Client::AUTH_HTTP_TOKEN);
        $this->info = $connectionInfo;
        $this->client = $client;
    }

    public function listProjects(?string $organization = null, ?string $project = null, int $page = 1, int $perPage = 20): array
    {
        //todo implement search
        if ($organization != '') {
            $projects = $this->client->organization()->repositories($organization, 'all', $page);
        } else {
            $projects = $this->client->repositories()->all();
        }

        return array_map(function (array $githubProject) {
            return new VcsProjectInfo($githubProject['owner']['login'], $githubProject['name'], $this->info->getId());
        }, $projects);
    }

    public function fetchFile(string $organization, string $project, string $path)
    {
        return base64_decode(
            $this->client->repositories()->contents()->show($organization, $project, $path)["content"]);
    }
}
