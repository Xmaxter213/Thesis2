<!DOCTYPE html>
<html>
<head>
    <title>Show Input Field</title>
    <script type="text/javascript">
        function toggleInputField() {
            var isChecked = document.getElementById('radioBtn').checked;
            var inputField = document.getElementById('inputField');
            if (isChecked) {
                inputField.style.display = 'block';
            } else {
                inputField.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <form>
        <input type="radio" id="radioBtn" name="showInput" onclick="toggleInputField()">
        <label for="radioBtn">Show Input Field</label>
        
        <div id="inputField" style="display:none;">
            <input type="text" name="textInput">
        </div>
    </form>
</body>
</html>
