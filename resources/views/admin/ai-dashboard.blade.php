@extends('layouts.app')

@section('title', 'AI Dashboard')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">AI Usage Dashboard</h1>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Provider Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @foreach($stats as $stat)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-700 text-lg capitalize mb-4">{{ $stat->provider }}</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-gray-500 text-sm">Total Calls</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stat->total_calls) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Success Rate</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $stat->total_calls > 0 ? number_format(($stat->successful / $stat->total_calls) * 100, 1) : 0 }}%
                    </p>
                </div>
                <div class="mt-2">
                    <p class="text-gray-500 text-sm">Input Tokens</p>
                    <p class="text-xl font-semibold text-gray-800">{{ number_format($stat->total_input_tokens) }}</p>
                </div>
                <div class="mt-2">
                    <p class="text-gray-500 text-sm">Output Tokens</p>
                    <p class="text-xl font-semibold text-gray-800">{{ number_format($stat->total_output_tokens) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Recent Analyses --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Book Analyses</h2>

            @if($analyses->isEmpty())
                <p class="text-gray-400 text-sm">No analyses yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Book</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Sentiment</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Score</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Reviews</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Analyzed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($analyses as $analysis)
                            <tr>
                                <td class="py-2 text-sm font-medium text-gray-800">
                                    <a href="{{ route('books.show', $analysis->book) }}" class="hover:underline">
                                        {{ Str::limit($analysis->book->title, 30) }}
                                    </a>
                                </td>
                                <td class="py-2 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($analysis->overall_sentiment === 'positive') bg-green-100 text-green-800
                                        @elseif($analysis->overall_sentiment === 'negative') bg-red-100 text-red-800
                                        @elseif($analysis->overall_sentiment === 'mixed') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ ucfirst($analysis->overall_sentiment) }}
                                    </span>
                                </td>
                                <td class="py-2 text-sm font-medium text-green-600">{{ number_format($analysis->sentiment_score * 100, 0) }}%</td>
                                <td class="py-2 text-sm">{{ $analysis->reviews_analyzed }}</td>
                                <td class="py-2 text-sm text-gray-500">{{ $analysis->updated_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Recent Usage Logs --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Usage Logs</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Provider</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Feature</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Model</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Status</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentLogs as $log)
                        <tr>
                            <td class="py-2 text-sm capitalize">{{ $log->provider }}</td>
                            <td class="py-2 text-sm">{{ $log->feature }}</td>
                            <td class="py-2 text-sm text-gray-500">{{ $log->model_used }}</td>
                            <td class="py-2 text-sm">
                                @if($log->success)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Success</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800" title="{{ $log->error_message }}">Failed</span>
                                @endif
                            </td>
                            <td class="py-2 text-sm text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection