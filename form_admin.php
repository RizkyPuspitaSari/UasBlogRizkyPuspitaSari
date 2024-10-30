
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Navigation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h2 class="card-title mb-4 text-center">Dashboard</h2>
            <form class="p-3">
                <div class="mb-3">
                    <label for="navigation" class="form-label"><strong>Navigate to:</strong></label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="fas fa-compass"></i></span>
                        <select class="form-select" id="navigation" onchange="navigateToPage()">
                            <option value="" selected>Select a page</option>
                            <option value="article_crud.php"><i class="fas fa-file-alt"></i> Article Management</option>
                            <option value="kategori.php"><i class="fas fa-folder"></i> Category Management</option>
                        </select>
                        <button type="button" class="btn btn-primary" onclick="navigateToPage()">Go</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<script>
    function navigateToPage() {
        var select = document.getElementById('navigation');
        var selectedValue = select.value;
        if (selectedValue) {
            window.location.href = selectedValue;
        }
    }
</script>

</body>
</html>
