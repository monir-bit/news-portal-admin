@extends('admin.layout.master')
@section('title')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Add New Category</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">News</li>
                <li class="breadcrumb-item active">Breaking</li>
            </ol>
        </div>
    </div>
</div><!-- /.container-fluid -->
@endsection()
@section('body')
<div class="container-fluid">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
        <!-- jquery validation -->
        <div class="card card-primary">
            <div class="card-header">
            <h3 class="card-title">Edit New Breaking</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form id="quickForm" method="POST" action="{{Url('/admin/news/breaking/update')}}">
                @csrf
                <input type="hidden" name="id" value="<?= $category->id; ?>" />
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Headling</label>
                        <input type="text" name="headline" class="form-control" id="exampleInputEmail1" value="<?= $category->headline; ?>">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">News URL</label>
                        <input type="text" name="news_url" class="form-control" id="exampleInputEmail1" value="<?= $category->news_url; ?>" placeholder="News URL">
                    </div>
                    <div class="form-group">
                        <label for="Visible"><input type="checkbox" name="status" <?= $category->status == 1 ? 'checked' : ''; ?> value="1"/> Visible</label>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <input type="submit" class="btn btn-primary" value="Save">
                </div>
            </form>
        </div>
        <!-- /.card -->
        </div>
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-6">

        </div>
        <!--/.col (right) -->
    </div>
    <!-- /.row -->
</div><!-- /.container-fluid -->
@endsection()
