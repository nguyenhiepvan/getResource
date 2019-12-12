@if ($crud->hasAccess('create'))
<a href="{{ route('crud.test.generateForm') }}" class="btn btn-info" title="Generate test by link"><i class="fa fa-download"></i>Generate test</a>
@endif