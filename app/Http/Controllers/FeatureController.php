<?php

namespace App\Http\Controllers;

use App\Services\JiraService;

use Illuminate\Http\Request;

class FeatureController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    public function index()
    {
        $features = $this->jira->getProjectFeatures();

        return view('features.index', compact('features'));
    }

    public function show($id)
    {
        $data = $this->jira->getEpicsByFeature($id);

        return view('features.show', [
            'counts' => $data['counts'],
            'feature' => $data['component'],
            'groupedItems' => $data['groupedItems'],
            'unassignedItems' => $data['unassignedItems'],
            'shippedItems' => $data['shippedItems'],
        ]);
    }
}
