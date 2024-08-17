<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>landing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <form action="{{ route('insert.settings.field') }}" method="POST" >
        @csrf
        <div class="container mt-5" >
            <div class="row" >
                <div class="col-6" >
                    <div class="form-group" >
                        <input type="text" class="form-control" name="type" placeholder="type" >
                    </div>
                </div>
                <div class="col-6" >
                    <div class="form-group" >
                        <input type="text" class="form-control" name="message" placeholder="message" >
                    </div>
                </div>
                <div class="col-6 mt-3" >
                    <div class="form-group" >
                        <button type="submit" class="btn btn-success">add new fileds</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @include('sweetalert::alert')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.2/js/bootstrap.min.js"></script>
</body>
</html>
