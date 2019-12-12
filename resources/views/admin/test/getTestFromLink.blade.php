@extends('backpack::layout')
@section('header')
<section class="content-header">
	<h1>
		<span class="text-capitalize">Test</span>
		<small>Generate test.</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
		<li><a href="{{ route('crud.test.index') }}" class="text-capitalize">Back to all test</a></li>
		<li class="active">generate</li>
	</ol>
</section>
@endsection
@section('content')
<a href="{{ route('crud.test.index') }}" class="hidden-print"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>tests</span></a>
<div class="row m-t-20">
	<div class="col-md-8 col-md-offset-2">
		<div class="card card-primary card-outline card-tabs">
			<div class="card-header p-0 pt-1 border-bottom-0">
				<ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
					<li class="nav-item active">
						<a class="nav-link active" id="get-one-by-one" data-toggle="pill" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Get one by one</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="get-many" data-toggle="pill" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Get many</a>
					</li>
				</ul>
			</div>
			<div class="card-body">
				<div class="tab-content" id="custom-tabs-two-tabContent">
					<div class="tab-pane fade active in" id="tab1" role="tabpanel" aria-labelledby="get-one-by-one">
						<form method="post"
						action="{{ route('crud.test.genTest') }}"
						>
						{!! csrf_field() !!}
						<div class="col-md-12">
							<div class="row display-flex-wrap">
								<!-- load the view from the application if it exists, otherwise load the one in the package -->
								<div class="box col-md-12 padding-10 p-t-20">
									<!-- load the view from type and view_namespace attribute if set -->
									<!-- text input -->
									<div class="form-group col-xs-12">
										<label>Link</label>
										<input required type="text" name="link" value="" class="form-control" placeholder="https://example.com">
									</div>
								</div>
							</div>
							<div class="">
								<button type="submit" class="btn btn-success">
									<span class="fa fa-download" role="presentation" aria-hidden="true"></span> &nbsp;
									<span>Generate</span>
								</button>
							</div><!-- /.box-footer-->
						</div>
					</form>
				</div>
				<div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="get-many">
					<form method="post"
					action="{{ route('crud.test.genTests') }}"
					>
					{!! csrf_field() !!}
					<div class="col-md-12">
						<div class="row display-flex-wrap">
							<!-- load the view from the application if it exists, otherwise load the one in the package -->
							<div class="box col-md-12 padding-10 p-t-20">
								<!-- load the view from type and view_namespace attribute if set -->
								<!-- text input -->
								<div class="form-group col-xs-12">
									<label>Link</label>
									<input required type="text" name="link" value="" class="form-control" placeholder="https://example.com">
								</div>
							</div>
						</div>
						<div class="">
							<button type="submit" class="btn btn-success">
								<span class="fa fa-download" role="presentation" aria-hidden="true"></span> &nbsp;
								<span>Generate</span>
							</button>
						</div><!-- /.box-footer-->
					</div><!-- /.box -->
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /.card -->
</div>
</div>
@endsection