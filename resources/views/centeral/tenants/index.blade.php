<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>landing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <form action="{{ route('tenants.store') }}" method="POST" >
        @csrf
        <div class="container mt-5" >
            <div class="row" >
                <div class="col-12" >
                    @csrf
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
                <div class="mt-5" >{{ $migrateOutput }}</div>
            </div>
        </div>
    </form>
    @include('sweetalert::alert')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.2/js/bootstrap.min.js"></script>
</body>
</html>
