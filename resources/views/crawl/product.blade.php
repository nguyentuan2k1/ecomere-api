<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Bootstrap 4 Form</title>
</head>
<body>

<div class="container mt-5">
    <form method="post">
        @csrf
        <div class="form-group">
            <label for="urlField">URL:</label>
            <input type="url" name="url" class="form-control" id="urlField" placeholder="Enter URL" required>
        </div>

        <div class="form-group">
            <label for="textField1">Name:</label>
            <input type="text" name="regex-name" class="form-control" id="textField1" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField1">Name Position:</label>
            <input type="text" name="regex-name-position" class="form-control" id="textField1" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField2">Price</label>
            <input type="text" name="regex-price" class="form-control" id="textField2" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField2">Price Position</label>
            <input type="text" name="regex-price-position" class="form-control" id="textField2" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField3">Image</label>
            <input type="text" name="regex-image" class="form-control" id="textField3" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField3">Image Position</label>
            <input type="text" name="regex-image-position" class="form-control" id="textField3" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField4">description</label>
            <input type="text" name="regex-desc" class="form-control" id="textField4" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField4">description</label>
            <input type="text" name="regex-desc-position" class="form-control" id="textField4" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField4">Regex Link Product</label>
            <input type="text" name="regex-link-product" class="form-control" id="textField4" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField4">Regex Link Product</label>
            <input type="text" name="regex-link-product-position" class="form-control" id="textField4" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField4">category_id</label>
            <input type="text" name="regex-category" class="form-control" id="textField4" placeholder="" required>
        </div>

        <div class="form-group">
            <label for="textField4">Brand</label>
            <input type="text" name="brand-name" class="form-control" id="textField4" placeholder="" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
