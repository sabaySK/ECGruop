@extends('layouts.admin');
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Brands</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.brands')}}">
                        <div class="text-tiny">Brands</div>
                    </a>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name"
                                tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{route('admin.brand_add')}}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        @if (@Session::has('status'))
                            <p class="alert alert-success">{{Session::get('status')}}</p>
                        @endif
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img src="{{asset('uploads/brands')}}/{{$brand->image}}" alt="{{$brand->name}}" class="image"></td>
                                    <td>{{$brand->name}}</td>
                                    <td>{{$brand->slug}}</td>
                                    <td>
                                        <div class="list-icon-function">
                                            <a href="{{route('admin.brand_edit',['id'=>$brand->id])}}">
                                                <div class="item edit">
                                                    <i class="icon-edit-3"></i>
                                                </div>
                                            </a>
                                            <form action="{{route('admin.brand_delete', ['id'=>$brand->id])}}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="item text-danger delete">
                                                    <i class="icon-trash-2"></i>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{$brands->links('pagination::bootstrap-5')}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(function(){
            $('.delete').on('click', function(e){
                e.preventDefault();
                var form = $(this).closest('form');
                swal({
                    title: 'Do you want to delete this ?',
                    text: 'When delete will not recover!',
                    type: 'warning',
                    buttons: ['No', 'Yes'],
                    confirmButtonColor: '#dc3545'
                }).then(function(result){
                    if(result){
                        form.submit();
                    }
                })
            })
        })
    </script>
@endpush

