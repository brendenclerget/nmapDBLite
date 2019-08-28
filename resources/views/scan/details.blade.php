@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-header">Scan Details</div>
                <div class="card-body">
                    <p class="lead">
                        Scan IP Address: {{ $scan->ip->address }}
                    </p>
                    Started: {{ $scan->startDatePretty() }}<br>
                    Finished: {{ $scan->endDatePretty() }}<br>
                    State: {{ $scan->state }}<br>
                    Reason (TTL): {{ $scan->reason }} ({{ $scan->reason_ttl }})
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12 mt-3 mt-md-0">
            <div class="card">
                <div class="card-header">Host Info</div>
                <div class="card-body">
                    <p>
                        Host: {{ $scan->host }}
                    </p>
                    Host Type: {{ $scan->host_type }}<br>
                    Smoothed RTT: {{ $scan->srtt }}<br>
                    Timeout: {{ $scan->to }}
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center" style="margin-top:25px;">
            <div class="col-md-12">
            <div class="card">
                <div class="card-header">Port Info</div>
                <div class="card-body">
                    <div class="table-responsive">
                        {{ $dt->table(['class' => 'table table-hover']) }}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{ $dt->scripts() }}
@endpush
