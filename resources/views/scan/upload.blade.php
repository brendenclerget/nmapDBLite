@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Upload Scan</div>
                
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

                    Select the nmap XML file to import into the system below.

                    {!! Form::open(
                        [
                            'route' => 'scan.store', 
                            'files' => true
                        ]
                    ) !!}
                    
                    <div class="input-group mb-3">
                        <div class="custom-file">
                            <input type="file" name="scan" class="custom-file-input" id="nmap_scan">
                            <label class="custom-file-label" for="scan"> Choose File</label>
                        </div>
                    </div>

                    {!! Form::submit('Import Scan', array_merge(['class' => 'btn btn-primary'])) !!}

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#nmap_scan').on('change',function(){
    // Get file name and rip out the fakepath stuff for viewability
    var fileName = $(this).val().replace(/C:\\fakepath\\/, '')

    // Update the label on the form
    $(this).next('.custom-file-label').html(fileName)
})
</script>
@endpush
