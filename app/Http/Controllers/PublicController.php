<?php

namespace App\Http\Controllers;

use App\Models\Project;

class PublicController extends Controller
{
    public function projectWall()
    {
        $projects = Project::where('status', 'active')
            ->get()
            ->map(function ($project) {
                $project->collected  = $project->totalContributed();
                $project->percentage = $project->fundingPercentage();
                return $project;
            })
            ->sortByDesc('percentage');

        return view('public.project-wall', compact('projects'));
    }
}