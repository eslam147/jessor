<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>landing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <form action="{{ route('domain.store') }}" method="POST" >
        @csrf
        <div class="container mt-5" >
            <div class="row" >
                <div class="col-6" >
                    <div class="form-group" >
                        <label> Subdomain </label>
                        <input type="text" name="subdomain" class="form-control mt-3" placeholder="example : sampledomain.com">
                    </div>
                </div>
                <div class="col-6" >
                    <div class="form-group" >
                        <label> Domain</label>
                        <input type="text" name="domain" class="form-control mt-3" placeholder="example : sampledomain.com">
                    </div>
                </div>
                <div class="col-12 mt-5" >
                    <button type="submit" style="width: 100%" class="btn btn-success" >Submit</button>
                </div>
            </div>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.2/js/bootstrap.min.js"></script>
</body>
</html>
