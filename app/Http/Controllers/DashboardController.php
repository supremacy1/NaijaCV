<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\JobApplication;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $recentCvs = Cv::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $recentJobs = JobApplication::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $totalCvs = Cv::where('user_id', $userId)->count();

        $totalJobs = JobApplication::where('user_id', $userId)->count();

        $interviews = JobApplication::where('user_id', $userId)
            ->where('status', 'interview')
            ->count();

        $offers = JobApplication::where('user_id', $userId)
            ->where('status', 'offer')
            ->count();

        $rejectedCount = JobApplication::where('user_id', $userId)
            ->where('status', 'rejected')
            ->count();

        return view('dashboard', compact(
            'recentCvs',
            'recentJobs',
            'totalCvs',
            'totalJobs',
            'interviews',
            'offers',
            'rejectedCount'
        ));
    }
}
