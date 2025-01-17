<?php

namespace App\Services;

use GuzzleHttp\Client;

class JiraService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://cityinnovate.atlassian.net/',
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(env('JIRA_USERNAME') . ':' . env('JIRA_API_TOKEN')),
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Fetch unreleased versions (releases) for the project.
     */
    public function getUnreleasedReleases()
    {

        // Fetch versions from the project
        $response = $this->client->get("/rest/api/3/project/AA/versions");
        $versions = json_decode($response->getBody(), true);

        // Filter for unreleased versions
        return array_filter($versions, fn($version) => !$version['released']);
    }

    public function getReleaseDetails($releaseId)
    {
        $response = $this->client->get("/rest/api/3/version/{$releaseId}");

        return json_decode($response->getBody(), true);
    }

    /**
     * Fetch Critical and P0 epics in a release.
     */
    public function getEpicsInRelease($releaseKey, $priorities = ['Critical', 'P0'])
    {
        $priorityFilter = implode(',', $priorities);

        $response = $this->client->get("/rest/api/3/search", [
            'query' => [
                'jql' => "fixVersion = '{$releaseKey}' AND priority IN ({$priorityFilter}) AND issuetype = Epic ORDER BY summary ASC",
                'fields' => 'summary,priority,status',
            ],
        ]);

        return json_decode($response->getBody(), true)['issues'];
    }

    /**
     * Fetch all epics in a release.
     */
    public function getAllEpicsInRelease($releaseKey)
    {

        $response = $this->client->get("/rest/api/3/search", [
            'query' => [
                'jql' => "fixVersion = '{$releaseKey}' AND issuetype = Epic ORDER BY summary ASC",
                'fields' => 'summary,priority,status',
            ],
        ]);

        return json_decode($response->getBody(), true)['issues'];
    }

    /**
     * Fetch all issues in a release.
     */
    public function getIssuesInRelease($releaseKey)
    {
        $response = $this->client->get("/rest/api/3/search", [
            'query' => [
                'jql' => "fixVersion = '{$releaseKey}' ORDER BY status ASC",
                'fields' => 'status',
                'maxResults' => 1000,
            ],
        ]);

        return json_decode($response->getBody(), true)['issues'];
    }

    /**
     * Fetch details of a specific epic by its key.
     */
    public function getEpic($epicKey)
    {
        $response = $this->client->get("/rest/api/3/issue/{$epicKey}", [
            'query' => [
                'fields' => 'summary,priority,description',
                'expand' => 'renderedFields',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Fetch child issues for a specific epic.
     */
    public function getChildIssues($epicKey)
    {
        $response = $this->client->get("/rest/api/3/search", [
            'query' => [
                'jql' => "'Epic Link' = '{$epicKey}' ORDER BY summary ASC",
                'fields' => 'summary,description,status',
            ],
        ]);

        return json_decode($response->getBody(), true)['issues'];
    }
}
