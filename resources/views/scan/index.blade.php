@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Browse Scans</div>
                
                <div class="card-body">
                    
                    <!-- Show success message if redirected after file upload -->
                    @if (session()->has('message'))
                        <div class="alert alert-info">
                            {{ session('message') }}
                        </div>
                    @endif

                    

                    <p class="lead">
                        Browse scans by IP Address. Click the eyeball on an IP address to view all scans associated with it.
                    </p>
                    
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
