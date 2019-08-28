@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Browse Scans</div>
                
                <div class="card-body">
                    
                    <!-- Show Errors if they exist from file input validation -->
                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    

                    <p class="lead">
                        View all scans for {{ $ip_record->address }}
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
